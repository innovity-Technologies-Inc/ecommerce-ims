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
        $lowStockProducts = $this->dashboardService->getLowStockProducts(50);

        return view('admin.products.low-stock', compact('lowStockProducts'));
    }
}
