<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\WarehousePerformanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $reportData = $this->performanceService->getPerformanceReport($filters, 15);

        if ($request->ajax()) {
            return view('admin.reports.warehouse-performance.partials.table', compact('reportData', 'filters'));
        }

        return view('admin.reports.warehouse-performance.index', compact('reportData', 'filters', 'warehouses'));
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
