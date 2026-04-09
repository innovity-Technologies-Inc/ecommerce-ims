<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SectionSetting;
use App\Services\HomepageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\FlashSaleService;
use App\Services\ProductService;

class HomepageSectionController extends Controller
{
    public function __construct(
        protected HomepageService $homepageService,
        protected ProductService $productService,
        protected FlashSaleService $flashSaleService
    ) {}

    /**
     * Show the bestsellers configuration page.
     */
    public function bestsellers(): View
    {
        $section = SectionSetting::firstOrCreate(
            ['section_name' => 'bestsellers'],
            [
                'section_title' => 'Best Sellers',
                'section_subtitle' => 'Add bestselling products to weekly line up',
                'mode' => 'organic',
                'limit' => 10,
                'is_visible' => true,
            ]
        );

        $selectedProducts = $section->products()->with('primaryImage')->get();
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = $this->productService->getBrandsForDropdown();

        return view('admin.sections.form', [
            'section' => $section,
            'selectedProducts' => $selectedProducts,
            'categories' => $categories,
            'brands' => $brands,
            'title' => 'Bestsellers Section Settings'
        ]);
    }

    /**
     * Generic method to edit a section.
     */
    public function editSection(string $sectionName): View
    {
        $defaults = [
            'hot_deals' => [
                'section_title' => 'Hot Deals',
                'section_subtitle' => 'Add hot products to weekly line up',
                'limit' => 2
            ],
            'featured' => [
                'section_title' => 'Featured Products',
                'section_subtitle' => 'Add featured products to weekly line up',
                'limit' => 4
            ],
            'recently_added' => [
                'section_title' => 'Recently Added',
                'section_subtitle' => 'Add products to weekly line up',
                'limit' => 17
            ],
            'top_picks' => [
                'section_title' => 'Top Picks',
                'section_subtitle' => 'Our highly recommended products for you',
                'limit' => 8
            ],
        ];

        $section = SectionSetting::firstOrCreate(
            ['section_name' => $sectionName],
            array_merge($defaults[$sectionName] ?? [], [
                'mode' => 'organic',
                'is_visible' => true,
            ])
        );

        $selectedProducts = $section->products()->with('primaryImage')->get();
        $categories = $this->productService->getCategoriesForDropdown();
        $brands = $this->productService->getBrandsForDropdown();

        return view('admin.sections.form', [
            'section' => $section,
            'selectedProducts' => $selectedProducts,
            'categories' => $categories,
            'brands' => $brands,
            'title' => ucwords(str_replace('_', ' ', $sectionName)) . ' Section Settings'
        ]);
    }

    /**
     * Search products for AJAX product list in the form.
     */
    public function searchProducts(Request $request)
    {
        $products = $this->flashSaleService->searchProducts($request->all());

        if ($request->ajax()) {
            return view('admin.sections.partials.product_list', compact('products'))->render();
        }

        return $products;
    }

    /**
     * Update a section configuration.
     */
    public function updateSection(Request $request, string $sectionName): RedirectResponse
    {
        if ($request->has('limit')) {
            $request->merge(['limit' => (int) $request->limit]);
        }

        $data = $request->validate([
            'section_title' => 'required|string|max:255',
            'section_subtitle' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'mode' => 'required|in:organic,custom',
            'limit' => 'required|integer|min:1|max:50',
            'is_visible' => 'nullable',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $this->homepageService->updateSectionSetting($sectionName, $data);

        return back()->with([
            'message' => ucwords(str_replace('_', ' ', $sectionName)) . ' section updated successfully',
            'alert-type' => 'success',
        ]);
    }
}
