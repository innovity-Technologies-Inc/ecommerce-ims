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

            // 1. Create a single Batch Header for the PO receipt (Main)
            $batchHeader = Batch::create([
                'batch_number' => $data['batch_number'],
                'purchase_order_id' => $po->id,
                'warehouse_id' => $po->warehouse_id,
            ]);

            // 2. Create a separate Batch Header for Damaged items in Quarantine
            $damagedBatchHeader = Batch::create([
                'batch_number' => $data['batch_number'] . '-DMG',
                'purchase_order_id' => $po->id,
                'warehouse_id' => $quarantineWarehouse->id,
            ]);

            $po->update([
                'status' => 'Delivered',
                'received_date' => $data['received_date'] ?? now(),
            ]);

            foreach ($data['items'] as $itemId => $itemData) {
                $item = $po->items()->find($itemId);
                if (!$item) continue;

                $receivedQty = (int) ($itemData['received_quantity'] ?? 0);
                $damagedQty = (int) ($itemData['damaged_quantity'] ?? 0);
                $serialInput = $itemData['serial_numbers'] ?? [];
                $parsedSerials = $this->parseSerialNumbers($serialInput);

                // Validation: If serial numbers provided, count must match received quantity
                if (!empty($parsedSerials) && count($parsedSerials) !== ($receivedQty + $damagedQty)) {
                    throw new \Exception("Serial number count (" . count($parsedSerials) . ") for product {$item->product->name} does not match total quantity (" . ($receivedQty + $damagedQty) . ").");
                }

                // 3. Update PO Item received quantity
                $item->update([
                    'received_quantity' => $receivedQty,
                ]);

                // 4. Handle Received Items (Main Warehouse)
                if ($receivedQty > 0) {
                    BatchItem::create([
                        'batch_id' => $batchHeader->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $receivedQty,
                    ]);

                    // Create Serials for Received Items (Take first $receivedQty serials)
                    $receivedSerials = array_slice($parsedSerials, 0, $receivedQty);
                    foreach ($receivedSerials as $serial) {
                        BatchSerial::create([
                            'batch_id' => $batchHeader->id,
                            'warehouse_id' => $po->warehouse_id,
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'serial_no' => $serial,
                            'status' => 'Available',
                        ]);
                    }

                    // Update Inventory Level for Main Warehouse (Linked to this Batch)
                    $inventoryLevel = InventoryLevel::create([
                        'warehouse_id' => $po->warehouse_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'batch_id' => $batchHeader->id,
                        'current_quantity' => $receivedQty,
                    ]);

                    // Update Total Product/Variant Stock (Global pool)
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        $variant->increment('stock', $receivedQty);
                        $variant->update(['unit_cost' => $item->unit_cost]);
                    } else {
                        $product = Product::find($item->product_id);
                        $product->increment('stock', $receivedQty);
                        $product->update(['unit_cost' => $item->unit_cost]);
                    }

                    // Log Stock Ledger
                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        $po->warehouse_id,
                        $receivedQty,
                        'PO_RECEIPT',
                        'STOCK_IN',
                        $po->po_number,
                        $batchHeader->id
                    );
                }

                // 5. Handle Damaged Items (Quarantine)
                if ($damagedQty > 0) {
                    BatchItem::create([
                        'batch_id' => $damagedBatchHeader->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $damagedQty,
                    ]);

                    // Create Serials for Damaged Items (Take remaining serials)
                    $damagedSerials = array_slice($parsedSerials, $receivedQty);
                    foreach ($damagedSerials as $serial) {
                        BatchSerial::create([
                            'batch_id' => $damagedBatchHeader->id,
                            'warehouse_id' => $quarantineWarehouse->id,
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'serial_no' => $serial,
                            'status' => 'Damaged',
                        ]);
                    }

                    // Update Inventory Level for Quarantine Warehouse (Linked to this Batch)
                    $qInventoryLevel = InventoryLevel::create([
                        'warehouse_id' => $quarantineWarehouse->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'batch_id' => $damagedBatchHeader->id,
                        'current_quantity' => $damagedQty,
                    ]);

                    // Log Stock Ledger
                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        $quarantineWarehouse->id,
                        $damagedQty,
                        'PO_RECEIPT',
                        'QUARANTINE_DAMAGED',
                        $po->po_number,
                        $damagedBatchHeader->id
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
