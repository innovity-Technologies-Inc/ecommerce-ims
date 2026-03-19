<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected \App\Services\ProductService $productService
    ) {}

    public function index()
    {
        $summary = $this->dashboardService->getSummaryMetrics();
        $chartData = $this->dashboardService->getMonthlySalesData();
        $yearlyChartData = $this->dashboardService->getYearlySalesData();
        $monthlyBestSellingProducts = $this->dashboardService->getMonthlyBestSellingProducts(10);
        $yearlyBestSellingProducts = $this->dashboardService->getYearlyBestSellingProducts(10);
        $lowStockProducts = $this->dashboardService->getLowStockProducts(5); // Only show few on dashboard

        return view('admin.dashboard', compact(
            'summary',
            'chartData',
            'yearlyChartData',
            'monthlyBestSellingProducts',
            'yearlyBestSellingProducts',
            'lowStockProducts'
        ));
    }

    public function bestSellingProducts(\Illuminate\Http\Request $request)
    {
        $products = $this->dashboardService->getBestSellingProductsPaged($request->all(), 20);
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = $this->productService->getBrandsForDropdown();

        if ($request->ajax()) {
            return view('admin.products.partials.best_selling_table', compact('products'))->render();
        }

        return view('admin.products.best-selling', compact('products', 'categories', 'brands'));
    }

    public function lowStockProducts()
    {
        $lowStockProducts = $this->dashboardService->getLowStockProducts(20);

        return view('admin.products.low-stock', compact('lowStockProducts'));
    }
}
