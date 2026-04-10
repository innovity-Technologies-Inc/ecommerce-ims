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

        $view = $request->get('view');

        // Filter Options (Needed for both modes)
        $warehouses = Warehouse::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(100)->get();

        // Detailed View Mode
        if ($view) {
            $summary = $this->reportService->getSalesSummary($filters); // For Summary Cards
            $title = '';
            $data = null;
            $perPage = $request->has('is_print') ? null : 20;

            if ($view === 'trends') {
                $title = 'Sales Trends';
                $reportData = $this->reportService->getSalesSummary($filters, $perPage);
                $data = $reportData['grouped_data'];
            } else {
                $title = match ($view) {
                    'product' => 'Top Products by Sales',
                    'variant' => 'Sales by Variant',
                    'warehouse' => 'Sales by Warehouse',
                    'batch' => 'Top Batches by Sales',
                    'payment_method' => 'Payment Methods Breakdown',
                    default => 'Report Details'
                };
                $data = $this->reportService->getSalesByEntity($view, $filters, $perPage);
            }

            if ($request->has('is_print')) {
                return view('admin.reports.sales', compact(
                    'summary', 'filters', 'view', 'title', 'data',
                    'warehouses', 'categories', 'brands', 'products'
                ));
            }

            return view('admin.reports.sales', compact(
                'summary', 'filters', 'view', 'title', 'data',
                'warehouses', 'categories', 'brands', 'products'
            ));
        }

        // Dashboard Mode
        $summary = $this->reportService->getSalesSummary($filters);
        $breakdowns = [
            'product' => $this->reportService->getSalesByEntity('product', $filters),
            'variant' => $this->reportService->getSalesByEntity('variant', $filters),
            'warehouse' => $this->reportService->getSalesByEntity('warehouse', $filters),
            'batch' => $this->reportService->getSalesByEntity('batch', $filters),
            'payment_method' => $this->reportService->getSalesByEntity('payment_method', $filters),
        ];

        return view('admin.reports.sales', compact(
            'summary', 'breakdowns', 'filters', 'warehouses', 'categories', 'brands', 'products'
        ));
    }

    /**
     * Display the inventory report.
     */
    public function inventory(Request $request): View
    {
        $filters = $request->all();
        $view = $request->get('view');

        // Filter Options (Needed for both modes)
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(100)->get();

        // Detailed View Mode
        if ($view) {
            $report = $this->reportService->getInventoryReport($filters); // For Summary Cards
            $title = match ($view) {
                'warehouse' => 'Warehouse-wise Valuation',
                'product' => 'Product-wise Valuation',
                'batch' => 'Batch-wise Inventory Breakdown',
                default => 'Inventory Details'
            };
            $perPage = $request->has('is_print') ? null : 20;
            $reportData = $this->reportService->getInventoryReport($filters, $view, $perPage);
            $data = $reportData['data'];

            return view('admin.reports.inventory', compact(
                'report', 'filters', 'view', 'title', 'data',
                'warehouses', 'suppliers', 'categories', 'brands', 'products'
            ));
        }

        // Dashboard Mode
        $report = $this->reportService->getInventoryReport($filters);

        return view('admin.reports.inventory', compact(
            'report', 'filters', 'warehouses', 'suppliers', 'categories', 'brands', 'products'
        ));
    }

    /**
     * Display the stock report.
     */
    public function stock(Request $request): View
    {
        $filters = $request->all();
        $view = $request->get('view');

        // Filter Options
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $products = Product::orderBy('name')->limit(100)->get();

        // 1. Handle "View All" Mode
        if ($view) {
            $data = null;
            $title = '';
            $perPage = $request->has('is_print') ? null : 20;

            switch ($view) {
                case 'warehouse':
                    $title = 'Stock by Warehouse';
                    $data = $this->reportService->getStockReport(array_merge($filters, ['group_by' => 'warehouse']), $perPage);
                    break;
                case 'product':
                    $title = 'Stock by Product';
                    $data = $this->reportService->getStockReport(array_merge($filters, ['group_by' => 'product']), $perPage);
                    break;
                case 'batch':
                    $title = 'Stock by Batch';
                    $data = $this->reportService->getStockReport($filters, $perPage);
                    break;
                case 'movement':
                    $title = 'Stock Movement History';
                    $data = $this->reportService->getStockMovements($filters, $perPage ?? 1000);
                    break;
                case 'aging':
                    $title = 'Batch Aging (Stagnant Stock)';
                    $data = $this->reportService->getBatchAging($filters, $perPage ?? 1000); // Total limit 1000 if printing
                    break;
                case 'wastage_product':
                    $title = 'Wastage by Product';
                    $data = $this->reportService->getWastageBreakdown('product', $filters, $perPage);
                    break;
                case 'wastage_warehouse':
                    $title = 'Wastage by Warehouse';
                    $data = $this->reportService->getWastageBreakdown('warehouse', $filters, $perPage);
                    break;
                case 'wastage_batch':
                    $title = 'Wastage by Batch';
                    $data = $this->reportService->getWastageBreakdown('batch', $filters, $perPage);
                    break;
                case 'serial':
                    $title = 'Serial Number Trace';
                    $data = $this->reportService->getSerialTrace($filters, $perPage);
                    break;
            }

            // Summary cards still needed for context
            $rawReport = $this->reportService->getStockReport($filters);
            $summary = [
                'total_qty' => $rawReport->sum('current_quantity'),
                'damaged_qty' => $rawReport->sum('damaged_quantity'),
                'low_stock_count' => $rawReport->where('is_low_stock', 1)->count(),
                'total_value' => $rawReport->sum('inventory_value'),
            ];

            return view('admin.reports.stock', compact(
                'data', 'summary', 'filters', 'view', 'title',
                'warehouses', 'suppliers', 'categories', 'brands', 'products'
            ));
        }

        // 2. Dashboard Mode (Show top 10 for all)
        $rawReport = $this->reportService->getStockReport($filters);

        $summary = [
            'total_qty' => $rawReport->sum('current_quantity'),
            'damaged_qty' => $rawReport->sum('damaged_quantity'),
            'low_stock_count' => $rawReport->where('is_low_stock', 1)->count(),
            'total_value' => $rawReport->sum('inventory_value'),
        ];

        $breakdowns = [
            'warehouse' => $this->reportService->getStockReport(array_merge($filters, ['group_by' => 'warehouse']))->take(10),
            'product' => $this->reportService->getStockReport(array_merge($filters, ['group_by' => 'product']))->take(10),
            'batch' => $rawReport->take(10),
            'movement' => $this->reportService->getStockMovements($filters, 10),
            'aging' => $this->reportService->getBatchAging($filters, 10),
            'wastage_product' => $this->reportService->getWastageBreakdown('product', $filters, 10),
            'wastage_warehouse' => $this->reportService->getWastageBreakdown('warehouse', $filters, 10),
            'wastage_batch' => $this->reportService->getWastageBreakdown('batch', $filters, 10),
            'serial' => $this->reportService->getSerialTrace($filters, 10),
        ];

        return view('admin.reports.stock', compact(
            'breakdowns', 'summary', 'filters',
            'warehouses', 'suppliers', 'categories', 'brands', 'products'
        ));
    }

    /**
     * Export stock report to Excel.
     */
    public function exportStock(Request $request): BinaryFileResponse
    {
        $filters = $request->all();
        $view = $request->get('view', 'main');
        $headings = [];
        $exportData = [];
        $title = 'Stock Report';

        switch ($view) {
            case 'warehouse':
                $title = 'Stock by Warehouse';
                $headings = ['Warehouse', 'Total Quantity', 'Damaged Quantity', 'Valuation'];
                $data = $this->reportService->getStockReport(array_merge($filters, ['group_by' => 'warehouse']));
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->name,
                        $item->quantity,
                        $item->damaged_quantity,
                        number_format($item->valuation, 2, '.', ''),
                    ];
                }
                break;
            case 'product':
                $title = 'Stock by Product';
                $headings = ['Product', 'Total Quantity', 'Damaged Quantity', 'Valuation'];
                $data = $this->reportService->getStockReport(array_merge($filters, ['group_by' => 'product']));
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->name,
                        $item->quantity,
                        $item->damaged_quantity,
                        number_format($item->valuation, 2, '.', ''),
                    ];
                }
                break;
            case 'batch':
                $title = 'Stock by Batch';
                $headings = ['Batch #', 'SKU', 'Product', 'Warehouse', 'Current Qty', 'Damaged Qty', 'Valuation'];
                $data = $this->reportService->getStockReport($filters);
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->batch_number,
                        $item->sku,
                        $item->product_name,
                        $item->warehouse_name,
                        $item->current_quantity,
                        $item->damaged_quantity,
                        number_format($item->inventory_value, 2, '.', ''),
                    ];
                }
                break;
            case 'movement':
                $title = 'Stock Movements';
                $headings = ['Date', 'Product', 'Warehouse', 'Batch', 'Change Qty', 'Type'];
                $data = $this->reportService->getStockMovements($filters, null); // null for full export
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->created_at,
                        $item->product_name,
                        $item->warehouse_name,
                        $item->batch_number,
                        $item->change_qty,
                        str_replace('_', ' ', $item->transaction_type),
                    ];
                }
                break;
            case 'aging':
                $title = 'Batch Aging';
                $headings = ['Batch #', 'Warehouse', 'Supplier', 'Age (Days)'];
                $data = $this->reportService->getBatchAging($filters, null); // null for full export
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->batch_number,
                        $item->warehouse_name,
                        $item->supplier_name,
                        $item->age_days,
                    ];
                }
                break;
            case 'wastage_product':
            case 'wastage_warehouse':
            case 'wastage_batch':
                $type = str_replace('wastage_', '', $view);
                $title = 'Wastage by '.ucfirst($type);
                $headings = [ucfirst($type), 'Wastage Quantity'];
                $data = $this->reportService->getWastageBreakdown($type, $filters);
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->name,
                        $item->quantity,
                    ];
                }
                break;
            case 'serial':
                $title = 'Serial Trace Report';
                $headings = ['Serial #', 'Product', 'Status', 'Last Updated'];
                $data = $this->reportService->getSerialTrace($filters, null); // null for full export
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->serial_no,
                        $item->product_name,
                        ucfirst($item->stock_status),
                        $item->updated_at,
                    ];
                }
                break;
            default:
                $title = 'Full Stock Status';
                $headings = ['Warehouse', 'Product', 'SKU', 'Batch', 'Qty', 'Damaged', 'Valuation'];
                $data = $this->reportService->getStockReport($filters);
                foreach ($data as $item) {
                    $exportData[] = [
                        $item->warehouse_name,
                        $item->product_name,
                        $item->sku,
                        $item->batch_number,
                        $item->current_quantity,
                        $item->damaged_quantity,
                        number_format($item->inventory_value, 2, '.', ''),
                    ];
                }
                break;
        }

        return Excel::download(new SalesExport($exportData, $headings, $title), 'stock_report_'.$view.'_'.now()->format('Ymd_His').'.xlsx');
    }

    /**
     * Export inventory report to Excel.
     */
    public function exportInventory(Request $request): BinaryFileResponse
    {
        $filters = $request->all();
        $type = $request->get('type', 'product');

        $headings = [];
        $exportData = [];
        $title = 'Inventory Report';

        // Use the detailed data fetching if 'type' is one of the view types
        if (in_array($type, ['warehouse', 'product', 'batch'])) {
            $reportData = $this->reportService->getInventoryReport($filters, $type, null); // null perPage for full export
            $data = $reportData['data'];

            switch ($type) {
                case 'warehouse':
                    $title = 'Inventory by Warehouse';
                    $headings = ['Warehouse', 'Quantity', 'Valuation'];
                    foreach ($data as $item) {
                        $exportData[] = [$item->name, $item->quantity, number_format($item->valuation, 2, '.', '')];
                    }
                    break;
                case 'product':
                    $title = 'Inventory by Product';
                    $headings = ['Product', 'Quantity', 'Valuation'];
                    foreach ($data as $item) {
                        $exportData[] = [$item->name, $item->quantity, number_format($item->valuation, 2, '.', '')];
                    }
                    break;
                case 'batch':
                    $title = 'Inventory by Batch';
                    $headings = ['Batch #', 'Warehouse', 'Product', 'Quantity', 'Unit Cost', 'Valuation'];
                    foreach ($data as $item) {
                        $exportData[] = [
                            $item->name,
                            $item->warehouse,
                            $item->product,
                            $item->quantity,
                            number_format($item->unit_cost, 2, '.', ''),
                            number_format($item->valuation, 2, '.', ''),
                        ];
                    }
                    break;
            }
        } else {
            // Dashboard mode fallback
            $report = $this->reportService->getInventoryReport($filters);
            $title = 'Inventory Overview';
            $headings = ['Product', 'Quantity', 'Valuation'];
            foreach ($report['breakdowns']['product'] as $item) {
                $exportData[] = [$item['name'], $item['quantity'], number_format($item['valuation'], 2, '.', '')];
            }
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
            // Fetch all records for export (null perPage)
            $data = $this->reportService->getSalesByEntity($type, $filters, null);

            switch ($type) {
                case 'product':
                    $title = 'Top Products by Sales';
                    $headings = ['Product', 'Units Sold', 'Net Sales', 'Total Cost', 'Gross Profit'];
                    foreach ($data as $item) {
                        $exportData[] = [
                            $item->name,
                            $item->units_sold,
                            number_format($item->net_sales, 2, '.', ''),
                            number_format($item->total_cost, 2, '.', ''),
                            number_format($item->gross_profit, 2, '.', ''),
                        ];
                    }
                    break;
                case 'warehouse':
                    $title = 'Sales by Warehouse';
                    $headings = ['Warehouse', 'Units Sold', 'Net Sales', 'Total Cost', 'Gross Profit'];
                    foreach ($data as $item) {
                        $exportData[] = [
                            $item->name,
                            $item->units_sold,
                            number_format($item->net_sales, 2, '.', ''),
                            number_format($item->total_cost, 2, '.', ''),
                            number_format($item->gross_profit, 2, '.', ''),
                        ];
                    }
                    break;
                case 'batch':
                    $title = 'Sales by Batch';
                    $headings = ['Batch #', 'Units Sold', 'Net Sales', 'Total Cost', 'Gross Profit'];
                    foreach ($data as $item) {
                        $exportData[] = [
                            $item->name,
                            $item->units_sold,
                            number_format($item->net_sales, 2, '.', ''),
                            number_format($item->total_cost, 2, '.', ''),
                            number_format($item->gross_profit, 2, '.', ''),
                        ];
                    }
                    break;
                case 'variant':
                    $title = 'Sales by Variant';
                    $headings = ['Variant', 'Units Sold', 'Net Sales', 'Total Cost', 'Gross Profit'];
                    foreach ($data as $item) {
                        $exportData[] = [
                            $item->name,
                            $item->units_sold,
                            number_format($item->net_sales, 2, '.', ''),
                            number_format($item->total_cost, 2, '.', ''),
                            number_format($item->gross_profit, 2, '.', ''),
                        ];
                    }
                    break;
                case 'payment_method':
                    $title = 'Payment Method Breakdown';
                    $headings = ['Method', 'Orders', 'Net Sales'];
                    foreach ($data as $item) {
                        $exportData[] = [
                            $item->name,
                            $item->orders_count,
                            number_format($item->net_sales, 2, '.', ''),
                        ];
                    }
                    break;
            }
        }

        return Excel::download(new SalesExport($exportData, $headings, $title), 'sales_'.$type.'_'.now()->format('Ymd_His').'.xlsx');
    }
}
