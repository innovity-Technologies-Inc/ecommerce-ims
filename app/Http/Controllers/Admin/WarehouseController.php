<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WarehouseRequest;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $warehouses = $this->inventoryService->getAllWarehouses($request->all());

        if ($request->ajax()) {
            return view('admin.inventory.warehouses.partials.table', compact('warehouses'))->render();
        }

        return view('admin.inventory.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.inventory.warehouses.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseRequest $request)
    {
        $this->inventoryService->storeWarehouse($request->validated());

        return redirect()->route('admin.warehouses.index')->with([
            'message' => 'Warehouse created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse): View
    {
        return view('admin.inventory.warehouses.form', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        $this->inventoryService->updateWarehouse($warehouse, $request->validated());

        return redirect()->route('admin.warehouses.index')->with([
            'message' => 'Warehouse updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $this->inventoryService->deleteWarehouse($warehouse);

        return redirect()->route('admin.warehouses.index')->with([
            'message' => 'Warehouse deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
