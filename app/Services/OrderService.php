<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Get all orders with search and sorting.
     */
    public function getAllOrders(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Order::with('user');

        $filters = [];

        // Map parameters to FlexSearch filters
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
            $filters['created_at>='] = $params['date_from'];
        }

        if (! empty($params['date_to'])) {
            $filters['created_at<='] = $params['date_to'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['order_id', 'name', 'email', 'mobile'];

        // Apply both filtering and searching via FlexSearch
        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Apply Sorting
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

    public function __construct(protected CartService $cartService) {}

    /**
     * Update order status and send notification if requested.
     */
    public function updateOrderStatus(Order $order, string $status, bool $notify = false): bool
    {
        $order->update(['order_status' => $status]);

        if ($notify) {
            try {
                Mail::to($order->email)->send(new OrderStatusUpdateMail($order));
            } catch (\Exception $e) {
                \Log::error('Order Status Update Email failed: '.$e->getMessage());
            }
        }

        return true;
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
            $totalAmount = $subtotal + $shippingCharge;

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
                'subtotal' => $subtotal,
                'shipping_charge' => $shippingCharge,
                'discount' => 0,
                'total_amount' => $totalAmount,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'Pending',
                'order_status' => 'Pending',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->variant_id,
                    'product_name' => $item->product_name,
                    'variant_name' => $item->variant_name,
                    'unit_price' => $item->price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->subtotal,
                ]);
            }

            $this->cartService->clearCart();
            session()->forget('shipping_method_id');

            // Send order confirmation email
            try {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            } catch (\Exception $e) {
                // Log the error but don't fail the order placement
                \Log::error('Order Confirmation Email failed: '.$e->getMessage());
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
            'Out for Delivery' => 'Out for Delivery',
            'Delivered' => 'Delivered',
            'Cancelled' => 'Cancelled',
            'Rejected' => 'Rejected',
        ];
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
        return Order::with(['orderItems'])->where('order_id', $orderId)->first();
    }
}
