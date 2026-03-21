<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\Wastage;
use DaiyanMozumder\FlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    public function __construct(protected FlexSearch $flexSearch) {}

    public function getOrderDetails(string $orderId): ?Order
    {
        return Order::with(['orderItems.product', 'orderItems.productVariant'])
            ->where('order_id', $orderId)
            ->first();
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

    public function getReturnRequests(array $filters): LengthAwarePaginator
    {
        $query = ReturnRequest::with(['order', 'user']);

        return $this->flexSearch->apply($query, $filters, [
            'return_id' => 'like',
            'status' => '=',
            'order.order_id' => 'like',
        ], 'id', 'desc');
    }

    public function getReturnRequestDetails(int $id): ReturnRequest
    {
        return ReturnRequest::with(['order', 'user', 'returnItems.product', 'returnItems.productVariant'])->findOrFail($id);
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
            $returnRequest = ReturnRequest::with('returnItems')->findOrFail($id);
            $returnRequest->update(['status' => 'received']);

            foreach ($returnRequest->returnItems as $item) {
                $item->update(['is_received' => true]);

                if ($item->condition === 'intact') {
                    // Restock
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock', $item->quantity);
                        }
                    } else {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }

                    // Decrease sales value and count
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('sales_count', $item->quantity);
                        // sales_value logic depends on how it's tracked.
                        // Assuming total_sales is not directly on product but we might need to adjust something.
                    }
                } elseif ($item->condition === 'damage') {
                    Wastage::create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'reason' => 'Damaged return',
                        'return_id' => $returnRequest->id,
                    ]);
                }
            }

            return $returnRequest;
        });
    }

    public function getReturnedProducts(array $filters): LengthAwarePaginator
    {
        $query = ReturnItem::with(['returnRequest.order', 'product', 'productVariant'])
            ->where('is_received', true);

        return $this->flexSearch->apply($query, $filters, [
            'returnRequest.return_id' => 'like',
            'product.name' => 'like',
        ], 'id', 'desc');
    }

    public function getWastages(array $filters): LengthAwarePaginator
    {
        $query = Wastage::with(['product', 'productVariant', 'returnRequest']);

        return $this->flexSearch->apply($query, $filters, [
            'product.name' => 'like',
            'reason' => 'like',
        ], 'id', 'desc');
    }

    public function trackReturn(string $orderId): ?ReturnRequest
    {
        return ReturnRequest::whereHas('order', function ($query) use ($orderId) {
            $query->where('order_id', $orderId);
        })->latest()->first();
    }
}
