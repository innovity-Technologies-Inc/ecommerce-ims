<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ShippingMethodRequest;
use App\Models\ShippingMethod;
use App\Services\ShippingMethodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingMethodController extends Controller
{
    public function __construct(protected ShippingMethodService $shippingMethodService) {}

    /**
     * Display a listing of shipping methods.
     */
    public function index(Request $request)
    {
        $shippingMethods = $this->shippingMethodService->getPaginatedMethods($request->all());

        if ($request->ajax()) {
            return view('admin.shipping_methods.partials.table', compact('shippingMethods'))->render();
        }

        return view('admin.shipping_methods.index', compact('shippingMethods'));
    }

    /**
     * Show the form for creating a new shipping method.
     */
    public function create(): View
    {
        return view('admin.shipping_methods.create');
    }

    /**
     * Store a newly created shipping method.
     */
    public function store(ShippingMethodRequest $request): RedirectResponse
    {
        $this->shippingMethodService->storeMethod($request->validated());

        return redirect()->route('admin.shipping_methods.index')->with('success', 'Shipping method created successfully!');
    }

    /**
     * Show the form for editing the specified shipping method.
     */
    public function edit(ShippingMethod $shippingMethod): View
    {
        return view('admin.shipping_methods.edit', compact('shippingMethod'));
    }

    /**
     * Update the specified shipping method.
     */
    public function update(ShippingMethodRequest $request, ShippingMethod $shippingMethod): RedirectResponse
    {
        $this->shippingMethodService->updateMethod($shippingMethod, $request->validated());

        return redirect()->route('admin.shipping_methods.index')->with('success', 'Shipping method updated successfully!');
    }

    /**
     * Remove the specified shipping method.
     */
    public function destroy(ShippingMethod $shippingMethod): RedirectResponse
    {
        $this->shippingMethodService->deleteMethod($shippingMethod);

        return redirect()->route('admin.shipping_methods.index')->with('success', 'Shipping method deleted successfully!');
    }

    /**
     * Toggle the status of a shipping method.
     */
    public function toggleStatus(ShippingMethod $shippingMethod): \Illuminate\Http\JsonResponse
    {
        $this->shippingMethodService->toggleStatus($shippingMethod);

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping method status updated successfully',
        ]);
    }
}
