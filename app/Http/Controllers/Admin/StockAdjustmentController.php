<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\StockAdjustmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function __construct(protected StockAdjustmentService $adjustmentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $adjustments = $this->adjustmentService->getAllAdjustments($request->all());
        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return view('admin.inventory.adjustment.partials.table', compact('adjustments'));
        }

        return view('admin.inventory.adjustment.index', compact('adjustments', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $warehouses = Warehouse::all();
        $products = Product::with('variants')->get();
        $generatedBatchNumber = 'ADJ-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(2)));

        return view('admin.inventory.adjustment.create', compact('warehouses', 'products', 'generatedBatchNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockAdjustmentRequest $request): RedirectResponse
    {
        try {
            $this->adjustmentService->storeAdjustment($request->validated());

            return redirect()->route('admin.inventory.adjustment.index')->with([
                'message' => 'Stock adjustment created successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            Log::error('Stock Adjustment Store Error: '.$e->getMessage());

            return back()->with([
                'message' => 'Something went wrong: '.$e->getMessage(),
                'alert-type' => 'error',
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAdjustment $adjustment): View
    {
        $adjustment->load(['warehouse', 'batch', 'items.product', 'items.variant', 'creator']);

        // Manually attach serials to each item based on batch and product
        foreach ($adjustment->items as $item) {
            $item->serials = \App\Models\BatchSerial::where([
                'batch_id' => $adjustment->batch_id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
            ])->get();
        }

        return view('admin.inventory.adjustment.show', compact('adjustment'));
    }
}
