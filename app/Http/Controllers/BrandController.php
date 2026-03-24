<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(protected BrandService $brandService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $brands = $this->brandService->getAllBrands($request->all());

        if ($request->ajax()) {
            return view('admin.brands.partials.table', compact('brands'))->render();
        }

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.brands.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        $this->brandService->storeBrand($request->validated());

        return redirect()->route('admin.brands.index')->with([
            'message' => 'Brand created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand): View
    {
        return view('admin.brands.form', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        $this->brandService->updateBrand($brand, $request->validated());

        return redirect()->route('admin.brands.index')->with([
            'message' => 'Brand updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $this->brandService->deleteBrand($brand);

        return redirect()->route('admin.brands.index')->with([
            'message' => 'Brand deleted successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Toggle the status of a brand.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $this->brandService->toggleStatus($brand);

        return response()->json([
            'status' => 'success',
            'message' => 'Brand status updated successfully',
        ]);
    }
}
