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
    public function getSalesSummary(array $filters): array
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

        $groupedData = $trendQuery->selectRaw("
                $selectRaw as period,
                COUNT(DISTINCT orders.id) as orders_count,
                SUM(order_items.total_price) as net_sales,
                SUM(order_items.total_cost) as total_cost,
                SUM(order_items.total_price - order_items.total_cost) as gross_profit
            ")
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

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
    public function getSalesByEntity(string $entity, array $filters): \Illuminate\Support\Collection
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

        return $query->orderBy('net_sales', 'desc')->limit(20)->get();
    }

    /**
     * Apply common filters to the query.
     */
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
            // Check if we already have ordered_product_batches joined (for entities)
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

    /**
     * Apply item level filters.
     */
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

    /**
     * Get Inventory Report with valuation and filters.
     */
    public function getInventoryReport(array $filters): array
    {
        $asOfDate = ! empty($filters['as_of_date']) ? $filters['as_of_date'] : null;
        $includeDamaged = ($filters['include_damaged'] ?? 'no') === 'yes';

        // 1. Core Query for Current or Historical Stock
        if ($asOfDate) {
            $data = $this->getHistoricalInventory($filters, $asOfDate, $includeDamaged);
        } else {
            $data = $this->getCurrentInventory($filters, $includeDamaged);
        }

        // 2. Summary Totals
        $totals = [
            'total_items' => $data->unique('product_id')->count(),
            'total_quantity' => $data->sum('quantity'),
            'total_valuation' => $data->sum('valuation'),
        ];

        // 3. Entity Breakdowns
        $breakdowns = [
            'warehouse' => $data->groupBy('warehouse_name')->map(fn ($group) => [
                'name' => $group->first()->warehouse_name,
                'quantity' => $group->sum('quantity'),
                'valuation' => $group->sum('valuation'),
            ])->sortByDesc('valuation'),

            'product' => $data->groupBy('product_name')->map(fn ($group) => [
                'name' => $group->first()->product_name,
                'quantity' => $group->sum('quantity'),
                'valuation' => $group->sum('valuation'),
            ])->sortByDesc('valuation')->take(50),

            'batch' => $data->sortByDesc('batch_id')->map(fn ($item) => [
                'name' => $item->batch_number,
                'warehouse' => $item->warehouse_name,
                'product' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_cost' => $item->unit_cost,
                'valuation' => $item->valuation,
            ])->take(50),
        ];

        return [
            'totals' => $totals,
            'breakdowns' => $breakdowns,
            'raw_data' => $data,
        ];
    }

    /**
     * Get Current Inventory from batch_products and warehouses.
     */
    protected function getCurrentInventory(array $filters, bool $includeDamaged): \Illuminate\Support\Collection
    {
        $query = DB::table('batch_products')
            ->join('batches', 'batch_products.batch_id', '=', 'batches.id')
            ->join('warehouses', 'batches.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'batch_products.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'batch_products.product_variant_id', '=', 'product_variants.id')
            ->select(
                'batch_products.product_id',
                'batches.id as batch_id',
                'batches.batch_number',
                'warehouses.name as warehouse_name',
                DB::raw("CONCAT(products.name, IF(product_variants.variant_name IS NOT NULL, CONCAT(' (', product_variants.variant_name, ')'), '')) as product_name"),
                'batch_products.unit_cost'
            );

        // Quantity Selection
        if ($includeDamaged) {
            $query->addSelect(DB::raw('(batch_products.saleable_qty + batch_products.damaged_qty) as quantity'));
            $query->addSelect(DB::raw('((batch_products.saleable_qty + batch_products.damaged_qty) * batch_products.unit_cost) as valuation'));
        } else {
            $query->addSelect('batch_products.saleable_qty as quantity');
            $query->addSelect(DB::raw('(batch_products.saleable_qty * batch_products.unit_cost) as valuation'));
        }

        $this->applyInventoryFilters($query, $filters);

        return $query->get();
    }

    /**
     * Calculate Inventory as of a specific date using Stock Ledgers.
     */
    protected function getHistoricalInventory(array $filters, string $date, bool $includeDamaged): \Illuminate\Support\Collection
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
            })
            ->select(
                'batch_products.product_id',
                'batches.id as batch_id',
                'batches.batch_number',
                'warehouses.name as warehouse_name',
                DB::raw("CONCAT(products.name, IF(product_variants.variant_name IS NOT NULL, CONCAT(' (', product_variants.variant_name, ')'), '')) as product_name"),
                'batch_products.unit_cost',
                'ledger_sums.historical_qty as quantity',
                DB::raw('(ledger_sums.historical_qty * batch_products.unit_cost) as valuation')
            );

        $this->applyInventoryFilters($query, $filters);

        return $query->get();
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

    protected function getEmptyTotals(): array
    {
        return [
            'orders_count' => 0, 'units_sold' => 0, 'net_sales' => 0, 'gross_sales' => 0,
            'discount_amount' => 0, 'shipping_revenue' => 0, 'total_cost' => 0, 'gross_profit' => 0,
            'aov' => 0, 'gross_margin_percent' => 0,
        ];
    }

    /**
     * Get the SQL raw for grouping by date.
     */
    protected function getGroupingRaw(string $grouping): string
    {
        return match ($grouping) {
            'weekly' => "DATE_FORMAT(DATE_SUB(orders.created_at, INTERVAL WEEKDAY(orders.created_at) DAY), '%Y-%m-%d')",
            'monthly' => "DATE_FORMAT(orders.created_at, '%Y-%m')",
            'yearly' => "DATE_FORMAT(orders.created_at, '%Y')",
            default => "DATE_FORMAT(orders.created_at, '%Y-%m-%d')", // daily
        };
    }
}
