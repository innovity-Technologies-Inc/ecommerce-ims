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
        $today = $now->copy()->startOfDay();
        $startOfWeek = $now->copy()->subDays(6)->startOfDay(); // Last 7 days
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        // Today's Sales
        $todayOrderQuery = Order::where('created_at', '>=', $today)
            ->where('order_status', 'Delivered');
        $todaySales = $todayOrderQuery->sum('total_amount');
        $todayCost = $todayOrderQuery->sum('total_cost');
        $todayProfit = $todaySales - $todayCost;

        // Weekly Sales (Last 7 Days)
        $weeklyOrderQuery = Order::where('created_at', '>=', $startOfWeek)
            ->where('order_status', 'Delivered');
        $weeklySales = $weeklyOrderQuery->sum('total_amount');
        $weeklyCost = $weeklyOrderQuery->sum('total_cost');
        $weeklyProfit = $weeklySales - $weeklyCost;

        // Monthly Sales
        $thisMonthOrderQuery = Order::where('created_at', '>=', $startOfMonth)
            ->where('order_status', 'Delivered');
        $thisMonthSales = $thisMonthOrderQuery->sum('total_amount');
        $thisMonthCost = $thisMonthOrderQuery->sum('total_cost');
        $thisMonthProfit = $thisMonthSales - $thisMonthCost;

        // Yearly Sales
        $thisYearOrderQuery = Order::where('created_at', '>=', $startOfYear)
            ->where('order_status', 'Delivered');
        $thisYearSales = $thisYearOrderQuery->sum('total_amount');
        $thisYearCost = $thisYearOrderQuery->sum('total_cost');
        $thisYearProfit = $thisYearSales - $thisYearCost;

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
        // 1. Simple Products (no variants) - only if min_stock_global > 0
        $simpleGlobalLowCount = Product::whereDoesntHave('variants')
            ->where('min_stock_global', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock_global')
            ->count();

        // 2. Variants - only if min_stock_global > 0
        $variantGlobalLowCount = ProductVariant::where('min_stock_global', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock_global')
            ->count();

        $globalLowStockCount = $simpleGlobalLowCount + $variantGlobalLowCount;

        // Count Warehouse Low Stock
        // Join inventory_levels with warehouse_stock_limits to check thresholds
        $warehouseLowStockCount = DB::table('inventory_levels')
            ->join('warehouse_stock_limits', function ($join) {
                $join->on('inventory_levels.product_id', '=', 'warehouse_stock_limits.product_id')
                    ->on('inventory_levels.warehouse_id', '=', 'warehouse_stock_limits.warehouse_id')
                    ->where(function ($q) {
                        $q->whereColumn('inventory_levels.product_variant_id', '=', 'warehouse_stock_limits.product_variant_id')
                            ->orWhere(function ($sq) {
                                $sq->whereNull('inventory_levels.product_variant_id')
                                    ->whereNull('warehouse_stock_limits.product_variant_id');
                            });
                    });
            })
            ->whereColumn('inventory_levels.current_quantity', '<=', 'warehouse_stock_limits.min_stock')
            ->count();

        $lowStockCount = $globalLowStockCount + $warehouseLowStockCount;

        return [
            'todaySales' => $todaySales,
            'todayCost' => $todayCost,
            'todayProfit' => $todayProfit,
            'weeklySales' => $weeklySales,
            'weeklyCost' => $weeklyCost,
            'weeklyProfit' => $weeklyProfit,
            'thisMonthSales' => $thisMonthSales,
            'thisMonthCost' => $thisMonthCost,
            'thisMonthProfit' => $thisMonthProfit,
            'thisYearSales' => $thisYearSales,
            'thisYearCost' => $thisYearCost,
            'thisYearProfit' => $thisYearProfit,
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
     * Get yearly revenue vs cost data for the last 10 years.
     */
    public function getYearlyRevenueVsCostData(): array
    {
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 9; // Last 10 years

        $metrics = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('SUM(total_cost) as cost')
        )
            ->whereYear('created_at', '>=', $startYear)
            ->where('order_status', 'Delivered')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->keyBy('year')
            ->toArray();

        $revenue = [];
        $cost = [];
        $profit = [];
        $labels = [];

        for ($i = $startYear; $i <= $currentYear; $i++) {
            $labels[] = (string) $i;
            $rev = (float) ($metrics[$i]['revenue'] ?? 0);
            $cst = (float) ($metrics[$i]['cost'] ?? 0);
            $revenue[] = $rev;
            $cost[] = $cst;
            $profit[] = $rev - $cst;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
        ];
    }

    /**
     * Get revenue vs cost data for each month of the current year.
     */
    public function getRevenueVsCostData(): array
    {
        $year = Carbon::now()->year;

        $metrics = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('SUM(total_cost) as cost')
        )
            ->whereYear('created_at', $year)
            ->where('order_status', 'Delivered')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        $revenue = [];
        $cost = [];
        $profit = [];
        $labels = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M');
            $rev = (float) ($metrics[$i]['revenue'] ?? 0);
            $cst = (float) ($metrics[$i]['cost'] ?? 0);
            $revenue[] = $rev;
            $cost[] = $cst;
            $profit[] = $rev - $cst;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
        ];
    }

    /**
     * Get order count data for each month of the current year.
     */
    public function getMonthlyOrderData(): array
    {
        $year = Carbon::now()->year;

        $orders = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('created_at', $year)
            ->whereNotIn('order_status', ['Cancelled', 'Rejected'])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $data = [];
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M');
            $data[] = $orders[$i] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get order count data for the last 10 years.
     */
    public function getYearlyOrderData(): array
    {
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 9;

        $orders = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('created_at', '>=', $startYear)
            ->whereNotIn('order_status', ['Cancelled', 'Rejected'])
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->pluck('total', 'year')
            ->toArray();

        $data = [];
        $labels = [];
        for ($i = $startYear; $i <= $currentYear; $i++) {
            $labels[] = (string) $i;
            $data[] = $orders[$i] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get purchase order count data for each month of the current year.
     */
    public function getMonthlyPurchaseData(): array
    {
        $year = Carbon::now()->year;

        $purchases = DB::table('purchase_orders')->select(
            DB::raw('MONTH(order_date) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('order_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $data = [];
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M');
            $data[] = $purchases[$i] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get purchase order count data for the last 10 years.
     */
    public function getYearlyPurchaseData(): array
    {
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 9;

        $purchases = DB::table('purchase_orders')->select(
            DB::raw('YEAR(order_date) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('order_date', '>=', $startYear)
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->pluck('total', 'year')
            ->toArray();

        $data = [];
        $labels = [];
        for ($i = $startYear; $i <= $currentYear; $i++) {
            $labels[] = (string) $i;
            $data[] = $purchases[$i] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
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

    public function getLowStockProducts(int $perPage = 10)
    {
        $allLowStock = collect();

        // 1. Check Global Stock Levels (Saleable)

        // 1a. Simple Products - only if min_stock_global > 0
        $globalLowProducts = Product::with(['primaryImage', 'category'])
            ->whereDoesntHave('variants')
            ->where('min_stock_global', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock_global')
            ->get();

        foreach ($globalLowProducts as $product) {
            $allLowStock->push([
                'type' => 'Global',
                'product_id' => $product->id,
                'name' => $product->name,
                'category' => $product->category?->name ?? 'N/A',
                'image' => $product->primaryImage?->image_path ?? '',
                'variant_name' => 'N/A',
                'sku' => 'N/A',
                'stock' => $product->stock,
                'location' => 'All Warehouses',
                'suggested_restock' => max($product->min_stock_global * 2 - $product->stock, 10),
            ]);
        }

        // 1b. Variants - only if min_stock_global > 0
        $globalLowVariants = ProductVariant::with(['product.primaryImage', 'product.category'])
            ->where('min_stock_global', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock_global')
            ->get();

        foreach ($globalLowVariants as $variant) {
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
                'suggested_restock' => max($variant->min_stock_global * 2 - $variant->stock, 10),
            ]);
        }

        // 2. Check Warehouse-specific Stock Levels
        $warehouseLowStock = InventoryLevel::with(['product.primaryImage', 'product.category', 'variant', 'warehouse'])
            ->join('warehouse_stock_limits', function ($join) {
                $join->on('inventory_levels.product_id', '=', 'warehouse_stock_limits.product_id')
                    ->on('inventory_levels.warehouse_id', '=', 'warehouse_stock_limits.warehouse_id')
                    ->where(function ($q) {
                        $q->whereColumn('inventory_levels.product_variant_id', '=', 'warehouse_stock_limits.product_variant_id')
                            ->orWhere(function ($sq) {
                                $sq->whereNull('inventory_levels.product_variant_id')
                                    ->whereNull('warehouse_stock_limits.product_variant_id');
                            });
                    });
            })
            ->whereColumn('inventory_levels.current_quantity', '<=', 'warehouse_stock_limits.min_stock')
            ->select('inventory_levels.*', 'warehouse_stock_limits.min_stock as warehouse_min')
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
                'suggested_restock' => max($level->warehouse_min * 2 - $level->current_quantity, 10),
            ]);
        }

        return $allLowStock->sortBy('stock')->take($perPage);
    }

    /**
     * Get low stock products with pagination.
     */
    public function getLowStockProductsPaged(array $params = [], int $perPage = 20): \Illuminate\Pagination\LengthAwarePaginator
    {
        $allLowStock = collect();

        // 1a. Simple Products
        $globalLowProducts = Product::with(['primaryImage', 'category'])
            ->whereDoesntHave('variants')
            ->where('min_stock_global', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock_global');

        if (!empty($params['search'])) {
            $search = $params['search'];
            $globalLowProducts->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        foreach ($globalLowProducts->get() as $product) {
            $allLowStock->push((object)[
                'type' => 'Global',
                'product_id' => $product->id,
                'name' => $product->name,
                'category' => $product->category?->name ?? 'N/A',
                'image' => $product->primaryImage?->image_path ?? '',
                'variant_name' => 'N/A',
                'sku' => $product->sku ?? 'N/A',
                'stock' => $product->stock,
                'location' => 'All Warehouses',
                'min_stock' => $product->min_stock_global,
            ]);
        }

        // 1b. Variants
        $globalLowVariants = ProductVariant::with(['product.primaryImage', 'product.category'])
            ->where('min_stock_global', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock_global');

        if (!empty($params['search'])) {
            $search = $params['search'];
            $globalLowVariants->where(function($q) use ($search) {
                $q->where('variant_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        foreach ($globalLowVariants->get() as $variant) {
            $allLowStock->push((object)[
                'type' => 'Global',
                'product_id' => $variant->product_id,
                'name' => $variant->product?->name ?? 'N/A',
                'category' => $variant->product?->category?->name ?? 'N/A',
                'image' => $variant->product?->primaryImage?->image_path ?? '',
                'variant_name' => $variant->variant_name,
                'sku' => $variant->sku,
                'stock' => $variant->stock,
                'location' => 'All Warehouses',
                'min_stock' => $variant->min_stock_global,
            ]);
        }

        // 2. Warehouse
        $warehouseLowStock = InventoryLevel::with(['product.primaryImage', 'product.category', 'variant', 'warehouse'])
            ->join('warehouse_stock_limits', function ($join) {
                $join->on('inventory_levels.product_id', '=', 'warehouse_stock_limits.product_id')
                    ->on('inventory_levels.warehouse_id', '=', 'warehouse_stock_limits.warehouse_id')
                    ->where(function ($q) {
                        $q->whereColumn('inventory_levels.product_variant_id', '=', 'warehouse_stock_limits.product_variant_id')
                            ->orWhere(function ($sq) {
                                $sq->whereNull('inventory_levels.product_variant_id')
                                    ->whereNull('warehouse_stock_limits.product_variant_id');
                            });
                    });
            })
            ->whereColumn('inventory_levels.current_quantity', '<=', 'warehouse_stock_limits.min_stock')
            ->select('inventory_levels.*', 'warehouse_stock_limits.min_stock as warehouse_min');

        if (!empty($params['search'])) {
            $search = $params['search'];
            $warehouseLowStock->where(function($q) use ($search) {
                $q->whereHas('product', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })->orWhereHas('variant', function($vq) use ($search) {
                    $vq->where('variant_name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            });
        }

        foreach ($warehouseLowStock->get() as $level) {
            $allLowStock->push((object)[
                'type' => 'Warehouse',
                'product_id' => $level->product_id,
                'name' => $level->product?->name ?? 'N/A',
                'category' => $level->product?->category?->name ?? 'N/A',
                'image' => $level->product?->primaryImage?->image_path ?? '',
                'variant_name' => $level->variant?->variant_name ?? 'N/A',
                'sku' => $level->variant?->sku ?? ($level->product?->sku ?? 'N/A'),
                'stock' => $level->current_quantity,
                'location' => $level->warehouse?->name ?? 'Unknown',
                'min_stock' => $level->warehouse_min,
            ]);
        }

        $sorted = $allLowStock->sortBy('stock');

        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sorted->slice($offset, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
