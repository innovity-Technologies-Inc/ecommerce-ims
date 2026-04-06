<?php

namespace App\Services;

use App\Models\InventoryLevel;
use App\Models\Order;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehousePerformanceService
{
    /**
     * Get Performance Metrics for all warehouses or a specific one.
     */
    public function getPerformanceReport(array $filters, ?int $perPage = 10)
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->format('Y-m-d');
        $warehouseId = $filters['warehouse_id'] ?? null;

        $query = Warehouse::query();

        if ($warehouseId && $warehouseId !== 'all') {
            $query->where('id', $warehouseId);
        }

        $warehouses = $query->get();
        $reportData = collect();

        foreach ($warehouses as $warehouse) {
            $metrics = $this->calculateWarehouseMetrics($warehouse->id, $startDate, $endDate);
            $reportData->push(array_merge(['warehouse_name' => $warehouse->name, 'warehouse_id' => $warehouse->id], $metrics));
        }

        if ($perPage) {
            $page = request()->get('page', 1);
            return new LengthAwarePaginator(
                $reportData->forPage($page, $perPage),
                $reportData->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return $reportData;
    }

    /**
     * Calculate all KPIs for a specific warehouse and date range.
     */
    protected function calculateWarehouseMetrics(int $warehouseId, string $startDate, string $endDate): array
    {
        // 1. Stock Movements from Ledgers
        $movements = DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'PO_RECEIPT' THEN change_qty ELSE 0 END) as received_qty,
                SUM(CASE WHEN transaction_type = 'SALE' THEN ABS(change_qty) ELSE 0 END) as sold_qty,
                SUM(CASE WHEN transaction_type IN ('Manual_Adjustment', 'STOCK_ADJUSTMENT_IN', 'STOCK_ADJUSTMENT') AND change_qty > 0 THEN change_qty ELSE 0 END) as adjusted_in,
                SUM(CASE WHEN transaction_type IN ('Manual_Adjustment', 'STOCK_ADJUSTMENT_OUT', 'STOCK_ADJUSTMENT') AND change_qty < 0 THEN ABS(change_qty) ELSE 0 END) as adjusted_out,
                SUM(CASE WHEN transaction_type = 'warehouse_damage' THEN ABS(change_qty) ELSE 0 END) as wastage_entry_qty,
                SUM(CASE WHEN transaction_type = 'DAMAGED' AND change_qty > 0 THEN change_qty ELSE 0 END) as damaged_plus_stock,
                SUM(CASE WHEN transaction_type IN ('RTV_Dispatch', 'RTV_DISPATCH') THEN ABS(change_qty) ELSE 0 END) as rtv_qty,
                SUM(CASE WHEN transaction_type = 'RETURN_INTACT' THEN change_qty ELSE 0 END) as returns_qty
            ")
            ->first();

        // 2. Total Opening Stock (Sum of all changes BEFORE start date, including damaged)
        $openingStock = DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->whereDate('created_at', '<', $startDate)
            ->sum('change_qty');

        // 3. Closing Stock Snapshots (Live)
        $saleableClosing = (int) InventoryLevel::where('warehouse_id', $warehouseId)->sum('current_quantity');
        $damagedClosing = (int) InventoryLevel::where('warehouse_id', $warehouseId)->sum('damaged_quantity');
        $totalClosingStock = $saleableClosing + $damagedClosing;

        // 4. Inventory Value (Live - Saleable only)
        $inventoryValue = DB::table('inventory_levels')
            ->join('batch_products', function($join) {
                $join->on('inventory_levels.batch_id', '=', 'batch_products.batch_id')
                     ->on('inventory_levels.product_id', '=', 'batch_products.product_id')
                     ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(batch_products.product_variant_id, 0)');
            })
            ->where('inventory_levels.warehouse_id', $warehouseId)
            ->sum(DB::raw('inventory_levels.current_quantity * batch_products.unit_cost'));

        // 5. Returns (All conditions: Intact + Damaged)
        $returns = DB::table('return_items')
            ->join('batches', 'return_items.batch_id', '=', 'batches.id')
            ->where('batches.warehouse_id', $warehouseId)
            ->where('return_items.is_received', true)
            ->whereDate('return_items.created_at', '>=', $startDate)
            ->whereDate('return_items.created_at', '<=', $endDate)
            ->sum('return_items.quantity');

        // 6. Fulfillment KPIs (using ordered_product_batches for accurate warehouse attribution)
        $fulfillment = DB::table('ordered_product_batches')
            ->join('batches', 'ordered_product_batches.batch_id', '=', 'batches.id')
            ->join('order_items', 'ordered_product_batches.order_item_id', '=', 'order_items.id')
            ->where('batches.warehouse_id', $warehouseId)
            ->whereDate('ordered_product_batches.created_at', '>=', $startDate)
            ->whereDate('ordered_product_batches.created_at', '<=', $endDate)
            ->selectRaw("
                COUNT(DISTINCT order_items.order_id) as fulfillment_orders,
                SUM(ordered_product_batches.quantity) as units_shipped
            ")
            ->first();

        // 7. Fill Rate Calculations
        $unitsShipped = (int) ($fulfillment->units_shipped ?? 0);
        
        // Total Ordered for this warehouse context
        $totalOrderedUnits = DB::table('order_items')
            ->whereExists(function($query) use ($warehouseId, $startDate, $endDate) {
                $query->select(DB::raw(1))
                    ->from('ordered_product_batches')
                    ->join('batches', 'ordered_product_batches.batch_id', '=', 'batches.id')
                    ->whereColumn('ordered_product_batches.order_item_id', 'order_items.id')
                    ->where('batches.warehouse_id', $warehouseId)
                    ->whereDate('ordered_product_batches.created_at', '>=', $startDate)
                    ->whereDate('ordered_product_batches.created_at', '<=', $endDate);
            })
            ->sum('quantity');

        // Gross Fill Rate: Shipped / Ordered
        $grossFillRate = $totalOrderedUnits > 0 ? ($unitsShipped / $totalOrderedUnits) * 100 : 100;
        
        // Net Fill Rate: (Shipped - Returned) / Ordered
        $netShipped = $unitsShipped - $returns;
        $netFillRate = $totalOrderedUnits > 0 ? ($netShipped / $totalOrderedUnits) * 100 : 100;

        // Return Rate: Returned / Shipped
        $returnRate = $unitsShipped > 0 ? ($returns / $unitsShipped) * 100 : 0;

        // 8. Damage Rate (Wastage Qty / Total Handled Qty)
        // We use handledQty as the base (Good Received + Damaged Received + Returns)
        $totalDamagedInPeriod = ($movements->wastage_entry_qty ?? 0) + ($movements->damaged_plus_stock ?? 0);
        $handledQty = ($movements->received_qty ?? 0) + ($movements->returns_qty ?? 0) + ($movements->damaged_plus_stock ?? 0);
        $wastageQty = (int) ($movements->wastage_entry_qty ?? 0);
        $damageRate = $handledQty > 0 ? ($wastageQty / $handledQty) * 100 : 0;

        // 9. Low Stock SKU Count
        $lowStockCount = DB::table('inventory_levels')
            ->join('products', 'inventory_levels.product_id', '=', 'products.id')
            ->leftJoin('warehouse_stock_limits', function($join) {
                $join->on('inventory_levels.warehouse_id', '=', 'warehouse_stock_limits.warehouse_id')
                     ->on('inventory_levels.product_id', '=', 'warehouse_stock_limits.product_id')
                     ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(warehouse_stock_limits.product_variant_id, 0)');
            })
            ->where('inventory_levels.warehouse_id', $warehouseId)
            ->whereRaw('inventory_levels.current_quantity <= COALESCE(warehouse_stock_limits.min_stock, products.min_stock_global)')
            ->count();

        // 10. Slow-Moving Stock % (SKUs with no sales in the period)
        $totalSKUs = InventoryLevel::where('warehouse_id', $warehouseId)->distinct('product_id', 'product_variant_id')->count();
        $soldSKUs = DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->where('transaction_type', 'SALE')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->distinct('product_id', 'product_variant_id')
            ->count();
        $slowMovingPercent = $totalSKUs > 0 ? (($totalSKUs - $soldSKUs) / $totalSKUs) * 100 : 0;

        // 11. Stock Turnover (COGS / Average Inventory Value)
        $cogs = DB::table('ordered_product_batches')
            ->join('batches', 'ordered_product_batches.batch_id', '=', 'batches.id')
            ->where('batches.warehouse_id', $warehouseId)
            ->whereDate('ordered_product_batches.created_at', '>=', $startDate)
            ->whereDate('ordered_product_batches.created_at', '<=', $endDate)
            ->sum('subtotal_cost');
        
        $avgInventoryValue = $inventoryValue;
        $turnover = $avgInventoryValue > 0 ? ($cogs / $avgInventoryValue) : 0;

        return [
            'opening_stock' => (int) $openingStock,
            'received_qty' => (int) ($movements->received_qty ?? 0),
            'damaged_plus_stock' => (int) ($movements->damaged_plus_stock ?? 0),
            'total_damaged_qty' => (int) $totalDamagedInPeriod,
            'sold_qty' => (int) ($movements->sold_qty ?? 0),
            'returns_qty' => (int) $returns, // Use accurate returns count
            'adjusted_in' => (int) ($movements->adjusted_in ?? 0),
            'adjusted_out' => (int) ($movements->adjusted_out ?? 0),
            'wastage_entry_qty' => (int) ($movements->wastage_entry_qty ?? 0),
            'rtv_qty' => (int) ($movements->rtv_qty ?? 0),
            'saleable_closing' => $saleableClosing,
            'damaged_closing' => $damagedClosing,
            'total_closing_stock' => $totalClosingStock,
            'inventory_value' => (float) $inventoryValue,
            'fulfillment_orders' => (int) ($fulfillment->fulfillment_orders ?? 0),
            'units_shipped' => $unitsShipped,
            'fill_rate' => (float) $grossFillRate,
            'net_fill_rate' => (float) $netFillRate,
            'return_rate' => (float) $returnRate,
            'damage_rate' => (float) $damageRate,
            'low_stock_count' => $lowStockCount,
            'slow_moving_percent' => (float) $slowMovingPercent,
            'stock_turnover' => (float) $turnover,
        ];
    }
}
