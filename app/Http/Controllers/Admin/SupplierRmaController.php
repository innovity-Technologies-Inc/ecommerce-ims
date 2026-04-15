<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRmaRequest;
use App\Http\Requests\Admin\SupplierRmaStatusRequest;
use App\Models\Batch;
use App\Models\BatchSerial;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierRma;
use App\Services\InventoryService;
use App\Services\SupplierRmaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SupplierRmaController extends Controller
{
    public function __construct(
        protected SupplierRmaService $rmaService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $rmas = $this->rmaService->getAllRmas($request->all());
        $suppliers = Supplier::all();

        if ($request->ajax()) {
            return view('admin.inventory.rma.partials.table', compact('rmas'));
        }

        return view('admin.inventory.rma.index', compact('rmas', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::where('status', 'received')->get();

        return view('admin.inventory.rma.create', compact('suppliers', 'purchaseOrders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRmaRequest $request): RedirectResponse
    {
        try {
            $this->rmaService->storeRma($request->validated());

            return redirect()->route('admin.inventory.rma.index')->with([
                'message' => 'Supplier RMA created successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier RMA Store Error: '.$e->getMessage());

            return back()->with([
                'message' => 'Something went wrong: '.$e->getMessage(),
                'alert-type' => 'error',
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierRma $rma): View
    {
        $rma->load(['supplier', 'purchaseOrder', 'rmaItems.product', 'rmaItems.variant', 'rmaItems.batch', 'rmaItems.serial']);

        return view('admin.inventory.rma.show', compact('rma'));
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(SupplierRmaStatusRequest $request, SupplierRma $rma): RedirectResponse
    {
        try {
            $this->rmaService->updateStatus($rma, $request->status);

            return back()->with([
                'message' => 'RMA status updated successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier RMA Status Update Error: '.$e->getMessage());

            return back()->with([
                'message' => 'Something went wrong: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * AJAX: Get purchase orders for a supplier.
     */
    public function getPurchaseOrders(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = PurchaseOrder::where('status', 'Delivered');
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        return response()->json($query->get());
    }

    /**
     * AJAX: Get batches for a supplier or PO that have damaged items.
     */
    public function getDamagedBatches(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Batch::with(['batchProducts.product', 'batchProducts.variant', 'purchaseOrder'])
            ->where('total_damaged_qty', '>', 0);

        if ($request->purchase_order_id) {
            $query->where('purchase_order_id', $request->purchase_order_id);
        } elseif ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $batches = $query->get();

        // Calculate truly available damaged quantity by subtracting quantities in active RMAs
        $batches->each(function ($batch) {
            $batch->batchProducts->each(function ($bp) use ($batch) {
                $alreadyInRma = \App\Models\RmaItem::whereHas('supplierRma', function ($q) {
                    $q->where('status', '!=', 'closed');
                })
                    ->where('batch_id', $batch->id)
                    ->where('product_id', $bp->product_id)
                    ->where('product_variant_id', $bp->product_variant_id)
                    ->sum('quantity');

                $bp->damaged_qty = max(0, $bp->damaged_qty - $alreadyInRma);
            });

            // Filter out products with 0 available damaged qty
            $filteredProducts = $batch->batchProducts->filter(fn ($bp) => $bp->damaged_qty > 0)->values();
            $batch->setRelation('batchProducts', $filteredProducts);
        });

        // Filter out batches with no available products
        $batches = $batches->filter(fn ($batch) => $batch->batchProducts->count() > 0);

        return response()->json($batches->values());
    }

    /**
     * AJAX: Get damaged serials for a specific batch and product.
     */
    public function getDamagedSerials(Request $request): \Illuminate\Http\JsonResponse
    {
        $serialsQuery = BatchSerial::where([
            'batch_id' => $request->batch_id,
            'product_id' => $request->product_id,
            'product_status' => 'damaged',
        ]);

        if ($request->product_variant_id) {
            $serialsQuery->where('product_variant_id', $request->product_variant_id);
        }

        // Exclude serials already in an active RMA
        $activeRmaSerialIds = \App\Models\RmaItem::whereHas('supplierRma', function ($q) {
            $q->where('status', '!=', 'closed');
        })
            ->whereNotNull('batch_serial_id')
            ->pluck('batch_serial_id');

        $serialsQuery->whereNotIn('id', $activeRmaSerialIds);

        return response()->json($serialsQuery->get());
    }
}
