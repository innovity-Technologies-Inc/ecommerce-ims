<?php

namespace App\Services;

use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
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

        $lowStockLimit = GeneralSetting::first()->low_stock_limit ?? 5;
        $lowStockCount = ProductVariant::where('stock', '<=', $lowStockLimit)->count();

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
     * Get best selling products.
     */
    public function getBestSellingProducts(int $limit = 5)
    {
        return Product::with(['primaryImage', 'category'])
            ->orderBy('sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get low stock product variants.
     */
    public function getLowStockProducts(int $perPage = 10)
    {
        $lowStockLimit = GeneralSetting::first()->low_stock_limit ?? 5;

        return ProductVariant::with(['product.primaryImage', 'product.category'])
            ->where('stock', '<=', $lowStockLimit)
            ->paginate($perPage);
    }
}
