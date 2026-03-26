<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\Wastage;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $imagePath = null;
            if (isset($data['image'])) {
                $imagePath = HelperClass::file_upload($data['image'], 'returns');
            }

            $returnRequest = ReturnRequest::create([
                'order_id' => $data['order_id_pk'],
                'return_id' => 'RET-'.strtoupper(uniqid()),
                'user_id' => Auth::check() ? Auth::id() : null,
                'reason' => $data['reason'],
                'status' => 'pending',
                'image' => $imagePath,
            ]);

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

            if ($data['status'] === 'approved') {
                foreach ($data['items'] as $itemId => $itemData) {
                    $returnItem = ReturnItem::findOrFail($itemId);
                    $returnItem->update([
                        'condition' => $itemData['condition'],
                    ]);
                }
            }

            return $returnRequest;
        });
    }

    public function receiveReturn(int $id): ReturnRequest
    {
        return DB::transaction(function () use ($id) {
            $returnRequest = ReturnRequest::with(['returnItems', 'order'])->findOrFail($id);
            $returnRequest->update(['status' => 'received']);

            $order = $returnRequest->order;

            foreach ($returnRequest->returnItems as $item) {
                $item->update(['is_received' => true]);

                if ($item->condition === 'intact') {
                    // Restock
                    if ($item->product_variant_id) {
                        $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock', $item->quantity);
                        }
                    } else {
                        $product = \App\Models\Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }

                    // Decrease product sales count
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('sales_count', $item->quantity);
                    }

                    // Adjust Order and OrderItem for Dashboard
                    $order->subtotal -= $item->total_price;
                    $order->total_amount -= $item->total_price;

                    $orderItem = \App\Models\OrderItem::where('order_id', $order->id)
                        ->where('product_id', $item->product_id)
                        ->where('product_variant_id', $item->product_variant_id)
                        ->first();

                    if ($orderItem) {
                        $orderItem->decrement('quantity', $item->quantity);
                        $orderItem->decrement('total_price', $item->total_price);
                    }

                    // Log to Stock Ledger (Intact return to pool)
                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        null,
                        $item->quantity,
                        'RTV_DISPATCH',
                        'INTACT_RETURN',
                        $returnRequest->return_id
                    );
                } elseif ($item->condition === 'damage') {
                    Wastage::create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'reason' => 'Damaged return',
                        'return_id' => $returnRequest->id,
                    ]);

                    // Log to Stock Ledger (Damaged - not restocked but tracked as adjustment)
                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        null,
                        0, // Stock didn't change (wasn't restocked), but we log the wastage event
                        'ADJUSTMENT',
                        'SHRINKAGE_LOST_DAMAGED_RETURN',
                        $returnRequest->return_id
                    );
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
}
