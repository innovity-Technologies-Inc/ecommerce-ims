<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseOrderRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(protected PurchaseOrderService $poService) {}

    /**
     * Display a listing of the purchase orders.
     */
    public function index(Request $request): View
    {
        $pos = $this->poService->getAllPurchaseOrders($request->all());
        $suppliers = Supplier::all();

        if ($request->ajax()) {
            return view('admin.inventory.po.partials.table', compact('pos'));
        }

        return view('admin.inventory.po.index', compact('pos', 'suppliers'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(): View
    {
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::with('variants')->where('status', 1)->get();

        return view('admin.inventory.po.create', compact('suppliers', 'warehouses', 'products'));
    }

    /**
     * Store a newly created purchase order in storage.
     */
    public function store(PurchaseOrderRequest $request): RedirectResponse
    {
        try {
            $this->poService->storePurchaseOrder($request->validated());

            return redirect()->route('admin.inventory.po.index')->with([
                'message' => 'Purchase Order created successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $po): View
    {
        $po->load(['supplier', 'warehouse', 'items.product', 'items.variant', 'creator']);

        return view('admin.inventory.po.show', compact('po'));
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit(PurchaseOrder $po): View
    {
        if ($po->status === 'Delivered') {
            return redirect()->route('admin.inventory.po.index')->with([
                'message' => 'Delivered Purchase Orders cannot be edited.',
                'alert-type' => 'error',
            ]);
        }

        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::with('variants')->where('status', 1)->get();
        $po->load('items');

        return view('admin.inventory.po.edit', compact('po', 'suppliers', 'warehouses', 'products'));
    }

    /**
     * Update the specified purchase order in storage.
     */
    public function update(PurchaseOrderRequest $request, PurchaseOrder $po): RedirectResponse
    {
        try {
            $this->poService->updatePurchaseOrder($po, $request->validated());

            return redirect()->route('admin.inventory.po.index')->with([
                'message' => 'Purchase Order updated successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Update the status of the specified purchase order.
     */
    public function updateStatus(Request $request, PurchaseOrder $po): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:Draft,Sent,Delivered',
            'received_date' => 'nullable|date',
        ]);

        try {
            $this->poService->updateStatus($po, $request->status, $request->received_date);

            return back()->with([
                'message' => 'Status updated successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified purchase order from storage.
     */
    public function destroy(PurchaseOrder $po): RedirectResponse
    {
        try {
            $this->poService->deletePurchaseOrder($po);

            return redirect()->route('admin.inventory.po.index')->with([
                'message' => 'Purchase Order deleted successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}
