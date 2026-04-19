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
        $yearlyRevenueVsCostData = $this->dashboardService->getYearlyRevenueVsCostData();
        $revenueVsCostData = $this->dashboardService->getRevenueVsCostData();

        // Orders & Purchases data
        $monthlyOrderData = $this->dashboardService->getMonthlyOrderData();
        $yearlyOrderData = $this->dashboardService->getYearlyOrderData();
        $monthlyPurchaseData = $this->dashboardService->getMonthlyPurchaseData();
        $yearlyPurchaseData = $this->dashboardService->getYearlyPurchaseData();

        $monthlyBestSellingProducts = $this->dashboardService->getMonthlyBestSellingProducts(10);
        $yearlyBestSellingProducts = $this->dashboardService->getYearlyBestSellingProducts(10);
        $lowStockProducts = $this->dashboardService->getLowStockProducts(5); // Only show few on dashboard

        return view('admin.dashboard', compact(
            'summary',
            'yearlyRevenueVsCostData',
            'revenueVsCostData',
            'monthlyOrderData',
            'yearlyOrderData',
            'monthlyPurchaseData',
            'yearlyPurchaseData',
            'monthlyBestSellingProducts',
            'yearlyBestSellingProducts',
            'lowStockProducts'
        ));
    }

    public function bestSellingProducts(\Illuminate\Http\Request $request)
    {
        $perPage = $request->has('is_print') ? null : 20;
        $products = $this->dashboardService->getBestSellingProductsPaged($request->all(), $perPage);
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = $this->productService->getBrandsForDropdown();

        if ($request->ajax()) {
            return view('admin.products.partials.best_selling_table', compact('products'))->render();
        }

        return view('admin.products.best-selling', compact('products', 'categories', 'brands'));
    }

    public function lowStockProducts(\Illuminate\Http\Request $request)
    {
        $perPage = $request->has('is_print') ? 500 : 20;
        $lowStockProducts = $this->dashboardService->getLowStockProductsPaged($request->all(), $perPage);

        if ($request->ajax()) {
            return view('admin.products.partials.low_stock_table', compact('lowStockProducts'))->render();
        }

        return view('admin.products.low-stock', compact('lowStockProducts'));
    }
}
