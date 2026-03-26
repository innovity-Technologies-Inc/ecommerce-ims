<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InventoryAllocationController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Display a listing of unallocated products/variants.
     */
    public function index(Request $request): View
    {
        $unallocated = $this->inventoryService->getUnallocatedStock($request->all());

        return view('admin.inventory.allocation.index', compact('unallocated'));
    }

    /**
     * Show the allocation form.
     */
    public function create(Request $request): View
    {
        $productId = $request->product_id;
        $variantId = $request->variant_id;

        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;
        $warehouses = Warehouse::all();

        return view('admin.inventory.allocation.create', compact('product', 'variant', 'warehouses'));
    }

    /**
     * Store the allocation.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->inventoryService->allocateStock($request->all());

            return redirect()->route('admin.inventory.allocation.index')->with([
                'message' => 'Stock allocated successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            Log::error('Stock Allocation Error: '.$e->getMessage());

            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}
