<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $suppliers = $this->inventoryService->getAllSuppliers($request->all());

        if ($request->ajax()) {
            return view('admin.inventory.suppliers.partials.table', compact('suppliers'))->render();
        }

        return view('admin.inventory.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.inventory.suppliers.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request)
    {
        $this->inventoryService->storeSupplier($request->validated());

        return redirect()->route('admin.suppliers.index')->with([
            'message' => 'Supplier created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        return view('admin.inventory.suppliers.form', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $this->inventoryService->updateSupplier($supplier, $request->validated());

        return redirect()->route('admin.suppliers.index')->with([
            'message' => 'Supplier updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $this->inventoryService->deleteSupplier($supplier);

        return redirect()->route('admin.suppliers.index')->with([
            'message' => 'Supplier deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
