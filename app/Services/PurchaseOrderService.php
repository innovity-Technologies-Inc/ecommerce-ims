<?php

namespace App\Services;

use App\Mail\PurchaseOrderMail;
use App\Models\Batch;
use App\Models\BatchItem;
use App\Models\BatchSerial;
use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Warehouse;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderService
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Get all purchase orders with search and sorting.
     */
    public function getAllPurchaseOrders(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = PurchaseOrder::with(['supplier', 'creator', 'warehouse']);

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

        if (isset($params['warehouse_id'])) {
            $query->where('warehouse_id', $params['warehouse_id']);
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
                'warehouse_id' => $data['warehouse_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'] ?? 'Draft',
                'notify_supplier' => isset($data['notify_supplier']),
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::guard('admin')->id(),
            ]);

            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'order_quantity' => $item['order_quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

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
                'warehouse_id' => $data['warehouse_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => $data['status'],
                'notify_supplier' => isset($data['notify_supplier']),
                'notes' => $data['notes'] ?? null,
            ]);

            // Delete old items and recreate
            $po->items()->delete();

            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'order_quantity' => $item['order_quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            if ($po->notify_supplier && $po->status === 'Sent') {
                $this->sendPOMail($po);
            }

            return $po;
        });
    }

    /**
     * Update status and handle inventory if Delivered.
     */
    public function updateStatus(PurchaseOrder $po, string $status, ?string $receivedDate = null, bool $notifySupplier = false): void
    {
        DB::transaction(function () use ($po, $status, $receivedDate, $notifySupplier) {
            if ($po->status === 'Delivered') {
                throw new \Exception('Status cannot be changed once delivered.');
            }

            if ($status === 'Delivered') {
                throw new \Exception('Delivered status can only be set via the "Receive PO" form.');
            }

            if ($status === 'Sent' && $po->status !== 'Draft') {
                throw new \Exception('Purchase Order can only be marked as Sent if it is currently in Draft status.');
            }

            $updateData = ['status' => $status];
            $po->update($updateData);

            if ($status === 'Sent' && $notifySupplier) {
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
     * Receive a purchase order with batches and serial numbers.
     */
    public function receivePurchaseOrder(PurchaseOrder $po, array $data): void
    {
        DB::transaction(function () use ($po, $data) {
            if ($po->status !== 'Sent') {
                throw new \Exception('Only Sent Purchase Orders can be received.');
            }

            $quarantineWarehouse = Warehouse::where('is_quarantine', true)->first();
            if (!$quarantineWarehouse) {
                throw new \Exception('Quarantine warehouse not found. Please seed warehouses.');
            }

            $po->update([
                'status' => 'Delivered',
                'received_date' => $data['received_date'] ?? now(),
            ]);

            foreach ($data['items'] as $itemId => $itemData) {
                $item = $po->items()->find($itemId);
                if (!$item) continue;

                $receivedQty = (int) ($itemData['received_quantity'] ?? 0);
                $damagedQty = (int) ($itemData['damaged_quantity'] ?? 0);
                
                $globalBatchNumber = $data['batch_number'];
                
                // Parse separate serials
                $receivedSerials = $this->parseSerialNumbers($itemData['received_serials'] ?? []);
                $damagedSerials = $this->parseSerialNumbers($itemData['damaged_serials'] ?? []);

                // Validation: If serial numbers provided, count must match respective quantity
                if (!empty($receivedSerials) && count($receivedSerials) !== $receivedQty) {
                    throw new \Exception("Received serial number count (" . count($receivedSerials) . ") for product {$item->product->name} does not match received quantity ({$receivedQty}).");
                }
                if (!empty($damagedSerials) && count($damagedSerials) !== $damagedQty) {
                    throw new \Exception("Damaged serial number count (" . count($damagedSerials) . ") for product {$item->product->name} does not match damaged quantity ({$damagedQty}).");
                }

                // Update PO Item received quantity (good items)
                $item->update(['received_quantity' => $receivedQty]);

                // 1. Handle RECEIVED (GOOD) Items
                if ($receivedQty > 0) {
                    $mainBatch = Batch::create([
                        'batch_number' => $globalBatchNumber,
                        'purchase_order_id' => $po->id,
                        'supplier_id' => $po->supplier_id,
                        'warehouse_id' => $po->warehouse_id,
                    ]);

                    BatchItem::create([
                        'batch_id' => $mainBatch->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $receivedQty,
                    ]);

                    foreach ($receivedSerials as $serial) {
                        BatchSerial::create([
                            'batch_id' => $mainBatch->id,
                            'warehouse_id' => $po->warehouse_id,
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'serial_no' => $serial,
                            'status' => 'in-stock',
                        ]);
                    }

                    InventoryLevel::create([
                        'warehouse_id' => $po->warehouse_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'batch_id' => $mainBatch->id,
                        'current_quantity' => $receivedQty,
                    ]);

                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        $po->warehouse_id,
                        $receivedQty,
                        'PO_RECEIPT',
                        'RECEIVED_GOOD',
                        $po->po_number,
                        $mainBatch->id,
                        $po->supplier_id,
                        $item->unit_cost,
                        $receivedQty * $item->unit_cost
                    );

                    // Update Global Stock (Saleable Only)
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        $variant->increment('stock', $receivedQty);
                        $variant->update(['unit_cost' => $item->unit_cost]);
                    } else {
                        $product = Product::find($item->product_id);
                        $product->increment('stock', $receivedQty);
                        $product->update(['unit_cost' => $item->unit_cost]);
                    }
                }

                // 2. Handle DAMAGED Items (Quarantine)
                if ($damagedQty > 0) {
                    $qBatch = Batch::create([
                        'batch_number' => $globalBatchNumber,
                        'purchase_order_id' => $po->id,
                        'supplier_id' => $po->supplier_id,
                        'warehouse_id' => $quarantineWarehouse->id,
                    ]);

                    BatchItem::create([
                        'batch_id' => $qBatch->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $damagedQty,
                    ]);

                    foreach ($damagedSerials as $serial) {
                        BatchSerial::create([
                            'batch_id' => $qBatch->id,
                            'warehouse_id' => $quarantineWarehouse->id,
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'serial_no' => $serial,
                            'status' => 'damaged',
                        ]);
                    }

                    InventoryLevel::create([
                        'warehouse_id' => $quarantineWarehouse->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'batch_id' => $qBatch->id,
                        'current_quantity' => $damagedQty,
                    ]);

                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        $quarantineWarehouse->id,
                        $damagedQty,
                        'QUARANTINE',
                        'RECEIVED_DAMAGED',
                        $po->po_number,
                        $qBatch->id,
                        $po->supplier_id,
                        $item->unit_cost,
                        $damagedQty * $item->unit_cost
                    );
                }
            }
        });
    }

    /**
     * Parse serial numbers from input string.
     * Supports formats: SN001 - SN300, SN302, SN305-SN310, or simple comma separated
     */
    public function parseSerialNumbers(string|array $input): array
    {
        if (is_array($input)) {
            $serials = [];
            foreach ($input as $item) {
                $parsed = $this->parseSerialNumbers($item);
                $serials = array_merge($serials, $parsed);
            }
            return array_unique($serials);
        }

        if (empty(trim($input))) return [];

        $serials = [];
        // Support both comma and space/newline separation from tag inputs
        $parts = preg_split('/[,\s\n]+/', $input, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            if (str_contains($part, '-')) {
                $range = explode('-', $part);
                if (count($range) === 2) {
                    $start = trim($range[0]);
                    $end = trim($range[1]);

                    preg_match('/^([a-zA-Z]*)([0-9]+)$/', $start, $startMatches);
                    preg_match('/^([a-zA-Z]*)([0-9]+)$/', $end, $endMatches);

                    if (count($startMatches) === 3 && count($endMatches) === 3) {
                        $prefix = $startMatches[1];
                        $startNum = (int) $startMatches[2];
                        $endNum = (int) $endMatches[2];
                        $padding = strlen($startMatches[2]);

                        for ($i = $startNum; $i <= $endNum; $i++) {
                            $serials[] = $prefix . str_pad((string)$i, $padding, '0', STR_PAD_LEFT);
                        }
                    } else {
                        $serials[] = $start;
                        $serials[] = $end;
                    }
                } else {
                    $serials[] = $part;
                }
            } else {
                $serials[] = $part;
            }
        }

        return array_unique($serials);
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
                Log::error('PO Send Mail Error: ' . $e->getMessage());
            }
        }
    }
}
