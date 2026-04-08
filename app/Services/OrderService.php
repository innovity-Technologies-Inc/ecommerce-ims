<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * Get all orders with search and sorting.
     */
    public function getAllOrders(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Order::with('user');

        $filters = [];

        if (! empty($params['order_status'])) {
            $filters['order_status'] = $params['order_status'];
        }

        if (! empty($params['payment_method'])) {
            $filters['payment_method'] = $params['payment_method'];
        }

        if (! empty($params['payment_status'])) {
            $filters['payment_status'] = $params['payment_status'];
        }

        if (! empty($params['date_from'])) {
            $filters['created_at>='] = $params['date_from'].' 00:00:00';
        }

        if (! empty($params['date_to'])) {
            $filters['created_at<='] = $params['date_to'].' 23:59:59';
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['order_id', 'name', 'email', 'mobile'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('order_id', 'asc');
                break;
            case 'z-a':
                $query->orderBy('order_id', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Update order status and send notification if requested.
     */
    public function updateOrderStatus(Order $order, string $status, bool $notify = false, ?string $reason = null, array $itemData = []): bool
    {
        $status = trim($status);

        return DB::transaction(function () use ($order, $status, $notify, $reason, $itemData) {
            $oldStatus = $order->order_status;

            // Finality check: If current status is Delivered, Cancelled or Rejected, do not allow changes
            if (in_array($oldStatus, ['Delivered', 'Cancelled', 'Rejected'])) {
                throw new \Exception("Order status cannot be changed once it is {$oldStatus}.");
            }

            // Historical check: Prevent moving to a status that has already been used
            if ($order->statusLogs()->where('status', $status)->exists()) {
                throw new \Exception("Order has already been in the '{$status}' status. Status cannot be reused.");
            }

            // --- INVENTORY INTEGRATION LOGIC ---

            if ($status === 'Shipped') {
                if (empty($itemData)) {
                    throw new \Exception('Inventory allocation data is required for Shipped status.');
                }

                $orderTotalCost = 0;

                foreach ($order->orderItems as $item) {
                    $itemAllocations = $itemData[$item->id]['allocations'] ?? [];
                    if (empty($itemAllocations)) {
                        throw new \Exception("Inventory allocation is required for {$item->product_name}.");
                    }

                    // Validate total allocated quantity
                    $totalAllocatedQty = array_sum(array_column($itemAllocations, 'quantity'));
                    if ($totalAllocatedQty != $item->quantity) {
                        throw new \Exception("Total allocated quantity ({$totalAllocatedQty}) does not match ordered quantity ({$item->quantity}) for {$item->product_name}.");
                    }

                    $itemTotalCost = 0;

                    // Clear previous allocations if any (re-shipped scenario, though unlikely)
                    $item->orderedProductBatches()->delete();

                    foreach ($itemAllocations as $alloc) {
                        // Fetch unit cost from BatchProduct
                        $batchProduct = \App\Models\BatchProduct::where([
                            'batch_id' => $alloc['batch_id'],
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                        ])->first();

                        if (! $batchProduct) {
                            throw new \Exception("Batch #{$alloc['batch_id']} does not contain {$item->product_name}.");
                        }

                        $unitCost = $batchProduct->unit_cost;
                        $subtotalCost = $alloc['quantity'] * $unitCost;
                        $itemTotalCost += $subtotalCost;

                        $orderedBatch = \App\Models\OrderedProductBatch::create([
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                            'batch_id' => $alloc['batch_id'],
                            'quantity' => $alloc['quantity'],
                            'unit_cost' => $unitCost,
                            'subtotal_cost' => $subtotalCost,
                        ]);

                        // Update Serials if provided for this batch
                        if (! empty($alloc['serials'])) {
                            if (count($alloc['serials']) != $alloc['quantity']) {
                                throw new \Exception("Selected serials count must match allocated quantity ({$alloc['quantity']}) for Batch #{$alloc['batch_id']}.");
                            }

                            // Mark previous serials as in-stock if any
                            \App\Models\BatchSerial::where('order_item_id', $item->id)
                                ->where('batch_id', $alloc['batch_id'])
                                ->update([
                                    'order_item_id' => null,
                                    'stock_status' => 'in_stock',
                                ]);

                            // Assign new serials and mark as sold (since stock is being deducted now)
                            \App\Models\BatchSerial::whereIn('id', $alloc['serials'])->update([
                                'order_item_id' => $item->id,
                                'stock_status' => 'sold',
                            ]);
                        }

                        // --- Deduct Stock Immediately on Shipping ---
                        $warehouseId = $alloc['batch_id'] ? \App\Models\Batch::find($alloc['batch_id'])->warehouse_id : null;

                        // Deduct from Warehouse and Batch Inventory Levels
                        $inventoryLevel = \App\Models\InventoryLevel::where([
                            'warehouse_id' => $warehouseId,
                            'batch_id' => $alloc['batch_id'],
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                        ])->first();

                        if ($inventoryLevel) {
                            $inventoryLevel->decrement('current_quantity', $alloc['quantity']);
                        }

                        // Update BatchProduct totals
                        if ($batchProduct) {
                            $batchProduct->decrement('saleable_qty', $alloc['quantity']);
                        }

                        // Update Batch totals
                        \App\Models\Batch::where('id', $alloc['batch_id'])->decrement('total_saleable_qty', $alloc['quantity']);

                        // Log to Stock Ledger (Aggregate entry per batch)
                        $this->inventoryService->logStockChange(
                            productId: $item->product_id,
                            variantId: $item->product_variant_id,
                            warehouseId: $warehouseId,
                            changeQty: -$alloc['quantity'],
                            transactionType: 'SALE',
                            reasonCode: 'ORDER_SHIPPED',
                            referenceId: $order->order_id,
                            batchId: $alloc['batch_id'],
                            supplierId: $batchProduct->batch->supplier_id ?? null
                        );
                    }

                    // Global Stock Deduction (Moved to Shipped)
                    if ($item->product_variant_id) {
                        $item->productVariant->decrement('stock', $item->quantity);
                    } else {
                        $item->product->decrement('stock', $item->quantity);
                    }

                    // Update item total cost
                    $item->update(['total_cost' => $itemTotalCost]);
                    $orderTotalCost += $itemTotalCost;
                }

                // Update order total cost
                $order->update(['total_cost' => $orderTotalCost]);
            }

            if ($status === 'Delivered') {
                // Payment and Sales Count logic remains here
            }

            // Define statuses that are considered "Active" (stock is deducted)
            $activeStatuses = ['Pending', 'Processing', 'Out for Delivery', 'Delivered', 'Shipped'];
            // Define statuses that are considered "Restorative" (stock should be returned)
            $restorativeStatuses = ['Cancelled', 'Rejected'];

            // Stock restoration if cancelled (handled here for simplicity, although terminal status usually prevents this)
            if (in_array($oldStatus, $activeStatuses) && in_array($status, $restorativeStatuses)) {
                // Return serials to stock if they were shipped/allocated
                foreach ($order->orderItems as $item) {
                    \App\Models\BatchSerial::where('order_item_id', $item->id)->update([
                        'order_item_id' => null,
                        'stock_status' => 'in_stock',
                    ]);
                }
                $this->adjustStock($order, 'increase');
            }

            // Note: We don't deduct stock on 'Pending' anymore because we do it on 'Shipped' granularly.
            // But we keep the overall products.stock sync for compatibility.
            if ($status === 'Shipped') {
                // The adjustStock(decrease) was called on ORDER_PLACED in older versions.
                // Now, it is handled granularly within the 'Shipped' block above.
            }

            $order->order_status = $status;

            if (in_array($status, ['Cancelled', 'Rejected'])) {
                $order->rejection_reason = $reason;
            } else {
                $order->rejection_reason = null;
            }

            // Automatically set payment status to Paid if delivered
            if ($status === 'Delivered') {
                $order->payment_status = 'Paid';
            }

            $order->save();

            // Increment sales_count if delivered
            if ($status === 'Delivered') {
                foreach ($order->orderItems as $item) {
                    Product::where('id', $item->product_id)->increment('sales_count', $item->quantity);
                }
            }

            // Log status change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $status,
                'changed_at' => now(),
            ]);

            Log::info("Order {$order->order_id} status updated from {$oldStatus} to {$status}");

            if ($notify) {
                try {
                    Mail::to($order->email)->send(new OrderStatusUpdateMail($order));
                } catch (\Exception $e) {
                    Log::error('Order Status Update Email failed: '.$e->getMessage());
                }
            }

            return true;
        });
    }

    /**
     * Adjust stock for all items in an order.
     */
    protected function adjustStock(Order $order, string $direction): void
    {
        $order->load('orderItems');

        foreach ($order->orderItems as $item) {
            $warehouseId = null;

            // For restoration (increase), we need to find where it was deducted from,
            // but for now, we'll try to find any existing inventory level or log it to unallocated if unknown.
            // In a more complex system, we'd track warehouse_id per order_item.

            if ($item->product_variant_id) {
                $variant = ProductVariant::find($item->product_variant_id);
                if ($variant) {
                    if ($direction === 'increase') {
                        $variant->increment('stock', $item->quantity);
                    } else {
                        $variant->decrement('stock', $item->quantity);
                    }
                }
            } else {
                $product = Product::find($item->product_id);
                if ($product) {
                    if ($direction === 'increase') {
                        $product->increment('stock', $item->quantity);
                    } else {
                        $product->decrement('stock', $item->quantity);
                    }
                }
            }

            // Log the change in ledger
            $this->inventoryService->logStockChange(
                $item->product_id,
                $item->product_variant_id,
                null, // Tracking unallocated stock pool for now as per project current logic
                $direction === 'increase' ? $item->quantity : -$item->quantity,
                $direction === 'increase' ? 'ADJUSTMENT' : 'SALE',
                $direction === 'increase' ? 'ORDER_RESTORED' : 'ORDER_PLACED',
                $order->order_id
            );
        }
    }

    /**
     * Delete an order.
     */
    public function deleteOrder(Order $order): bool
    {
        return $order->delete();
    }

    public function placeOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $cartItems = $this->cartService->getCartItems();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Your cart is empty.');
            }

            $shippingMethodId = session('shipping_method_id');
            if (! $shippingMethodId) {
                throw new \Exception('Please select a shipping method.');
            }

            $shippingMethod = ShippingMethod::findOrFail($shippingMethodId);

            $orderId = $this->generateOrderId();
            $subtotal = $this->cartService->getCartTotal();
            $shippingCharge = $shippingMethod->price;

            // Coupon Calculation
            $discount = 0;
            $couponId = null;
            $appliedCoupon = null;

            if (session()->has('coupon')) {
                $sessionCoupon = session('coupon');
                $coupon = $this->couponService->getCouponByCode($sessionCoupon['code']);
                $validation = $this->couponService->validateCoupon($coupon, $subtotal);

                if ($validation['valid']) {
                    $discount = $this->couponService->calculateDiscount($coupon, $subtotal, $shippingCharge);
                    $couponId = $coupon->id;
                    $appliedCoupon = $coupon;
                } else {
                    session()->forget('coupon');
                }
            }

            $totalAmount = ($subtotal + $shippingCharge) - $discount;

            $productDiscountTotal = $cartItems->sum(fn ($item) => $item->product_discount * $item->quantity);

            $order = Order::create([
                'order_id' => $orderId,
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'address' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'zip' => $data['zip'] ?? null,
                'shipping_method_id' => $shippingMethod->id,
                'shipping_method_name' => $shippingMethod->name,
                'coupon_id' => $couponId,
                'subtotal' => $subtotal,
                'shipping_charge' => $shippingCharge,
                'discount' => $discount,
                'product_discount' => $productDiscountTotal,
                'total_amount' => $totalAmount,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'Pending',
                'order_status' => 'Pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Update user profile if authenticated
            if (auth()->check()) {
                auth()->user()->update([
                    'name' => $data['name'],
                    'mobile' => $data['mobile'],
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'state' => $data['state'] ?? null,
                    'country' => $data['country'] ?? null,
                    'zip' => $data['zip'] ?? null,
                ]);
            }

            // Log initial Pending status
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'Pending',
                'changed_at' => now(),
            ]);

            // If there's a coupon discount, calculate proportional distribution for items
            // but only if applied to product price. If applied to shipping, it's different.
            $couponProductDiscount = 0;
            if ($appliedCoupon && $appliedCoupon->apply_for === 'total_product_price') {
                $couponProductDiscount = $discount;
            }

            foreach ($cartItems as $item) {
                $itemCouponDiscount = 0;
                if ($couponProductDiscount > 0 && $subtotal > 0) {
                    // Proportional distribution: (item_subtotal / total_subtotal) * total_coupon_discount
                    $itemCouponDiscount = ($item->subtotal / $subtotal) * $couponProductDiscount;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->variant_id,
                    'product_name' => $item->product_name,
                    'variant_name' => $item->variant_name,
                    'regular_price' => $item->regular_price,
                    'product_discount' => $item->product_discount,
                    'coupon_discount' => $itemCouponDiscount / $item->quantity, // per unit
                    'unit_price' => $item->price - ($itemCouponDiscount / $item->quantity),
                    'quantity' => $item->quantity,
                    'total_price' => $item->subtotal - $itemCouponDiscount,
                ]);

                // We NO LONGER deduct stock here.
                // Stock will be deducted only when status changes to 'Shipped'.
            }

            // Record Coupon Usage
            if ($appliedCoupon) {
                $this->couponService->recordUsage($appliedCoupon, $order);
            }

            $this->cartService->clearCart();
            session()->forget(['shipping_method_id', 'coupon', 'shipping_charge', 'subtotal']);

            // Send order confirmation email
            try {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            } catch (\Exception $e) {
                Log::error('Order Confirmation Email failed: '.$e->getMessage());
            }

            return $order;
        });
    }

    public function generateOrderId(): string
    {
        do {
            $orderId = 'ORD-'.strtoupper(Str::random(10));
        } while (Order::where('order_id', $orderId)->exists());

        return $orderId;
    }

    /**
     * Generate an invoice for the given order.
     */
    public function generateInvoice(Order $order): Order
    {
        if (! $order->invoice_no) {
            $lastInvoice = Order::whereNotNull('invoice_no')->latest('id')->first();
            $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_no, -4) + 1 : 1;
            $invoiceNo = 'INV-'.date('Ymd').'-'.str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $order->update([
                'invoice_no' => $invoiceNo,
                'invoice_date' => now(),
            ]);
        }

        return $order;
    }

    /**
     * Regenerate an invoice (updates the date).
     */
    public function regenerateInvoice(Order $order): Order
    {
        if (! $order->invoice_no) {
            return $this->generateInvoice($order);
        }

        $order->update([
            'invoice_date' => now(),
        ]);

        return $order;
    }

    public function getStatusList(): array
    {
        return [
            'Pending' => 'Pending',
            'Processing' => 'Processing',
            'Shipped' => 'Shipped',
            'Out for Delivery' => 'Out for Delivery',
            'Delivered' => 'Delivered',
            'Cancelled' => 'Cancelled',
            'Rejected' => 'Rejected',
        ];
    }

    /**
     * Get available status transitions based on current status.
     */
    public function getAvailableTransitions(string $currentStatus): array
    {
        $transitions = [
            'Pending' => ['Processing', 'Cancelled', 'Rejected'],
            'Processing' => ['Shipped'],
            'Shipped' => ['Out for Delivery'],
            'Out for Delivery' => ['Delivered'],
            'Delivered' => [],
            'Cancelled' => [],
            'Rejected' => [],
        ];

        $availableKeys = $transitions[$currentStatus] ?? [];
        $allStatuses = $this->getStatusList();

        return array_intersect_key($allStatuses, array_flip($availableKeys));
    }

    public function getPaymentMethods(): array
    {
        return [
            'COD' => 'Cash On Delivery',
            'Online' => 'Online Payment',
        ];
    }

    public function getPaymentStatuses(): array
    {
        return [
            'Pending' => 'Pending',
            'Paid' => 'Paid',
            'Failed' => 'Failed',
        ];
    }

    /**
     * Get orders for a specific user.
     */
    public function getUserOrders(int $userId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Order::where('user_id', $userId)->latest()->paginate(10);
    }

    /**
     * Track an order by its order_id.
     */
    public function trackOrderById(string $orderId): ?Order
    {
        return Order::with(['orderItems', 'statusLogs'])->where('order_id', $orderId)->first();
    }

    /**
     * Get warehouses that have stock for a specific product/variant.
     */
    public function getWarehousesForItem(int $productId, ?int $variantId = null): \Illuminate\Support\Collection
    {
        return \App\Models\Warehouse::whereHas('inventoryLevels', function ($query) use ($productId, $variantId) {
            $query->where('product_id', $productId)
                ->where('current_quantity', '>', 0);

            if ($variantId && $variantId != 0) {
                $query->where('product_variant_id', $variantId);
            } else {
                $query->whereNull('product_variant_id');
            }
        })->get();
    }

    /**
     * Get batches for a product/variant in a specific warehouse.
     */
    public function getBatchesForItemInWarehouse(int $warehouseId, int $productId, ?int $variantId = null): \Illuminate\Support\Collection
    {
        $query = \App\Models\Batch::select('batches.*', 'inventory_levels.current_quantity as saleable_qty')
            ->join('inventory_levels', 'batches.id', '=', 'inventory_levels.batch_id')
            ->where('inventory_levels.warehouse_id', $warehouseId)
            ->where('inventory_levels.product_id', $productId)
            ->where('inventory_levels.current_quantity', '>', 0);

        if ($variantId && $variantId != 0) {
            $query->where('inventory_levels.product_variant_id', $variantId);
        } else {
            $query->whereNull('inventory_levels.product_variant_id');
        }

        return $query->get();
    }

    /**
     * Get available serials for a batch and product/variant.
     */
    public function getAvailableSerials(int $batchId, int $productId, ?int $variantId = null): \Illuminate\Support\Collection
    {
        $query = \App\Models\BatchSerial::where('batch_id', $batchId)
            ->where('product_id', $productId)
            ->where('stock_status', 'in_stock')
            ->where('product_status', 'good');

        if ($variantId && $variantId != 0) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }

        return $query->get();
    }
}
