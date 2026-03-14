<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index(){
        $summary = $this->dashboardService->getSummaryMetrics();
        $chartData = $this->dashboardService->getMonthlySalesData();
        $yearlyChartData = $this->dashboardService->getYearlySalesData();
        $bestSellingProducts = $this->dashboardService->getBestSellingProducts();
        $lowStockProducts = $this->dashboardService->getLowStockProducts(5); // Only show few on dashboard

        return view('admin.dashboard', compact('summary', 'chartData', 'yearlyChartData', 'bestSellingProducts', 'lowStockProducts'));
    }

    public function lowStockProducts()
    {
        $lowStockProducts = $this->dashboardService->getLowStockProducts(20);

        return view('admin.products.low-stock', compact('lowStockProducts'));
    }
}
