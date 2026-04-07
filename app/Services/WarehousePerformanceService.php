<?php

namespace App\Services;

use App\Models\InventoryLevel;
use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
        // 1. Stock Movements from Ledgers (Source of Truth)
        $movements = DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'PO_RECEIPT' THEN change_qty ELSE 0 END) as received_qty,
                SUM(CASE WHEN transaction_type = 'SALE' THEN ABS(change_qty) ELSE 0 END) as sold_qty,
                SUM(CASE WHEN transaction_type = 'STOCK_ADJUSTMENT' AND change_qty > 0 THEN change_qty ELSE 0 END) as adjusted_in,
                SUM(CASE WHEN transaction_type = 'STOCK_ADJUSTMENT' AND change_qty < 0 THEN ABS(change_qty) ELSE 0 END) as adjusted_out,
                SUM(CASE WHEN transaction_type = 'WAREHOUSE_DAMAGE' THEN ABS(change_qty) ELSE 0 END) + 
                SUM(CASE WHEN transaction_type = 'RETURN_DAMAGED' THEN ABS(change_qty) ELSE 0 END) as total_wastage_qty,
                SUM(CASE WHEN transaction_type = 'DAMAGED' THEN change_qty ELSE 0 END) as po_damaged_qty,
                SUM(CASE WHEN transaction_type = 'RTV_DISPATCH' THEN ABS(change_qty) ELSE 0 END) as rtv_qty,
                SUM(CASE WHEN transaction_type IN ('RETURN_INTACT', 'RETURN_DAMAGED') THEN ABS(change_qty) ELSE 0 END) as total_returns_qty,
                COUNT(DISTINCT CASE WHEN transaction_type = 'SALE' THEN reference_id END) as fulfillment_orders
            ")
            ->first();

        // 2. Total Opening Stock (Cumulative history before period)
        $openingStock = DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->whereDate('created_at', '<', $startDate)
            ->sum('change_qty');

        // 3. Closing Stock Snapshots (Ledger Based for perfect reconciliation)
        // Saleable: All time (PO_RECEIPT + RETURN_INTACT + ADJ_IN) - (SALE)
        $saleableClosing = (int) DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->where(function ($q) {
                $q->whereIn('transaction_type', ['PO_RECEIPT', 'RETURN_INTACT', 'SALE'])
                    ->orWhere('transaction_type', 'STOCK_ADJUSTMENT');
            })
            ->sum('change_qty');

        // Damaged Pool: All time (DAMAGED) - (RTV_DISPATCH)
        // Strictly stock that arrived damaged from supplier and is still on-hand.
        $poDamagedClosing = (int) DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->whereIn('transaction_type', ['DAMAGED', 'RTV_DISPATCH'])
            ->sum('change_qty');

        // Current Wastage (Loss of value): Cumulative WAREHOUSE_DAMAGE + RETURN_DAMAGED
        // Stock that was damaged internally or returned damaged by customers.
        $wastageClosing = (int) DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->whereIn('transaction_type', ['WAREHOUSE_DAMAGE', 'RETURN_DAMAGED'])
            ->sum(DB::raw('ABS(change_qty)'));

        $totalClosingStock = $saleableClosing + $poDamagedClosing + $wastageClosing;

        // 4. Inventory Value (Live - Saleable only)
        $inventoryValue = DB::table('inventory_levels')
            ->join('batch_products', function ($join) {
                $join->on('inventory_levels.batch_id', '=', 'batch_products.batch_id')
                    ->on('inventory_levels.product_id', '=', 'batch_products.product_id')
                    ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(batch_products.product_variant_id, 0)');
            })
            ->where('inventory_levels.warehouse_id', $warehouseId)
            ->sum(DB::raw('inventory_levels.current_quantity * batch_products.unit_cost'));

        // 5. Fill Rate Calculations
        $unitsShipped = (int) ($movements->sold_qty ?? 0);
        $totalReturns = (int) ($movements->total_returns_qty ?? 0);

        /**
         * Gross Demand (Initial Shipment Quantity)
         * In this system, the stock_ledger 'SALE' entries represent the initial units shipped from a specific warehouse.
         * We use this as the primary source of truth for Gross Demand because it correctly handles split orders
         * and avoids double-counting at the warehouse level.
         */
        $totalOrderedUnits = (int) $unitsShipped;

        $grossFillRate = $totalOrderedUnits > 0 ? ($unitsShipped / $totalOrderedUnits) * 100 : 100;

        $netShipped = $unitsShipped - $totalReturns;
        $netFillRate = $totalOrderedUnits > 0 ? ($netShipped / $totalOrderedUnits) * 100 : 100;
        $returnRate = $unitsShipped > 0 ? ($totalReturns / $unitsShipped) * 100 : 0;

        // 6. Damage Rate (Total Wastage / Total Inflows)
        $wastageQty = (int) ($movements->total_wastage_qty ?? 0);
        $totalInflows = (int) ($movements->received_qty ?? 0) +
                        (int) ($movements->po_damaged_qty ?? 0) +
                        (int) ($movements->adjusted_in ?? 0) +
                        $totalReturns;

        $damageRate = $totalInflows > 0 ? ($wastageQty / $totalInflows) * 100 : 0;

        // 7. Health Metrics
        $lowStockCount = DB::table('inventory_levels')
            ->join('products', 'inventory_levels.product_id', '=', 'products.id')
            ->leftJoin('warehouse_stock_limits', function ($join) {
                $join->on('inventory_levels.warehouse_id', '=', 'warehouse_stock_limits.warehouse_id')
                    ->on('inventory_levels.product_id', '=', 'warehouse_stock_limits.product_id')
                    ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(warehouse_stock_limits.product_variant_id, 0)');
            })
            ->where('inventory_levels.warehouse_id', $warehouseId)
            ->whereRaw('inventory_levels.current_quantity <= COALESCE(warehouse_stock_limits.min_stock, products.min_stock_global)')
            ->count();

        $totalSKUs = InventoryLevel::where('warehouse_id', $warehouseId)->distinct('product_id', 'product_variant_id')->count();
        $soldSKUs = DB::table('stock_ledgers')
            ->where('warehouse_id', $warehouseId)
            ->where('transaction_type', 'SALE')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->distinct('product_id', 'product_variant_id')
            ->count();
        $slowMovingPercent = $totalSKUs > 0 ? (($totalSKUs - $soldSKUs) / $totalSKUs) * 100 : 0;

        $cogs = DB::table('ordered_product_batches')
            ->join('batches', 'ordered_product_batches.batch_id', '=', 'batches.id')
            ->where('batches.warehouse_id', $warehouseId)
            ->whereDate('ordered_product_batches.created_at', '>=', $startDate)
            ->whereDate('ordered_product_batches.created_at', '<=', $endDate)
            ->sum('subtotal_cost');

        $turnover = $inventoryValue > 0 ? ($cogs / $inventoryValue) : 0;

        return [
            'opening_stock' => (int) $openingStock,
            'received_qty' => (int) ($movements->received_qty ?? 0),
            'po_damaged_qty' => (int) ($movements->po_damaged_qty ?? 0),
            'total_wastage_qty' => $wastageQty,
            'sold_qty' => (int) ($movements->sold_qty ?? 0),
            'returns_qty' => $totalReturns,
            'adjusted_in' => (int) ($movements->adjusted_in ?? 0),
            'adjusted_out' => (int) ($movements->adjusted_out ?? 0),
            'rtv_qty' => (int) ($movements->rtv_qty ?? 0),
            'saleable_closing' => $saleableClosing,
            'po_damaged_closing' => $poDamagedClosing,
            'wastage_closing' => $wastageClosing,
            'total_closing_stock' => $totalClosingStock,
            'inventory_value' => (float) $inventoryValue,
            'fulfillment_orders' => (int) ($movements->fulfillment_orders ?? 0),
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
