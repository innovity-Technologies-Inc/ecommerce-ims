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
        $batch->load(['purchaseOrder', 'warehouse', 'items.product', 'items.variant', 'serials.product', 'serials.variant']);

        return view('admin.inventory.batches.show', compact('batch'));
    }
}
