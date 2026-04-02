<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchProduct;
use App\Models\BatchSerial;
use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Get all stock adjustments with search and filters.
     */
    public function getAllAdjustments(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = StockAdjustment::with(['warehouse', 'batch', 'creator']);

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['adjustment_number', 'remarks'];

        $filters = [];
        if (isset($params['warehouse_id']) && $params['warehouse_id'] !== 'all') {
            $filters['warehouse_id'] = $params['warehouse_id'];
        }

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest();
                break;
            case 'latest':
            default: $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a new stock adjustment.
     */
    public function storeAdjustment(array $data): StockAdjustment
    {
        return DB::transaction(function () use ($data) {
            // 1. Create or Find Batch
            $batch = Batch::firstOrCreate(
                ['batch_number' => $data['batch_number']],
                [
                    'warehouse_id' => $data['warehouse_id'],
                    'supplier_id' => null,
                    'total_received_qty' => 0,
                    'total_saleable_qty' => 0,
                    'total_damaged_qty' => 0,
                ]
            );

            // 2. Create Adjustment Header
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $this->generateAdjustmentNumber(),
                'warehouse_id' => $data['warehouse_id'],
                'batch_id' => $batch->id,
                'adjustment_date' => $data['adjustment_date'] ?? now(),
                'remarks' => $data['remarks'] ?? null,
                'created_by' => Auth::guard('admin')->id(),
            ]);

            $totalQty = 0;

            foreach ($data['items'] as $item) {
                $qty = (int) $item['quantity'];
                if ($qty <= 0) {
                    continue;
                }

                $totalQty += $qty;

                // 3. Create Adjustment Item
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity' => $qty,
                    'unit_cost' => $item['unit_cost'],
                ]);

                // 4. Update BatchProduct
                $bp = BatchProduct::firstOrCreate([
                    'batch_id' => $batch->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                ]);
                $bp->increment('received_qty', $qty);
                $bp->increment('saleable_qty', $qty);
                $bp->update(['unit_cost' => $item['unit_cost']]);

                // 5. Update Global Product Stock
                if ($item['product_variant_id']) {
                    ProductVariant::find($item['product_variant_id'])->increment('stock', $qty);
                } else {
                    Product::find($item['product_id'])->increment('stock', $qty);
                }

                // 6. Update/Create InventoryLevel
                $inventory = InventoryLevel::firstOrCreate([
                    'warehouse_id' => $data['warehouse_id'],
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'batch_id' => $batch->id,
                ]);
                $inventory->increment('current_quantity', $qty);

                // 7. Create Serials if provided
                if (! empty($item['serial_numbers'])) {
                    $serials = is_array($item['serial_numbers']) ? $item['serial_numbers'] : explode(',', $item['serial_numbers']);
                    foreach ($serials as $serialNo) {
                        if (empty(trim($serialNo))) {
                            continue;
                        }
                        BatchSerial::create([
                            'batch_id' => $batch->id,
                            'warehouse_id' => $data['warehouse_id'],
                            'product_id' => $item['product_id'],
                            'product_variant_id' => $item['product_variant_id'] ?? null,
                            'serial_no' => trim($serialNo),
                            'product_status' => 'good',
                            'stock_status' => 'in_stock',
                        ]);
                    }
                }

                // 8. Log to StockLedger (Aggregate entry)
                $this->inventoryService->logStockChange(
                    productId: $item['product_id'],
                    variantId: $item['product_variant_id'] ?? null,
                    warehouseId: $data['warehouse_id'],
                    changeQty: $qty,
                    transactionType: 'Manual_Adjustment',
                    reasonCode: 'Stock Adjustment',
                    referenceId: $adjustment->adjustment_number,
                    batchId: $batch->id,
                    supplierId: null
                );
            }

            // 9. Update Batch Totals
            $batch->increment('total_received_qty', $totalQty);
            $batch->increment('total_saleable_qty', $totalQty);

            return $adjustment;
        });
    }

    /**
     * Generate unique adjustment number.
     */
    protected function generateAdjustmentNumber(): string
    {
        $date = now()->format('Ymd');
        $count = StockAdjustment::whereDate('created_at', now()->toDateString())->count() + 1;

        return 'ADJ-'.$date.'-'.str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
