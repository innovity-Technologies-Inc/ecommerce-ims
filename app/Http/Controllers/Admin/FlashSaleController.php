<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FlashSaleRequest;
use App\Services\FlashSaleService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function __construct(
        protected FlashSaleService $flashSaleService,
        protected ProductService $productService
    ) {}

    /**
     * Show the edit form for Flash Sale.
     */
    public function edit()
    {
        $flashSale = $this->flashSaleService->getFlashSale()->load('items.product.primaryImage');
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = $this->productService->getBrandsForDropdown();

        return view('admin.flash_sale.edit', compact('flashSale', 'categories', 'brands'));
    }

    /**
     * Update the Flash Sale.
     */
    public function update(FlashSaleRequest $request)
    {
        $this->flashSaleService->updateFlashSale($request->validated());

        return redirect()->back()->with('success', 'Flash Sale updated successfully!');
    }

    /**
     * Search products for AJAX product list in the form.
     */
    public function searchProducts(Request $request)
    {
        $products = $this->flashSaleService->searchProducts($request->all());

        if ($request->ajax()) {
            return view('admin.flash_sale.partials.product_list', compact('products'))->render();
        }

        return $products;
    }
}
