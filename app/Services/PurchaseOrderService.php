<?php

namespace App\Services;

use App\Mail\PurchaseOrderMail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderService
{
    /**
     * Get all purchase orders with search and sorting.
     */
    public function getAllPurchaseOrders(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = PurchaseOrder::with(['supplier', 'warehouse', 'creator']);

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['po_number'];

        // Apply Search and Filtering
        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        if (isset($params['status']) && $params['status'] !== 'all') {
            $query->where('status', $params['status']);
        }

        if (isset($params['supplier_id'])) {
            $query->where('supplier_id', $params['supplier_id']);
        }

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a newly created purchase order.
     */
    public function storePurchaseOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create([
                'po_number' => $this->generatePONumber(),
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'] ?? 'Draft',
                'notify_supplier' => isset($data['notify_supplier']),
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::guard('admin')->id(),
            ]);

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['order_quantity'] * $item['unit_cost'];
                $totalAmount += $subtotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'order_quantity' => $item['order_quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $subtotal,
                ]);
            }

            $po->update(['total_amount' => $totalAmount]);

            if ($po->notify_supplier && $po->status !== 'Draft') {
                $this->sendPOMail($po);
            }

            return $po;
        });
    }

    /**
     * Update the specified purchase order.
     */
    public function updatePurchaseOrder(PurchaseOrder $po, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $data) {
            // Only update if not Delivered
            if ($po->status === 'Delivered') {
                throw new \Exception('Delivered Purchase Orders cannot be modified.');
            }

            $po->update([
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'],
                'notify_supplier' => isset($data['notify_supplier']),
                'notes' => $data['notes'] ?? null,
            ]);

            // Delete old items and recreate
            $po->items()->delete();

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['order_quantity'] * $item['unit_cost'];
                $totalAmount += $subtotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'order_quantity' => $item['order_quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $subtotal,
                ]);
            }

            $po->update(['total_amount' => $totalAmount]);

            if ($po->notify_supplier && $po->status === 'Sent') {
                $this->sendPOMail($po);
            }

            return $po;
        });
    }

    /**
     * Update status and handle inventory if Delivered.
     */
    public function updateStatus(PurchaseOrder $po, string $status, ?string $receivedDate = null): void
    {
        DB::transaction(function () use ($po, $status, $receivedDate) {
            if ($po->status === 'Delivered') {
                throw new \Exception('Status cannot be changed once delivered.');
            }

            $updateData = ['status' => $status];
            if ($status === 'Delivered') {
                $updateData['received_date'] = $receivedDate ?? now();

                // Increase Stock
                foreach ($po->items as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        $variant->increment('stock', $item->order_quantity);
                    } else {
                        $product = Product::find($item->product_id);
                        $product->increment('stock', $item->order_quantity);
                    }

                    // Mark as received (full quantity for now)
                    $item->update(['received_quantity' => $item->order_quantity]);
                }
            }

            $po->update($updateData);

            if ($status === 'Sent' && $po->notify_supplier) {
                $this->sendPOMail($po);
            }
        });
    }

    /**
     * Delete purchase order.
     */
    public function deletePurchaseOrder(PurchaseOrder $po): void
    {
        if ($po->status === 'Delivered') {
            throw new \Exception('Delivered Purchase Orders cannot be deleted.');
        }
        $po->delete();
    }

    /**
     * Generate unique PO Number.
     */
    protected function generatePONumber(): string
    {
        $lastPo = PurchaseOrder::latest()->first();
        $number = $lastPo ? (int) str_replace('PO-', '', $lastPo->po_number) + 1 : 1;

        return 'PO-'.str_pad($number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Send PO Mail to Supplier.
     */
    public function sendPOMail(PurchaseOrder $po): void
    {
        if ($po->supplier->email) {
            try {
                Mail::to($po->supplier->email)->send(new PurchaseOrderMail($po));
            } catch (\Exception $e) {
                // Log error or notify admin, but don't break transaction
            }
        }
    }
}
