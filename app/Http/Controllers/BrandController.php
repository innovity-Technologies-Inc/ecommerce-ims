<?php

namespace App\Http\Controllers;

use App\HelperClass;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $brands = Brand::latest()->paginate(10);

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
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('icon')) {
            $data['icon'] = HelperClass::file_upload($request->file('icon'), 'brands');
        }

        Brand::create($data);

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
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('icon')) {
            if ($brand->icon) {
                HelperClass::file_delete($brand->icon);
            }
            $data['icon'] = HelperClass::file_upload($request->file('icon'), 'brands');
        }

        $brand->update($data);

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
        if ($brand->icon) {
            HelperClass::file_delete($brand->icon);
        }
        $brand->delete();

        return redirect()->route('admin.brands.index')->with([
            'message' => 'Brand deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
