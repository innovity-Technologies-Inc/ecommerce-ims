<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get Sales Summary with grouping and filters.
     */
    public function getSalesSummary(array $filters, ?int $perPage = null): array
    {
        // 1. Determine the scope of matching orders
        $orderIdsQuery = Order::query()->select('orders.id');
        $this->applyOrderFilters($orderIdsQuery, $filters);

        // If item level filters exist, we need to restrict orders to those having matching items
        if ($this->hasItemFilters($filters)) {
            $orderIdsQuery->whereExists(function ($q) use ($filters) {
                $q->select(DB::raw(1))
                    ->from('order_items')
                    ->whereColumn('order_items.order_id', 'orders.id');
                $this->applyItemFilters($q, $filters);
            });
        }

        $orderIds = $orderIdsQuery->pluck('id')->toArray();

        if (empty($orderIds)) {
            return [
                'grouped_data' => collect(),
                'totals' => $this->getEmptyTotals(),
                'group_by' => $filters['group_by'] ?? 'daily',
            ];
        }

        // 2. Calculate Order-Level Totals (Shipping, Orders Count)
        $orderTotals = Order::whereIn('id', $orderIds)
            ->selectRaw('
                COUNT(id) as orders_count,
                SUM(shipping_charge) as shipping_revenue
            ')
            ->first();

        // 3. Calculate Item-Level Totals (Sales, Cost, Units)
        $itemTotalsQuery = OrderItem::whereIn('order_items.order_id', $orderIds);
        $this->applyItemFilters($itemTotalsQuery, $filters);

        $itemTotals = $itemTotalsQuery->selectRaw('
            SUM(order_items.quantity) as units_sold,
            SUM(order_items.total_price) as net_sales,
            SUM(order_items.regular_price * order_items.quantity) as gross_sales,
            SUM((order_items.product_discount + order_items.coupon_discount) * order_items.quantity) as discount_amount,
            SUM(order_items.total_cost) as total_cost,
            SUM(order_items.total_price - order_items.total_cost) as gross_profit
        ')->first();

        // 4. Calculate Grouped Data for Trend Table
        $grouping = $filters['group_by'] ?? 'daily';
        $selectRaw = $this->getGroupingRaw($grouping);

        // Trend data should reflect the FILTERED items
        $trendQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.id', $orderIds);
        $this->applyItemFilters($trendQuery, $filters);

        $trendQuery->selectRaw("
                $selectRaw as period,
                COUNT(DISTINCT orders.id) as orders_count,
                SUM(order_items.total_price) as net_sales,
                SUM(order_items.total_cost) as total_cost,
                SUM(order_items.total_price - order_items.total_cost) as gross_profit
            ")
            ->groupBy('period')
            ->orderBy('period', 'desc');

        $groupedData = $perPage ? $trendQuery->paginate($perPage)->withQueryString() : $trendQuery->get();

        // Final Totals
        $totals = [
            'orders_count' => $orderTotals->orders_count ?? 0,
            'units_sold' => $itemTotals->units_sold ?? 0,
            'net_sales' => $itemTotals->net_sales ?? 0,
            'gross_sales' => $itemTotals->gross_sales ?? 0,
            'discount_amount' => $itemTotals->discount_amount ?? 0,
            'shipping_revenue' => $orderTotals->shipping_revenue ?? 0,
            'total_cost' => $itemTotals->total_cost ?? 0,
            'gross_profit' => $itemTotals->gross_profit ?? 0,
        ];

        $totals['aov'] = $totals['orders_count'] > 0 ? $totals['net_sales'] / $totals['orders_count'] : 0;
        $totals['gross_margin_percent'] = $totals['net_sales'] > 0 ? ($totals['gross_profit'] / $totals['net_sales']) * 100 : 0;

        return [
            'grouped_data' => $groupedData,
            'totals' => $totals,
            'group_by' => $grouping,
        ];
    }

    /**
     * Get Sales Breakdown by Entity (Product, Variant, Warehouse, etc.)
     */
    public function getSalesByEntity(string $entity, array $filters, ?int $perPage = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        $query = OrderItem::query()->join('orders', 'order_items.order_id', '=', 'orders.id');

        // Apply filters
        $this->applyOrderFilters($query, $filters, 'orders.');
        $this->applyItemFilters($query, $filters);

        switch ($entity) {
            case 'product':
                $query->selectRaw('
                    order_items.product_name as name,
                    SUM(order_items.quantity) as units_sold,
                    SUM(order_items.total_price) as net_sales,
                    SUM(order_items.total_cost) as total_cost,
                    SUM(order_items.total_price - order_items.total_cost) as gross_profit
                ')
                    ->groupBy('order_items.product_id', 'order_items.product_name');
                break;

            case 'variant':
                $query->selectRaw("
                    CONCAT(order_items.product_name, ' (', COALESCE(order_items.variant_name, 'No Variant'), ')') as name,
                    SUM(order_items.quantity) as units_sold,
                    SUM(order_items.total_price) as net_sales,
                    SUM(order_items.total_cost) as total_cost,
                    SUM(order_items.total_price - order_items.total_cost) as gross_profit
                ")
                    ->groupBy('order_items.product_id', 'order_items.product_variant_id', 'order_items.product_name', 'order_items.variant_name');
                break;

            case 'warehouse':
                $query->join('ordered_product_batches', 'order_items.id', '=', 'ordered_product_batches.order_item_id')
                    ->join('batches', 'ordered_product_batches.batch_id', '=', 'batches.id')
                    ->join('warehouses', 'batches.warehouse_id', '=', 'warehouses.id')
                    ->selectRaw('
                        warehouses.name as name,
                        SUM(ordered_product_batches.quantity) as units_sold,
                        SUM(ordered_product_batches.quantity * order_items.unit_price) as net_sales,
                        SUM(ordered_product_batches.subtotal_cost) as total_cost,
                        SUM((ordered_product_batches.quantity * order_items.unit_price) - ordered_product_batches.subtotal_cost) as gross_profit
                    ')
                    ->groupBy('warehouses.id', 'warehouses.name');
                break;

            case 'batch':
                $query->join('ordered_product_batches', 'order_items.id', '=', 'ordered_product_batches.order_item_id')
                    ->join('batches', 'ordered_product_batches.batch_id', '=', 'batches.id')
                    ->selectRaw('
                        batches.batch_number as name,
                        SUM(ordered_product_batches.quantity) as units_sold,
                        SUM(ordered_product_batches.quantity * order_items.unit_price) as net_sales,
                        SUM(ordered_product_batches.subtotal_cost) as total_cost,
                        SUM((ordered_product_batches.quantity * order_items.unit_price) - ordered_product_batches.subtotal_cost) as gross_profit
                    ')
                    ->groupBy('batches.id', 'batches.batch_number');
                break;

            case 'payment_method':
                $query->selectRaw('
                    orders.payment_method as name,
                    COUNT(DISTINCT orders.id) as orders_count,
                    SUM(order_items.total_price) as net_sales
                ')
                    ->groupBy('orders.payment_method');
                break;
        }

        $query->orderBy('net_sales', 'desc');

        return $perPage ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function getInventoryReport(array $filters, ?string $entity = null, ?int $perPage = null): array
    {
        $endDate = ! empty($filters['end_date']) ? $filters['end_date'] : null;
        $includeDamaged = ($filters['include_damaged'] ?? 'no') === 'yes';

        // 1. Core Query for Current or Historical Stock
        if ($endDate) {
            $data = $this->getHistoricalInventory($filters, $endDate, $includeDamaged, $entity, $perPage);
        } else {
            $data = $this->getCurrentInventory($filters, $includeDamaged, $entity, $perPage);
        }

        // If we are in entity mode (Detailed View), we return the data directly
        if ($entity) {
            return ['data' => $data];
        }

        // Summary Totals
        $totals = [
            'total_items' => $data->unique('product_id')->count(),
            'total_quantity' => $data->sum('quantity'),
            'total_valuation' => $data->sum('valuation'),
        ];

        // Entity Breakdowns (limited to 10 for dashboard)
        $dataWithStock = $data->filter(fn ($item) => $item->quantity > 0);

        $breakdowns = [
            'warehouse' => $dataWithStock->groupBy('warehouse_name')->map(fn ($group) => [
                'name' => $group->first()->warehouse_name,
                'quantity' => $group->sum('quantity'),
                'valuation' => $group->sum('valuation'),
            ])->sortByDesc('valuation')->take(10),

            'product' => $dataWithStock->groupBy('product_name')->map(fn ($group) => [
                'name' => $group->first()->product_name,
                'quantity' => $group->sum('quantity'),
                'valuation' => $group->sum('valuation'),
            ])->sortByDesc('valuation')->take(10),

            'batch' => $dataWithStock->groupBy(fn ($item) => $item->batch_id.'-'.$item->product_id)
                ->map(fn ($group) => [
                    'name' => $group->first()->batch_number,
                    'warehouse' => $group->first()->warehouse_name,
                    'product' => $group->first()->product_name,
                    'quantity' => $group->sum('quantity'),
                    'unit_cost' => $group->avg('unit_cost'),
                    'valuation' => $group->sum('valuation'),
                ])->sortByDesc('valuation')->take(10),
        ];

        return [
            'totals' => $totals,
            'breakdowns' => $breakdowns,
            'raw_data' => $data,
        ];
    }

    /**
     * Detailed Stock Report with Movements, Aging, etc.
     */
    public function getStockReport(array $filters, ?int $perPage = null)
    {
        $lastMovementSub = DB::table('stock_ledgers')
            ->select('batch_id', 'product_id', 'product_variant_id', DB::raw('MAX(created_at) as last_move'))
            ->groupBy('batch_id', 'product_id', 'product_variant_id');

        $query = DB::table('inventory_levels')
            ->join('batches', 'inventory_levels.batch_id', '=', 'batches.id')
            ->join('warehouses', 'inventory_levels.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('suppliers', 'batches.supplier_id', '=', 'suppliers.id')
            ->join('products', 'inventory_levels.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'inventory_levels.product_variant_id', '=', 'product_variants.id')
            ->join('batch_products', function ($join) {
                $join->on('inventory_levels.batch_id', '=', 'batch_products.batch_id')
                    ->on('inventory_levels.product_id', '=', 'batch_products.product_id')
                    ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(batch_products.product_variant_id, 0)');
            })
            ->leftJoinSub($lastMovementSub, 'movements', function ($join) {
                $join->on('inventory_levels.batch_id', '=', 'movements.batch_id')
                    ->on('inventory_levels.product_id', '=', 'movements.product_id')
                    ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(movements.product_variant_id, 0)');
            })
            ->leftJoin('warehouse_stock_limits', function ($join) {
                $join->on('inventory_levels.warehouse_id', '=', 'warehouse_stock_limits.warehouse_id')
                    ->on('inventory_levels.product_id', '=', 'warehouse_stock_limits.product_id')
                    ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(warehouse_stock_limits.product_variant_id, 0)');
            });

        $groupBy = $filters['group_by'] ?? 'batch';

        if ($groupBy === 'warehouse') {
            $query->select(
                'warehouses.name as name',
                DB::raw('SUM(inventory_levels.current_quantity) as quantity'),
                DB::raw('SUM(inventory_levels.damaged_quantity) as damaged_quantity'),
                DB::raw('SUM(inventory_levels.current_quantity * batch_products.unit_cost) as valuation')
            )->groupBy('warehouses.id', 'warehouses.name')->orderBy('valuation', 'desc')->orderBy('warehouses.id', 'desc');
        } elseif ($groupBy === 'product') {
            $query->select(
                'products.name as name',
                DB::raw('SUM(inventory_levels.current_quantity) as quantity'),
                DB::raw('SUM(inventory_levels.damaged_quantity) as damaged_quantity'),
                DB::raw('SUM(inventory_levels.current_quantity * batch_products.unit_cost) as valuation')
            )->groupBy('inventory_levels.product_id', 'products.name')->orderBy('valuation', 'desc')->orderBy('inventory_levels.product_id', 'desc');
        } else {
            $query->select(
                'warehouses.name as warehouse_name',
                'products.name as product_name',
                DB::raw("'N/A' as variant_name"),
                DB::raw('MAX(product_variants.sku) as sku'),
                'batches.batch_number',
                'suppliers.name as supplier_name',
                DB::raw('SUM(inventory_levels.current_quantity) as current_quantity'),
                DB::raw('SUM(inventory_levels.damaged_quantity) as damaged_quantity'),
                DB::raw('AVG(batch_products.unit_cost) as unit_cost'),
                DB::raw('MAX(movements.last_move) as last_move'),
                DB::raw('SUM(COALESCE(warehouse_stock_limits.min_stock, products.min_stock_global)) as min_threshold'),
                DB::raw('SUM(inventory_levels.current_quantity * batch_products.unit_cost) as inventory_value'),
                DB::raw('IF(SUM(inventory_levels.current_quantity) <= SUM(COALESCE(warehouse_stock_limits.min_stock, products.min_stock_global)), 1, 0) as is_low_stock')
            )->groupBy('inventory_levels.warehouse_id', 'inventory_levels.product_id', 'inventory_levels.batch_id', 'warehouses.name', 'products.name', 'batches.batch_number', 'suppliers.name')
                ->orderBy('inventory_value', 'desc')
                ->orderBy('inventory_levels.batch_id', 'desc');
        }

        $this->applyStockFilters($query, $filters);

        if (! empty($filters['low_stock_only']) && $filters['low_stock_only'] === 'yes') {
            $query->whereRaw('inventory_levels.current_quantity <= COALESCE(warehouse_stock_limits.min_stock, products.min_stock_global)');
        }

        if ($perPage) {
            return $query->paginate($perPage)->withQueryString();
        }

        return $query->get();
    }

    public function getWastageBreakdown(string $entity, array $filters, ?int $perPage = null)
    {
        $query = DB::table('batch_serials')
            ->join('products', 'batch_serials.product_id', '=', 'products.id')
            ->join('warehouses', 'batch_serials.warehouse_id', '=', 'warehouses.id')
            ->join('batches', 'batch_serials.batch_id', '=', 'batches.id')
            ->where('batch_serials.stock_status', 'wastage');

        if ($entity === 'product') {
            $query->select(
                'products.name as name',
                DB::raw('COUNT(batch_serials.id) as quantity')
            )->groupBy('batch_serials.product_id', 'products.name')->orderBy('quantity', 'desc')->orderBy('batch_serials.product_id', 'desc');
        } elseif ($entity === 'warehouse') {
            $query->select(
                'warehouses.name as name',
                DB::raw('COUNT(batch_serials.id) as quantity')
            )->groupBy('batch_serials.warehouse_id', 'warehouses.name')->orderBy('quantity', 'desc')->orderBy('batch_serials.warehouse_id', 'desc');
        } else {
            $query->select(
                'batches.batch_number as name',
                DB::raw('COUNT(batch_serials.id) as quantity')
            )->groupBy('batch_serials.batch_id', 'batches.batch_number')->orderBy('quantity', 'desc')->orderBy('batch_serials.batch_id', 'desc');
        }

        if ($perPage) {
            return $query->paginate($perPage)->withQueryString();
        }

        return $query->get();
    }

    public function getStockMovements(array $filters, ?int $perPage = null)
    {
        $query = DB::table('stock_ledgers')
            ->join('products', 'stock_ledgers.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'stock_ledgers.product_variant_id', '=', 'product_variants.id')
            ->join('warehouses', 'stock_ledgers.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('batches', 'stock_ledgers.batch_id', '=', 'batches.id')
            ->select(
                'stock_ledgers.*',
                'products.name as product_name',
                'product_variants.variant_name',
                'warehouses.name as warehouse_name',
                'batches.batch_number'
            );

        if (! empty($filters['start_date'])) {
            $query->whereDate('stock_ledgers.created_at', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('stock_ledgers.created_at', '<=', $filters['end_date']);
        }
        if (! empty($filters['warehouse_id'])) {
            $query->where('stock_ledgers.warehouse_id', $filters['warehouse_id']);
        }
        if (! empty($filters['product_id'])) {
            $query->where('stock_ledgers.product_id', $filters['product_id']);
        }

        $query->orderBy('stock_ledgers.created_at', 'desc')->orderBy('stock_ledgers.id', 'desc');

        return $perPage ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function getBatchAging(array $filters, ?int $perPage = null)
    {
        $query = DB::table('batches')
            ->leftJoin('suppliers', 'batches.supplier_id', '=', 'suppliers.id')
            ->join('warehouses', 'batches.warehouse_id', '=', 'warehouses.id')
            ->join('batch_products', 'batches.id', '=', 'batch_products.batch_id')
            ->where(DB::raw('batch_products.saleable_qty + batch_products.damaged_qty'), '>', 0)
            ->select(
                'batches.*',
                'suppliers.name as supplier_name',
                'warehouses.name as warehouse_name',
                DB::raw('DATEDIFF(NOW(), batches.created_at) as age_days')
            )
            ->groupBy('batches.id');

        if (! empty($filters['warehouse_id'])) {
            $query->where('batches.warehouse_id', $filters['warehouse_id']);
        }
        if (! empty($filters['supplier_id'])) {
            $query->where('batches.supplier_id', $filters['supplier_id']);
        }

        $query->orderBy('batches.created_at', 'asc')->orderBy('batches.id', 'asc');

        return $perPage ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function getSerialTrace(array $filters, ?int $perPage = null)
    {
        $query = DB::table('batch_serials')
            ->join('products', 'batch_serials.product_id', '=', 'products.id')
            ->join('batches', 'batch_serials.batch_id', '=', 'batches.id')
            ->select(
                'batch_serials.*',
                'products.name as product_name',
                'batches.batch_number'
            );

        if (! empty($filters['batch_id'])) {
            $query->where('batch_serials.batch_id', $filters['batch_id']);
        }
        if (! empty($filters['warehouse_id'])) {
            $query->where('batches.warehouse_id', $filters['warehouse_id']);
        }
        if (! empty($filters['supplier_id'])) {
            $query->where('batches.supplier_id', $filters['supplier_id']);
        }
        if (! empty($filters['product_id'])) {
            $query->where('batch_serials.product_id', $filters['product_id']);
        }
        if (! empty($filters['serial_no'])) {
            $query->where('batch_serials.serial_no', 'like', '%'.$filters['serial_no'].'%');
        }

        $query->orderBy('batch_serials.updated_at', 'desc')->orderBy('batch_serials.id', 'desc');

        return $perPage ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    protected function applyStockFilters($query, array $filters)
    {
        if (! empty($filters['warehouse_id'])) {
            $query->where('inventory_levels.warehouse_id', $filters['warehouse_id']);
        }
        if (! empty($filters['supplier_id'])) {
            $query->where('batches.supplier_id', $filters['supplier_id']);
        }
        if (! empty($filters['product_id'])) {
            $query->where('inventory_levels.product_id', $filters['product_id']);
        }
        if (! empty($filters['category_id'])) {
            $query->where('products.category_id', $filters['category_id']);
        }
        if (! empty($filters['brand_id'])) {
            $query->where('products.brand_id', $filters['brand_id']);
        }
        if (! empty($filters['batch_number'])) {
            $query->where('batches.batch_number', 'like', '%'.$filters['batch_number'].'%');
        }
    }

    protected function getCurrentInventory(array $filters, bool $includeDamaged, ?string $entity = null, ?int $perPage = null)
    {
        $query = DB::table('batch_products')
            ->join('batches', 'batch_products.batch_id', '=', 'batches.id')
            ->join('warehouses', 'batches.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'batch_products.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'batch_products.product_variant_id', '=', 'product_variants.id');

        if ($entity === 'warehouse') {
            $query->select(
                'warehouses.name as name',
                DB::raw('SUM(batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').') as quantity'),
                DB::raw('SUM((batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').') * batch_products.unit_cost) as valuation')
            )->groupBy('warehouses.id', 'warehouses.name')->orderBy('valuation', 'desc');
        } elseif ($entity === 'product') {
            $query->select(
                'products.name as name',
                DB::raw('SUM(batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').') as quantity'),
                DB::raw('SUM((batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').') * batch_products.unit_cost) as valuation')
            )->groupBy('batch_products.product_id', 'products.name')->orderBy('valuation', 'desc');
        } elseif ($entity === 'batch') {
            $query->select(
                'batches.batch_number as name',
                'warehouses.name as warehouse',
                'products.name as product',
                DB::raw('SUM(batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').') as quantity'),
                DB::raw('AVG(batch_products.unit_cost) as unit_cost'), // Weighted average would be better but AVG is simpler for now
                DB::raw('SUM((batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').') * batch_products.unit_cost) as valuation')
            )->groupBy('batches.id', 'batches.batch_number', 'warehouses.name', 'batch_products.product_id', 'products.name')
                ->orderBy('batches.id', 'desc');
        } else {
            $query->select(
                'batch_products.product_id',
                'batches.id as batch_id',
                'batches.batch_number',
                'warehouses.name as warehouse_name',
                'products.name as product_name',
                'batch_products.unit_cost',
                DB::raw('batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').' as quantity'),
                DB::raw('(batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '').') * batch_products.unit_cost as valuation')
            );
        }

        $this->applyInventoryFilters($query, $filters);

        if ($entity) {
            $query->where(DB::raw('batch_products.saleable_qty'.($includeDamaged ? ' + batch_products.damaged_qty' : '')), '>', 0);

            return $perPage ? $query->paginate($perPage)->withQueryString() : $query->get();
        }

        return $query->get();
    }

    protected function getHistoricalInventory(array $filters, string $date, bool $includeDamaged, ?string $entity = null, ?int $perPage = null)
    {
        $subQuery = DB::table('stock_ledgers')
            ->whereDate('created_at', '<=', $date)
            ->select(
                'batch_id',
                'product_id',
                'product_variant_id',
                DB::raw('SUM(change_qty) as historical_qty')
            )
            ->groupBy('batch_id', 'product_id', 'product_variant_id');

        $query = DB::table('batch_products')
            ->join('batches', 'batch_products.batch_id', '=', 'batches.id')
            ->join('warehouses', 'batches.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'batch_products.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'batch_products.product_variant_id', '=', 'product_variants.id')
            ->joinSub($subQuery, 'ledger_sums', function ($join) {
                $join->on('batch_products.batch_id', '=', 'ledger_sums.batch_id')
                    ->on('batch_products.product_id', '=', 'ledger_sums.product_id');
            });

        if ($entity === 'warehouse') {
            $query->select(
                'warehouses.name as name',
                DB::raw('SUM(ledger_sums.historical_qty) as quantity'),
                DB::raw('SUM(ledger_sums.historical_qty * batch_products.unit_cost) as valuation')
            )->groupBy('warehouses.id', 'warehouses.name')->orderBy('valuation', 'desc');
        } elseif ($entity === 'product') {
            $query->select(
                'products.name as name',
                DB::raw('SUM(ledger_sums.historical_qty) as quantity'),
                DB::raw('SUM(ledger_sums.historical_qty * batch_products.unit_cost) as valuation')
            )->groupBy('batch_products.product_id', 'products.name')->orderBy('valuation', 'desc');
        } elseif ($entity === 'batch') {
            $query->select(
                'batches.batch_number as name',
                'warehouses.name as warehouse',
                'products.name as product',
                DB::raw('SUM(ledger_sums.historical_qty) as quantity'),
                DB::raw('AVG(batch_products.unit_cost) as unit_cost'),
                DB::raw('SUM(ledger_sums.historical_qty * batch_products.unit_cost) as valuation')
            )->groupBy('batches.id', 'batches.batch_number', 'warehouses.name', 'batch_products.product_id', 'products.name')
                ->orderBy('batches.id', 'desc');
        } else {
            $query->select(
                'batch_products.product_id',
                'batches.id as batch_id',
                'batches.batch_number',
                'warehouses.name as warehouse_name',
                'products.name as product_name',
                'batch_products.unit_cost',
                'ledger_sums.historical_qty as quantity',
                DB::raw('(ledger_sums.historical_qty * batch_products.unit_cost) as valuation')
            );
        }

        $this->applyInventoryFilters($query, $filters);

        if ($entity) {
            $query->where('ledger_sums.historical_qty', '>', 0);

            return $perPage ? $query->paginate($perPage)->withQueryString() : $query->get();
        }

        return $query->get();
    }

    protected function applyOrderFilters($query, array $filters, string $prefix = ''): void
    {
        if (! empty($filters['start_date'])) {
            $query->whereDate($prefix.'created_at', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate($prefix.'created_at', '<=', $filters['end_date']);
        }
        if (! empty($filters['order_status'])) {
            $query->where($prefix.'order_status', $filters['order_status']);
        }
        if (! empty($filters['payment_status'])) {
            $query->where($prefix.'payment_status', $filters['payment_status']);
        }
        if (! empty($filters['payment_method'])) {
            $query->where($prefix.'payment_method', $filters['payment_method']);
        }

        // Entity level filters
        if (! empty($filters['warehouse_id'])) {
            $sql = $query->toSql();
            if (str_contains($sql, 'ordered_product_batches')) {
                $query->where('batches.warehouse_id', $filters['warehouse_id']);
            } else {
                $query->whereExists(function ($q) use ($filters, $prefix) {
                    $q->select(DB::raw(1))
                        ->from('ordered_product_batches')
                        ->join('batches', 'ordered_product_batches.batch_id', '=', 'batches.id')
                        ->whereColumn('ordered_product_batches.order_id', '=', (! empty($prefix) ? $prefix.'id' : 'orders.id'))
                        ->where('batches.warehouse_id', $filters['warehouse_id']);
                });
            }
        }
    }

    protected function applyItemFilters($query, array $filters): void
    {
        if (! empty($filters['product_id'])) {
            $query->where('order_items.product_id', $filters['product_id']);
        }
        if (! empty($filters['product_variant_id'])) {
            $query->where('order_items.product_variant_id', $filters['product_variant_id']);
        }
        if (! empty($filters['category_id'])) {
            if (! str_contains($query->toSql(), 'products')) {
                $query->join('products', 'order_items.product_id', '=', 'products.id');
            }
            $query->where('products.category_id', $filters['category_id']);
        }
        if (! empty($filters['brand_id'])) {
            if (! str_contains($query->toSql(), 'products')) {
                $query->join('products', 'order_items.product_id', '=', 'products.id');
            }
            $query->where('products.brand_id', $filters['brand_id']);
        }
    }

    protected function hasItemFilters(array $filters): bool
    {
        return ! empty($filters['product_id']) ||
               ! empty($filters['product_variant_id']) ||
               ! empty($filters['category_id']) ||
               ! empty($filters['brand_id']);
    }

    protected function getEmptyTotals(): array
    {
        return [
            'orders_count' => 0, 'units_sold' => 0, 'net_sales' => 0, 'gross_sales' => 0,
            'discount_amount' => 0, 'shipping_revenue' => 0, 'total_cost' => 0, 'gross_profit' => 0,
            'aov' => 0, 'gross_margin_percent' => 0,
        ];
    }

    protected function getGroupingRaw(string $grouping): string
    {
        return match ($grouping) {
            'weekly' => "DATE_FORMAT(DATE_SUB(orders.created_at, INTERVAL WEEKDAY(orders.created_at) DAY), '%Y-%m-%d')",
            'monthly' => "DATE_FORMAT(orders.created_at, '%Y-%m')",
            'yearly' => "DATE_FORMAT(orders.created_at, '%Y')",
            default => "DATE_FORMAT(orders.created_at, '%Y-%m-%d')", // daily
        };
    }

    protected function applyInventoryFilters($query, array $filters): void
    {
        if (! empty($filters['warehouse_id'])) {
            $query->where('batches.warehouse_id', $filters['warehouse_id']);
        }
        if (! empty($filters['supplier_id'])) {
            $query->where('batches.supplier_id', $filters['supplier_id']);
        }
        if (! empty($filters['product_id'])) {
            $query->where('batch_products.product_id', $filters['product_id']);
        }
        if (! empty($filters['category_id'])) {
            $query->where('products.category_id', $filters['category_id']);
        }
        if (! empty($filters['brand_id'])) {
            $query->where('products.brand_id', $filters['brand_id']);
        }
        if (! empty($filters['batch_number'])) {
            $query->where('batches.batch_number', 'like', '%'.$filters['batch_number'].'%');
        }
    }
}
