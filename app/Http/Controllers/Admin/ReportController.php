<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Display the sales report.
     */
    public function sales(Request $request): View
    {
        $filters = $request->all();

        // Default to last 30 days if no date range is set
        if (empty($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (empty($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }

        $summary = $this->reportService->getSalesSummary($filters);

        $breakdowns = [
            'product' => $this->reportService->getSalesByEntity('product', $filters),
            'variant' => $this->reportService->getSalesByEntity('variant', $filters),
            'warehouse' => $this->reportService->getSalesByEntity('warehouse', $filters),
            'batch' => $this->reportService->getSalesByEntity('batch', $filters),
            'payment_method' => $this->reportService->getSalesByEntity('payment_method', $filters),
        ];

        // Filter Options
        $warehouses = Warehouse::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        // We'll limit products in filter to avoid massive dropdowns, or use Select2 AJAX
        $products = Product::orderBy('name')->limit(100)->get();

        return view('admin.reports.sales', compact(
            'summary',
            'breakdowns',
            'filters',
            'warehouses',
            'categories',
            'brands',
            'products'
        ));
    }

    /**
     * Display the inventory report.
     */
    public function inventory(Request $request): View
    {
        $filters = $request->all();
        $report = $this->reportService->getInventoryReport($filters);

        // Filter Options
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(100)->get();

        return view('admin.reports.inventory', compact(
            'report',
            'filters',
            'warehouses',
            'suppliers',
            'categories',
            'brands',
            'products'
        ));
    }

    /**
     * Export inventory report to Excel.
     */
    public function exportInventory(Request $request): BinaryFileResponse
    {
        $filters = $request->all();
        $type = $request->get('type', 'product');
        $report = $this->reportService->getInventoryReport($filters);

        $headings = [];
        $exportData = [];
        $title = 'Inventory Report';

        switch ($type) {
            case 'warehouse':
                $title = 'Inventory by Warehouse';
                $headings = ['Warehouse', 'Quantity', 'Valuation'];
                foreach ($report['breakdowns']['warehouse'] as $item) {
                    $exportData[] = [$item['name'], $item['quantity'], number_format($item['valuation'], 2, '.', '')];
                }
                break;
            case 'product':
                $title = 'Inventory by Product';
                $headings = ['Product', 'Quantity', 'Valuation'];
                foreach ($report['breakdowns']['product'] as $item) {
                    $exportData[] = [$item['name'], $item['quantity'], number_format($item['valuation'], 2, '.', '')];
                }
                break;
            case 'batch':
                $title = 'Inventory by Batch';
                $headings = ['Batch #', 'Warehouse', 'Product', 'Quantity', 'Unit Cost', 'Valuation'];
                foreach ($report['breakdowns']['batch'] as $item) {
                    $exportData[] = [
                        $item['name'],
                        $item['warehouse'],
                        $item['product'],
                        $item['quantity'],
                        number_format($item['unit_cost'], 2, '.', ''),
                        number_format($item['valuation'], 2, '.', ''),
                    ];
                }
                break;
        }

        return Excel::download(new SalesExport($exportData, $headings, $title), 'inventory_'.$type.'_'.now()->format('Ymd_His').'.xlsx');
    }

    /**
     * Export sales report to Excel.
     */
    public function exportSales(Request $request): BinaryFileResponse
    {
        $filters = $request->all();
        $type = $request->get('type', 'trends');
        $headings = [];
        $exportData = [];
        $title = 'Sales Report';

        if ($type === 'trends') {
            $summary = $this->reportService->getSalesSummary($filters);
            $title = 'Sales Trends - '.ucfirst($summary['group_by']);
            $headings = ['Period', 'Orders', 'Net Sales', 'Total Cost', 'Gross Profit'];
            $exportData = $summary['grouped_data']->map(fn ($item) => [
                $item->period,
                $item->orders_count,
                number_format($item->net_sales, 2, '.', ''),
                number_format($item->total_cost, 2, '.', ''),
                number_format($item->gross_profit, 2, '.', ''),
            ])->toArray();
        } else {
            $data = $this->reportService->getSalesByEntity($type, $filters);

            switch ($type) {
                case 'product':
                    $title = 'Top Products by Sales';
                    $headings = ['Product', 'Units Sold', 'Net Sales', 'Total Cost', 'Gross Profit'];
                    $exportData = $data->map(fn ($item) => [$item->name, $item->units_sold, $item->net_sales, $item->total_cost, $item->gross_profit])->toArray();
                    break;
                case 'warehouse':
                    $title = 'Sales by Warehouse';
                    $headings = ['Warehouse', 'Units Sold', 'Net Sales', 'Total Cost', 'Gross Profit'];
                    $exportData = $data->map(fn ($item) => [$item->name, $item->units_sold, $item->net_sales, $item->total_cost, $item->gross_profit])->toArray();
                    break;
                case 'batch':
                    $title = 'Sales by Batch';
                    $headings = ['Batch #', 'Units Sold', 'Net Sales', 'Total Cost', 'Gross Profit'];
                    $exportData = $data->map(fn ($item) => [$item->name, $item->units_sold, $item->net_sales, $item->total_cost, $item->gross_profit])->toArray();
                    break;
                case 'payment_method':
                    $title = 'Payment Method Breakdown';
                    $headings = ['Method', 'Orders', 'Net Sales'];
                    $exportData = $data->map(fn ($item) => [$item->name, $item->orders_count, $item->net_sales])->toArray();
                    break;
            }
        }

        return Excel::download(new SalesExport($exportData, $headings, $title), 'sales_'.$type.'_'.now()->format('Ymd_His').'.xlsx');
    }
}
