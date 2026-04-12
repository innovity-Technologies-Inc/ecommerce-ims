<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DamageEntryRequest;
use App\Models\Batch;
use App\Models\BatchProduct;
use App\Models\BatchSerial;
use App\Models\Warehouse;
use App\Services\DamageEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WastageController extends Controller
{
    public function __construct(protected DamageEntryService $damageEntryService) {}

    /**
     * Show the form for creating a new damage entry.
     */
    public function create(): View
    {
        $warehouses = Warehouse::all();

        return view('admin.returns.wastage_entry', compact('warehouses'));
    }

    /**
     * Store a newly created damage entry in storage.
     */
    public function store(DamageEntryRequest $request): RedirectResponse
    {
        try {
            $this->damageEntryService->storeDamage($request->validated());

            return redirect()->route('admin.returns.wastages')->with([
                'message' => 'Damage entry recorded successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            \Log::error('Damage Entry Store Error: '.$e->getMessage());

            return back()->with([
                'message' => 'Something went wrong: '.$e->getMessage(),
                'alert-type' => 'error',
            ])->withInput();
        }
    }

    /**
     * AJAX: Get batches for a warehouse.
     */
    public function getBatches(Request $request): \Illuminate\Http\JsonResponse
    {
        $batches = Batch::where('warehouse_id', $request->warehouse_id)
            ->where('total_saleable_qty', '>', 0)
            ->get();

        return response()->json($batches);
    }

    /**
     * AJAX: Get products for a batch.
     */
    public function getProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = BatchProduct::with(['product', 'variant'])
            ->where('batch_id', $request->batch_id)
            ->where('saleable_qty', '>', 0)
            ->get();

        return response()->json($products);
    }

    /**
     * AJAX: Get good serials for a batch/product.
     */
    public function getGoodSerials(Request $request): \Illuminate\Http\JsonResponse
    {
        $serials = BatchSerial::where([
            'batch_id' => $request->batch_id,
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id,
            'product_status' => 'good',
            'stock_status' => 'in_stock',
        ])->get();

        return response()->json($serials);
    }
}
