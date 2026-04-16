<?php

namespace App\Services;

use App\Mail\PurchaseOrderMail;
use App\Models\Batch;
use App\Models\BatchProduct;
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
        DB::transaction(function () use ($po, $status, $notifySupplier) {
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
                if (! $item) {
                    continue;
                }

                $goodQty = (int) ($itemData['received_quantity'] ?? 0);
                $damagedQty = (int) ($itemData['damaged_quantity'] ?? 0);

                // Parse separate serials
                $receivedSerials = $this->parseSerialNumbers($itemData['received_serials'] ?? []);
                $damagedSerials = $this->parseSerialNumbers($itemData['damaged_serials'] ?? []);

                // Create BatchProduct record
                BatchProduct::create([
                    'batch_id' => $batch->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'received_qty' => $goodQty,   // Good items received
                    'saleable_qty' => $goodQty,   // Initial saleable same as good
                    'damaged_qty' => $damagedQty, // Damaged items received
                    'unit_cost' => $item->unit_cost,
                ]);

                // Store Good Serials
                foreach ($receivedSerials as $serialNo) {
                    BatchSerial::create([
                        'batch_id' => $batch->id,
                        'warehouse_id' => $po->warehouse_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'serial_no' => $serialNo,
                        'product_status' => 'good',
                        'stock_status' => 'in_stock',
                    ]);
                }

                // Store Damaged Serials
                foreach ($damagedSerials as $serialNo) {
                    BatchSerial::create([
                        'batch_id' => $batch->id,
                        'warehouse_id' => $po->warehouse_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'serial_no' => $serialNo,
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

                $inventoryLevel->current_quantity += $goodQty;
                $inventoryLevel->damaged_quantity += $damagedQty;
                $inventoryLevel->save();

                // Log Aggregate Stock Changes
                if ($goodQty > 0) {
                    $this->inventoryService->logStockChange(
                        productId: $item->product_id,
                        variantId: $item->product_variant_id,
                        warehouseId: $po->warehouse_id,
                        changeQty: $goodQty,
                        transactionType: 'PO_RECEIPT',
                        reasonCode: 'RECEIVED_GOOD',
                        referenceId: $po->po_number,
                        batchId: $batch->id,
                        supplierId: $po->supplier_id
                    );
                }

                if ($damagedQty > 0) {
                    $this->inventoryService->logStockChange(
                        productId: $item->product_id,
                        variantId: $item->product_variant_id,
                        warehouseId: $po->warehouse_id,
                        changeQty: $damagedQty,
                        transactionType: 'DAMAGED',
                        reasonCode: 'RECEIVED_DAMAGED',
                        referenceId: $po->po_number,
                        batchId: $batch->id,
                        supplierId: $po->supplier_id
                    );
                }

                // Update Global Saleable Stock
                if ($goodQty > 0) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock', $goodQty);
                        }
                        // Note: We intentionally do NOT increment base product stock if it's a variant
                    } else {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $goodQty);
                        }
                    }
                }

                // Accumulate totals for Batch header
                $totalReceived += $goodQty;
                $totalSaleable += $goodQty;
                $totalDamaged += $damagedQty;

                // Update PO Item info
                $item->update(['received_quantity' => $goodQty]);
            }

            // Update Batch totals
            $batch->update([
                'total_received_qty' => $totalReceived,
                'total_saleable_qty' => $totalSaleable,
                'total_damaged_qty' => $totalDamaged,
            ]);

            // Calculate Performance Score
            $deliveryScore = 0;
            $receivedDate = $data['received_date'] ?? now();
            if ($po->expected_delivery_date && $receivedDate <= $po->expected_delivery_date) {
                $deliveryScore = 40;
            }

            $qualityScore = 0;
            $totalProducts = $totalReceived + $totalDamaged;
            if ($totalProducts > 0) {
                $qualityScore = ($totalReceived / $totalProducts) * 60;
            }

            $performanceScore = $deliveryScore + $qualityScore;

            $po->update([
                'total_received_qty' => $totalReceived,
                'total_damaged_qty' => $totalDamaged,
                'performance_score' => round($performanceScore, 2),
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

        if (empty(trim($input))) {
            return [];
        }

        $serials = [];
        // Support both comma and space/newline separation from tag inputs
        $parts = preg_split('/[,\s\n]+/', $input, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            $part = trim($part);
            if (! empty($part)) {
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
        $lastPo = PurchaseOrder::orderBy('id', 'desc')->first();
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
                Log::error('PO Send Mail Error: '.$e->getMessage());
            }
        }
    }
}
