<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\WarehousePerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\WarehousePerformanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WarehousePerformanceController extends Controller
{
    public function __construct(
        protected WarehousePerformanceService $performanceService
    ) {}

    /**
     * Display the warehouse performance dashboard.
     */
    public function index(Request $request): View
    {
        $filters = $request->all();

        // Default to last 30 days
        if (empty($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (empty($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }

        $warehouses = Warehouse::orderBy('name')->get();
        $perPage = $request->has('is_print') ? null : 15;
        $reportData = $this->performanceService->getPerformanceReport($filters, $perPage);

        if ($request->ajax()) {
            return view('admin.reports.warehouse-performance.partials.table', compact('reportData', 'filters'));
        }

        return view('admin.reports.warehouse-performance.index', compact('reportData', 'filters', 'warehouses'));
    }

    /**
     * Export warehouse performance report to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->all();

        if (empty($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (empty($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }

        // Pass null for perPage to get all records
        $data = $this->performanceService->getPerformanceReport($filters, null);

        $exportData = [];
        foreach ($data as $row) {
            $exportData[] = [
                $row['warehouse_name'],
                (int) ($row['opening_stock'] ?? 0),
                (int) ($row['received_qty'] ?? 0),
                (int) ($row['po_damaged_qty'] ?? 0),
                (int) ($row['sold_qty'] ?? 0),
                (int) ($row['adjusted_in'] ?? 0),
                (int) ($row['returns_qty'] ?? 0),
                (int) ($row['rtv_qty'] ?? 0),
                (int) ($row['total_wastage_qty'] ?? 0),
                (int) ($row['total_closing_stock'] ?? 0),
                number_format($row['inventory_value'] ?? 0, 2, '.', ''),
                number_format($row['fill_rate'] ?? 0, 2, '.', ''),
                number_format($row['net_fill_rate'] ?? 0, 2, '.', ''),
                number_format($row['return_rate'] ?? 0, 2, '.', ''),
                number_format($row['damage_rate'] ?? 0, 2, '.', ''),
                number_format($row['stock_turnover'], 2, '.', ''),
            ];
        }

        $headings = [
            'Warehouse', 'Opening Stock', 'Received', 'Damaged (Supplier)', 'Sold', 'Adjusted In',
            'Returns', 'RTV', 'Wastage (Total)', 'Closing Stock', 'Inventory Value',
            'Gross Fill Rate (%)', 'Net Fill Rate (%)', 'Return Rate (%)', 'Wastage Rate (%)', 'Stock Turnover',
        ];

        $title = 'Warehouse Performance Report ('.$filters['start_date'].' to '.$filters['end_date'].')';
        $fileName = 'warehouse_performance_report_'.date('Ymd_His').'.xlsx';

        return Excel::download(new WarehousePerformanceExport($exportData, $headings, $title), $fileName);
    }

    /**
     * Display detailed metrics for a single warehouse.
     */
    public function show(int $id, Request $request): View
    {
        $warehouse = Warehouse::findOrFail($id);
        $filters = $request->all();

        if (empty($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (empty($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }

        $filters['warehouse_id'] = $id;
        $report = $this->performanceService->getPerformanceReport($filters, null)->first();

        return view('admin.reports.warehouse-performance.show', compact('warehouse', 'report', 'filters'));
    }
}
