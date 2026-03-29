<?php

namespace App\Services;

use App\Models\InventoryLevel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get summary metrics for the dashboard cards.
     */
    public function getSummaryMetrics(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        $thisMonthSales = Order::where('created_at', '>=', $startOfMonth)
            ->where('order_status', 'Delivered')
            ->sum('total_amount');

        $thisYearSales = Order::where('created_at', '>=', $startOfYear)
            ->where('order_status', 'Delivered')
            ->sum('total_amount');

        $totalSalesAmount = Order::where('order_status', 'Delivered')
            ->sum('total_amount');

        $totalProductSalesCount = OrderItem::whereHas('order', function ($query) {
            $query->where('order_status', 'Delivered');
        })->sum('quantity');

        $totalProducts = Product::count();

        $thisMonthOrdersCount = Order::where('created_at', '>=', $startOfMonth)
            ->whereNotIn('order_status', ['Cancelled', 'Rejected'])
            ->count();

        $totalCustomers = User::count();

        $pendingOrdersCount = Order::where('order_status', 'Pending')
            ->count();

        // Count Global Low Stock
        // Products/Variants where type is 'global' and total stock <= min_stock_global
        $globalLowStockCount = ProductVariant::where('min_stock_type', 'global')
            ->whereColumn('stock', '<=', 'min_stock_global')
            ->count();

        // Count Warehouse Low Stock
        // Inventory levels where product/variant type is 'warehouse' and current_quantity <= min_stock_override
        $warehouseLowStockCount = InventoryLevel::whereNotNull('min_stock_override')
            ->where(function($query) {
                $query->whereHas('variant', function($q) {
                    $q->where('min_stock_type', 'warehouse');
                })->orWhere(function($q) {
                    $q->whereNull('product_variant_id')
                      ->whereHas('product', function($pq) {
                          $pq->where('min_stock_type', 'warehouse');
                      });
                });
            })
            ->whereColumn('current_quantity', '<=', 'min_stock_override')
            ->count();

        $lowStockCount = $globalLowStockCount + $warehouseLowStockCount;

        return [
            'thisMonthSales' => $thisMonthSales,
            'thisYearSales' => $thisYearSales,
            'totalSalesAmount' => $totalSalesAmount,
            'totalProductSalesCount' => (int) $totalProductSalesCount,
            'totalProducts' => $totalProducts,
            'thisMonthOrdersCount' => $thisMonthOrdersCount,
            'totalCustomers' => $totalCustomers,
            'pendingOrdersCount' => $pendingOrdersCount,
            'lowStockCount' => $lowStockCount,
        ];
    }

    /**
     * Get sales data for the monthly chart of the current year.
     */
    public function getMonthlySalesData(): array
    {
        $year = Carbon::now()->year;

        $sales = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', $year)
            ->where('order_status', 'Delivered')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $data = [];
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M');
            $data[] = $sales[$i] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get sales data for the last 5 years.
     */
    public function getYearlySalesData(): array
    {
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 4;

        $sales = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', '>=', $startYear)
            ->where('order_status', 'Delivered')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->pluck('total', 'year')
            ->toArray();

        $data = [];
        $labels = [];
        for ($i = $startYear; $i <= $currentYear; $i++) {
            $labels[] = (string) $i;
            $data[] = $sales[$i] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get monthly best selling products.
     */
    public function getMonthlyBestSellingProducts(int $limit = 5)
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return Product::with(['primaryImage', 'category'])
            ->select('products.*', DB::raw('SUM(order_items.quantity) as period_sales_count'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.order_status', 'Delivered')
            ->where('orders.created_at', '>=', $startOfMonth)
            ->groupBy('products.id')
            ->orderBy('period_sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get yearly best selling products.
     */
    public function getYearlyBestSellingProducts(int $limit = 5)
    {
        $startOfYear = Carbon::now()->startOfYear();

        return Product::with(['primaryImage', 'category'])
            ->select('products.*', DB::raw('SUM(order_items.quantity) as period_sales_count'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.order_status', 'Delivered')
            ->where('orders.created_at', '>=', $startOfYear)
            ->groupBy('products.id')
            ->orderBy('period_sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get best selling products with pagination and advanced filtering.
     */
    public function getBestSellingProductsPaged(array $params = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        // Use a simplified join to avoid column name collisions (like 'name') with FlexSearch
        $query = Product::with(['primaryImage', 'category', 'brand'])
            ->select('products.*', DB::raw('SUM(order_items.quantity) as period_sales_count'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join(DB::raw('(SELECT id, order_status, created_at FROM orders) as orders'), 'order_items.order_id', '=', 'orders.id')
            ->where('orders.order_status', 'Delivered');

        // Advanced Product Filters
        $filters = [];
        if (! empty($params['category_id'])) {
            $filters['category_id'] = $params['category_id'];
        }
        if (! empty($params['brand_id'])) {
            $filters['brand_id'] = $params['brand_id'];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $filters['status'] = $params['status'];
        }

        // Time / Date Range Filtering
        $period = $params['period'] ?? 'all_time';
        if ($period === 'monthly') {
            $query->where('orders.created_at', '>=', Carbon::now()->startOfMonth());
        } elseif ($period === 'yearly') {
            $query->where('orders.created_at', '>=', Carbon::now()->startOfYear());
        } elseif ($period === 'custom') {
            if (! empty($params['date_from'])) {
                $query->where('orders.created_at', '>=', $params['date_from'].' 00:00:00');
            }
            if (! empty($params['date_to'])) {
                $query->where('orders.created_at', '<=', $params['date_to'].' 23:59:59');
            }
        }

        // FlexSearch Integration (Now possible because joins are simplified)
        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'slug', 'category.name', 'brand.name'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        return $query->groupBy('products.id')
            ->orderBy('period_sales_count', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get low stock product variants (Global and Warehouse levels).
     */
    public function getLowStockProducts(int $perPage = 10)
    {
        $allLowStock = collect();

        // 1. Check Global Stock Levels (Saleable)
        // Products/Variants where type is 'global' and total stock <= min_stock_global
        $globalLowStockVariants = ProductVariant::with(['product.primaryImage', 'product.category'])
            ->where('min_stock_type', 'global')
            ->whereColumn('stock', '<=', 'min_stock_global')
            ->get();

        foreach ($globalLowStockVariants as $variant) {
            $allLowStock->push([
                'type' => 'Global',
                'product_id' => $variant->product_id,
                'name' => $variant->product?->name ?? 'N/A',
                'category' => $variant->product?->category?->name ?? 'N/A',
                'image' => $variant->product?->primaryImage?->image_path ?? '',
                'variant_name' => $variant->variant_name,
                'sku' => $variant->sku,
                'stock' => $variant->stock,
                'location' => 'All Warehouses',
            ]);
        }

        // 2. Check Warehouse-specific Stock Levels (including Overrides)
        // Inventory levels where product/variant type is 'warehouse' and current_quantity <= min_stock_override
        $warehouseLowStock = InventoryLevel::with(['product.primaryImage', 'product.category', 'variant', 'warehouse'])
            ->whereNotNull('min_stock_override')
            ->where(function($query) {
                $query->whereHas('variant', function($q) {
                    $q->where('min_stock_type', 'warehouse');
                })->orWhere(function($q) {
                    $q->whereNull('product_variant_id')
                      ->whereHas('product', function($pq) {
                          $pq->where('min_stock_type', 'warehouse');
                      });
                });
            })
            ->whereColumn('current_quantity', '<=', 'min_stock_override')
            ->get();

        foreach ($warehouseLowStock as $level) {
            $allLowStock->push([
                'type' => 'Warehouse',
                'product_id' => $level->product_id,
                'name' => $level->product?->name ?? 'N/A',
                'category' => $level->product?->category?->name ?? 'N/A',
                'image' => $level->product?->primaryImage?->image_path ?? '',
                'variant_name' => $level->variant?->variant_name ?? 'N/A',
                'sku' => $level->variant?->sku ?? 'N/A',
                'stock' => $level->current_quantity,
                'location' => $level->warehouse?->name ?? 'Unknown',
            ]);
        }

        return $allLowStock->sortBy('stock')->take($perPage);
    }
}
