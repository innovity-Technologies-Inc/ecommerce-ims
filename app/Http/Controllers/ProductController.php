<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $products = $this->productService->getAllProducts($request->all());
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = $this->productService->getBrandsForDropdown();

        if ($request->ajax()) {
            return view('admin.products.partials.table', compact('products'))->render();
        }

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = Brand::all();

        return view('admin.products.form', compact('categories', 'brands'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            $this->productService->storeProduct($request->validated());

            return redirect()->route('admin.products.index')->with([
                'message' => 'Product created successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['images', 'variants', 'category', 'subCategory']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $product->load(['images', 'variants', 'category', 'subCategory']);
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = Brand::all();

        return view('admin.products.form', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $this->productService->updateProduct($product, $request->validated());

            return redirect()->route('admin.products.index')->with([
                'message' => 'Product updated successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $this->productService->deleteProduct($product);

            return redirect()->route('admin.products.index')->with([
                'message' => 'Product deleted successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Toggle the product status.
     */
    public function toggleStatus(int $id)
    {
        $this->productService->toggleStatus($id);

        return back()->with([
            'message' => 'Product status updated successfully',
            'alert-type' => 'success',
        ]);
    }
}
