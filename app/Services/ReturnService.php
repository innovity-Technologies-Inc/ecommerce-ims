<?php

namespace App\Services;

use App\HelperClass;
use App\Mail\ReturnRequestConfirmationMail;
use App\Mail\ReturnStatusUpdateMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\Wastage;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReturnService
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function getOrderDetails(string $orderId): ?Order
    {
        return Order::with(['orderItems.product.primaryImage', 'orderItems.productVariant'])
            ->where('order_id', $orderId)
            ->first();
    }

    public function checkExistingReturn(int $orderIdPk): bool
    {
        return ReturnRequest::where('order_id', $orderIdPk)->exists();
    }

    public function storeReturnRequest(array $data): ReturnRequest
    {
        return DB::transaction(function () use ($data) {
            $returnRequest = ReturnRequest::create([
                'order_id' => $data['order_id_pk'],
                'return_id' => 'RET-'.strtoupper(uniqid()),
                'user_id' => Auth::check() ? Auth::id() : null,
                'reason' => $data['reason'],
                'status' => 'pending',
            ]);

            // Handle Multiple Images
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $index => $image) {
                    $path = HelperClass::file_upload($image, 'returns');
                    \App\Models\ReturnImage::create([
                        'return_id' => $returnRequest->id,
                        'image_path' => $path,
                    ]);

                    // Set first image as primary for backward compatibility
                    if ($index === 0) {
                        $returnRequest->update(['image' => $path]);
                    }
                }
            } elseif (isset($data['image'])) {
                // Handle single image fallback
                $path = HelperClass::file_upload($data['image'], 'returns');
                \App\Models\ReturnImage::create([
                    'return_id' => $returnRequest->id,
                    'image_path' => $path,
                ]);
                $returnRequest->update(['image' => $path]);
            }

            foreach ($data['items'] as $item) {
                if ($item['quantity'] > 0) {
                    ReturnItem::create([
                        'return_id' => $returnRequest->id,
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                        'condition' => 'pending',
                        'is_received' => false,
                    ]);
                }
            }

            // Send Confirmation Email
            $recipientEmail = $returnRequest->user ? $returnRequest->user->email : $returnRequest->order->email;
            if ($recipientEmail) {
                Mail::to($recipientEmail)->send(new ReturnRequestConfirmationMail($returnRequest->load(['returnItems.product', 'returnItems.productVariant', 'order'])));
            }

            // Trigger Admin Notification
            try {
                \App\Models\AdminNotification::create([
                    'type' => 'return',
                    'title' => 'New Return Request',
                    'message' => "Return Request #{$returnRequest->return_id} has been submitted for Order #{$returnRequest->order->order_id}.",
                    'url' => route('admin.returns.show_request', $returnRequest->id),
                ]);
            } catch (\Exception $e) {
                Log::error('Return Notification failed: '.$e->getMessage());
            }

            return $returnRequest;
        });
    }

    public function getReturnRequests(array $params = []): LengthAwarePaginator
    {
        $query = ReturnRequest::with(['order', 'user']);

        $filters = [];
        if (isset($params['status']) && $params['status'] !== '') {
            $filters['status'] = $params['status'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['return_id', 'order.order_id'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        return $query->latest('id')->paginate(15);
    }

    public function getReturnRequestDetails(int $id): ReturnRequest
    {
        return ReturnRequest::with(['order', 'user', 'returnItems.product.primaryImage', 'returnItems.productVariant'])->findOrFail($id);
    }

    public function updateStatus(int $id, array $data): ReturnRequest
    {
        return DB::transaction(function () use ($id, $data) {
            $returnRequest = ReturnRequest::findOrFail($id);
            $returnRequest->update([
                'status' => $data['status'],
                'rejection_reason' => $data['status'] === 'rejected' ? $data['rejection_reason'] : null,
            ]);

            // Send Status Update Email
            $recipientEmail = $returnRequest->user ? $returnRequest->user->email : $returnRequest->order->email;
            if ($recipientEmail) {
                Mail::to($recipientEmail)->send(new ReturnStatusUpdateMail($returnRequest->load(['returnItems.product', 'returnItems.productVariant', 'order'])));
            }

            return $returnRequest;
        });
    }

    public function receiveReturn(int $id, array $data): ReturnRequest
    {
        return DB::transaction(function () use ($id, $data) {
            $returnRequest = ReturnRequest::findOrFail($id);

            // 1. Process Allocation (Moved from Approval to Receiving)
            foreach ($data['items'] as $itemId => $itemData) {
                $returnItem = ReturnItem::findOrFail($itemId);

                // Handle Multiple Allocations (Splitting ReturnItem if needed)
                if (! empty($itemData['allocations'])) {
                    $firstAlloc = true;
                    foreach ($itemData['allocations'] as $alloc) {
                        $serialIds = $alloc['batch_serial_ids'] ?? (isset($alloc['batch_serial_id']) ? [$alloc['batch_serial_id']] : []);
                        $currentCondition = $alloc['condition'];

                        if (! empty($serialIds)) {
                            foreach ($serialIds as $serialId) {
                                if ($firstAlloc) {
                                    $returnItem->update([
                                        'condition' => $currentCondition,
                                        'batch_id' => $alloc['batch_id'],
                                        'batch_serial_id' => $serialId,
                                        'quantity' => 1,
                                        'total_price' => 1 * $returnItem->unit_price,
                                    ]);
                                    $firstAlloc = false;
                                } else {
                                    ReturnItem::create([
                                        'return_id' => $returnRequest->id,
                                        'product_id' => $returnItem->product_id,
                                        'product_variant_id' => $returnItem->product_variant_id,
                                        'batch_id' => $alloc['batch_id'],
                                        'batch_serial_id' => $serialId,
                                        'quantity' => 1,
                                        'unit_price' => $returnItem->unit_price,
                                        'total_price' => $returnItem->unit_price,
                                        'condition' => $currentCondition,
                                        'is_received' => false,
                                    ]);
                                }
                            }
                        } else {
                            if ($firstAlloc) {
                                // Update existing ReturnItem with first allocation
                                $returnItem->update([
                                    'condition' => $currentCondition,
                                    'batch_id' => $alloc['batch_id'],
                                    'batch_serial_id' => null,
                                    'quantity' => $alloc['quantity'],
                                    'total_price' => $alloc['quantity'] * $returnItem->unit_price,
                                ]);
                                $firstAlloc = false;
                            } else {
                                // Create new ReturnItem for additional allocations
                                ReturnItem::create([
                                    'return_id' => $returnRequest->id,
                                    'product_id' => $returnItem->product_id,
                                    'product_variant_id' => $returnItem->product_variant_id,
                                    'batch_id' => $alloc['batch_id'],
                                    'batch_serial_id' => null,
                                    'quantity' => $alloc['quantity'],
                                    'unit_price' => $returnItem->unit_price,
                                    'total_price' => $alloc['quantity'] * $returnItem->unit_price,
                                    'condition' => $currentCondition,
                                    'is_received' => false,
                                ]);
                            }
                        }
                    }
                }
            }

            // 2. Refresh ReturnRequest and Process Stock Updates
            $returnRequest = ReturnRequest::with(['returnItems', 'order'])->findOrFail($id);
            $returnRequest->update(['status' => 'received']);

            $order = $returnRequest->order;

            // Group items by Batch, Product, and Variant for aggregate updates
            $groupedItems = $returnRequest->returnItems->groupBy(function ($item) {
                return ($item->batch_id ?? '0').'-'.$item->product_id.'-'.($item->product_variant_id ?? '0');
            });

            foreach ($groupedItems as $group) {
                $firstItem = $group->first();
                $totalQty = $group->sum('quantity');
                $totalPrice = $group->sum('total_price');

                // 1. Process Stock & Status for each item in group
                foreach ($group as $item) {
                    $item->update(['is_received' => true]);

                    if ($item->condition === 'intact') {
                        // Mark Serial as In-Stock if exists
                        if ($item->batch_serial_id) {
                            \App\Models\BatchSerial::where('id', $item->batch_serial_id)->update([
                                'product_status' => 'good',
                                'stock_status' => 'in_stock',
                                'order_item_id' => null, // Unlink from original order
                            ]);
                        }
                    } elseif ($item->condition === 'damage') {
                        // Mark Serial as Damaged/Wastage if exists
                        if ($item->batch_serial_id) {
                            \App\Models\BatchSerial::where('id', $item->batch_serial_id)->update([
                                'product_status' => 'damaged',
                                'stock_status' => 'wastage',
                                'order_item_id' => null,
                            ]);
                        }

                        // Get batch for warehouse_id
                        $batch = $item->batch_id ? \App\Models\Batch::find($item->batch_id) : null;

                        Wastage::create([
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'warehouse_id' => $batch ? $batch->warehouse_id : null,
                            'batch_id' => $item->batch_id,
                            'quantity' => $item->quantity,
                            'reason' => 'Damaged return ('.$returnRequest->return_id.')',
                            'return_id' => $returnRequest->id,
                        ]);

                        // Increment damaged_qty in BatchProduct
                        if ($item->batch_id) {
                            \App\Models\BatchProduct::where([
                                'batch_id' => $item->batch_id,
                                'product_id' => $item->product_id,
                                'product_variant_id' => $item->product_variant_id,
                            ])->increment('damaged_qty', $item->quantity);

                            // Also increment damaged_quantity in InventoryLevel
                            if ($batch) {
                                \App\Models\InventoryLevel::where([
                                    'warehouse_id' => $batch->warehouse_id,
                                    'batch_id' => $item->batch_id,
                                    'product_id' => $item->product_id,
                                    'product_variant_id' => $item->product_variant_id,
                                ])->increment('damaged_quantity', $item->quantity);

                                // Log to Stock Ledger as RETURN_DAMAGED
                                $this->inventoryService->logStockChange(
                                    productId: $item->product_id,
                                    variantId: $item->product_variant_id,
                                    warehouseId: $batch->warehouse_id,
                                    changeQty: $item->quantity,
                                    transactionType: 'RETURN_DAMAGED',
                                    reasonCode: 'DAMAGED_RETURN',
                                    referenceId: $returnRequest->return_id,
                                    batchId: $item->batch_id
                                );
                            }
                        }
                    }
                }

                // 2. Aggregate Inventory Updates (Only for INTACT items in the group)
                $intactQty = $group->where('condition', 'intact')->sum('quantity');
                if ($intactQty > 0 && $firstItem->batch_id) {
                    $batch = \App\Models\Batch::find($firstItem->batch_id);
                    if ($batch) {
                        // Increment physical stock in all tables

                        // a. Global Product/Variant Stock
                        if ($firstItem->product_variant_id) {
                            \App\Models\ProductVariant::where('id', $firstItem->product_variant_id)->increment('stock', $intactQty);
                        } else {
                            \App\Models\Product::where('id', $firstItem->product_id)->increment('stock', $intactQty);
                        }

                        // b. Batch Totals
                        $batch->increment('total_saleable_qty', $intactQty);

                        // c. BatchProduct Totals
                        \App\Models\BatchProduct::where([
                            'batch_id' => $firstItem->batch_id,
                            'product_id' => $firstItem->product_id,
                            'product_variant_id' => $firstItem->product_variant_id,
                        ])->increment('saleable_qty', $intactQty);

                        // d. Inventory Level (Warehouse + Batch)
                        \App\Models\InventoryLevel::where([
                            'warehouse_id' => $batch->warehouse_id,
                            'batch_id' => $firstItem->batch_id,
                            'product_id' => $firstItem->product_id,
                            'product_variant_id' => $firstItem->product_variant_id,
                        ])->increment('current_quantity', $intactQty);

                        // Log to Stock Ledger
                        $this->inventoryService->logStockChange(
                            productId: $firstItem->product_id,
                            variantId: $firstItem->product_variant_id,
                            warehouseId: $batch->warehouse_id,
                            changeQty: $intactQty,
                            transactionType: 'RETURN_INTACT',
                            reasonCode: 'INTACT_RETURN',
                            referenceId: $returnRequest->return_id,
                            batchId: $firstItem->batch_id
                        );
                    }
                }

                // 3. Financial & Quantity Reductions in Original Order (For ALL returned items)
                $product = \App\Models\Product::find($firstItem->product_id);
                if ($product) {
                    $product->decrement('sales_count', $totalQty);
                }

                $order->subtotal -= $totalPrice;
                $order->total_amount -= $totalPrice;

                // Update OrderItem quantity
                $orderItem = \App\Models\OrderItem::where('order_id', $order->id)
                    ->where('product_id', $firstItem->product_id)
                    ->where('product_variant_id', $firstItem->product_variant_id)
                    ->first();

                if ($orderItem) {
                    $orderItem->decrement('quantity', $totalQty);
                    $orderItem->decrement('total_price', $totalPrice);

                    // Reduce from ordered_product_batches if possible
                    if ($firstItem->batch_id) {
                        $opb = \App\Models\OrderedProductBatch::where([
                            'order_item_id' => $orderItem->id,
                            'batch_id' => $firstItem->batch_id,
                        ])->first();

                        if ($opb) {
                            $reducedQty = min($opb->quantity, $totalQty);
                            $costReduction = $reducedQty * $opb->unit_cost;

                            $opb->decrement('quantity', $reducedQty);
                            $opb->decrement('subtotal_cost', $costReduction);

                            $orderItem->decrement('total_cost', $costReduction);
                            $order->decrement('total_cost', $costReduction);

                            if ($opb->quantity <= 0) {
                                $opb->delete();
                            }
                        }
                    }
                }
            }

            $order->save();

            return $returnRequest;
        });
    }

    public function getReturnedProducts(array $params = []): LengthAwarePaginator
    {
        $query = ReturnItem::with(['returnRequest.order', 'product.primaryImage', 'productVariant'])
            ->where('is_received', true);

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['returnRequest.return_id', 'product.name'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        return $query->latest('id')->paginate(15);
    }

    public function getWastages(array $params = []): LengthAwarePaginator
    {
        $query = Wastage::with(['product.primaryImage', 'productVariant', 'returnRequest']);

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['product.name', 'reason'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        return $query->latest('id')->paginate(15);
    }

    public function trackReturn(string $orderId): ?ReturnRequest
    {
        return ReturnRequest::whereHas('order', function ($query) use ($orderId) {
            $query->where('order_id', $orderId);
        })->latest()->first();
    }

    /**
     * Get batches that were used to fulfill a specific order item.
     */
    public function getOrderBatches(int $orderItemId): \Illuminate\Support\Collection
    {
        return \App\Models\OrderedProductBatch::with('batch')
            ->where('order_item_id', $orderItemId)
            ->get()
            ->map(function ($opb) {
                return [
                    'id' => $opb->batch_id,
                    'batch_number' => $opb->batch->batch_number,
                    'shipped_qty' => $opb->quantity,
                ];
            });
    }

    /**
     * Get serials that were shipped for a specific order item and batch.
     */
    public function getOrderSerials(int $orderItemId, int $batchId): \Illuminate\Support\Collection
    {
        return \App\Models\BatchSerial::where('order_item_id', $orderItemId)
            ->where('batch_id', $batchId)
            ->where('stock_status', 'sold')
            ->get(['id', 'serial_no']);
    }
}
