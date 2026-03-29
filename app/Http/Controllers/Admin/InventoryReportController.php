<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryReportController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Display the stock report (Inventory Levels).
     */
    public function stock(Request $request): View
    {
        $params = $request->all();
        $inventoryLevels = $this->inventoryService->getStockReport($params);
        $warehouses = Warehouse::where('is_quarantine', false)->get();

        if ($request->ajax()) {
            return view('admin.inventory.stock.partials.table', compact('inventoryLevels'));
        }

        return view('admin.inventory.stock.index', compact('inventoryLevels', 'warehouses'));
    }

    /**
     * Display the granular stock details for a specific product/variant in a warehouse.
     */
    public function productStockDetails(int $id): View
    {
        $level = $this->inventoryService->getProductStockDetails($id);

        return view('admin.inventory.stock.show', compact('level'));
    }

    /**
     * Display the batch report.
     */
    public function batches(Request $request): View
    {
        $params = $request->all();
        $batches = $this->inventoryService->getBatchReport($params);
        $warehouses = Warehouse::where('is_quarantine', false)->get();

        if ($request->ajax()) {
            return view('admin.inventory.batches.partials.table', compact('batches'));
        }

        return view('admin.inventory.batches.index', compact('batches', 'warehouses'));
    }

    /**
     * Display the damaged/quarantine products report.
     */
    public function damaged(Request $request): View
    {
        $params = $request->all();
        $inventoryLevels = $this->inventoryService->getDamagedReport($params);

        if ($request->ajax()) {
            return view('admin.inventory.stock.partials.table', compact('inventoryLevels'));
        }

        return view('admin.inventory.damaged.index', compact('inventoryLevels'));
    }

    /**
     * Display the specified batch details.
     */
    public function showBatch(Batch $batch): View
    {
        // A "batch" in the UI sense is all rows with the same batch_number and PO
        $allBatchItems = Batch::where('batch_number', $batch->batch_number)
            ->where('purchase_order_id', $batch->purchase_order_id)
            ->where('warehouse_id', $batch->warehouse_id)
            ->with(['product', 'variant', 'serials'])
            ->get();

        return view('admin.inventory.batches.show', compact('batch', 'allBatchItems'));
    }
}
