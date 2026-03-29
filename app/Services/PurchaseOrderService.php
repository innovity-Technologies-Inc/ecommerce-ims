<?php

namespace App\Services;

use App\Mail\PurchaseOrderMail;
use App\Models\Batch;
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

            $po->update([
                'status' => 'Delivered',
                'received_date' => $data['received_date'] ?? now(),
            ]);

            // Create a SINGLE Batch for the entire PO Receipt
            $batch = Batch::create([
                'batch_number' => $data['batch_number'],
                'purchase_order_id' => $po->id,
                'supplier_id' => $po->supplier_id,
                'warehouse_id' => $po->warehouse_id,
                // Totals will be calculated below
            ]);

            $totalReceived = 0;
            $totalSaleable = 0;
            $totalDamaged = 0;

            foreach ($data['items'] as $itemId => $itemData) {
                $item = $po->items()->find($itemId);
                if (!$item) continue;

                $receivedQty = (int) ($itemData['received_quantity'] ?? 0);
                $damagedQty = (int) ($itemData['damaged_quantity'] ?? 0);
                $saleableQty = $receivedQty - $damagedQty;
                
                // Parse separate serials
                $receivedSerials = $this->parseSerialNumbers($itemData['received_serials'] ?? []);
                $damagedSerials = $this->parseSerialNumbers($itemData['damaged_serials'] ?? []);

                // Create BatchProduct record
                BatchProduct::create([
                    'batch_id' => $batch->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'received_qty' => $receivedQty,
                    'saleable_qty' => $saleableQty,
                    'damaged_qty' => $damagedQty,
                ]);

                // Store Good Serials
                foreach ($receivedSerials as $serial) {
                    BatchSerial::create([
                        'batch_id' => $batch->id,
                        'warehouse_id' => $po->warehouse_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'serial_no' => $serial,
                        'product_status' => 'good',
                        'stock_status' => 'in_stock',
                    ]);
                }

                // Store Damaged Serials
                foreach ($damagedSerials as $serial) {
                    BatchSerial::create([
                        'batch_id' => $batch->id,
                        'warehouse_id' => $po->warehouse_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'serial_no' => $serial,
                        'product_status' => 'damaged',
                        'stock_status' => 'in_stock',
                    ]);
                }

                // Update/Create InventoryLevel in the target warehouse
                $inventoryLevel = InventoryLevel::firstOrNew([
                    'warehouse_id' => $po->warehouse_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'batch_id' => $batch->id,
                ]);

                $inventoryLevel->current_quantity += $saleableQty;
                $inventoryLevel->damaged_quantity += $damagedQty;
                $inventoryLevel->save();

                // Log Stock Change (Saleable)
                if ($saleableQty > 0) {
                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        $po->warehouse_id,
                        $saleableQty,
                        'PO_RECEIPT',
                        'RECEIVED_GOOD',
                        $po->po_number,
                        $batch->id,
                        $po->supplier_id,
                        $item->unit_cost,
                        $saleableQty * $item->unit_cost
                    );
                }

                // Log Damaged Stock (Separate entry for clarity)
                if ($damagedQty > 0) {
                    $this->inventoryService->logStockChange(
                        $item->product_id,
                        $item->product_variant_id,
                        $po->warehouse_id,
                        $damagedQty,
                        'DAMAGED',
                        'RECEIVED_DAMAGED',
                        $po->po_number,
                        $batch->id,
                        $po->supplier_id,
                        $item->unit_cost,
                        $damagedQty * $item->unit_cost
                    );
                }

                // Update Global Saleable Stock
                if ($saleableQty > 0) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        $variant->increment('stock', $saleableQty);
                        $variant->update(['unit_cost' => $item->unit_cost]);
                    } else {
                        $product = Product::find($item->product_id);
                        $product->increment('stock', $saleableQty);
                        $product->update(['unit_cost' => $item->unit_cost]);
                    }
                }

                // Accumulate totals for Batch header
                $totalReceived += $receivedQty;
                $totalSaleable += $saleableQty;
                $totalDamaged += $damagedQty;

                // Update PO Item info
                $item->update(['received_quantity' => $receivedQty]);
            }

            // Update Batch totals
            $batch->update([
                'total_received_qty' => $totalReceived,
                'total_saleable_qty' => $totalSaleable,
                'total_damaged_qty' => $totalDamaged,
            ]);
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
