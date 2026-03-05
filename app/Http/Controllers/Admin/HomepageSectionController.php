<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SectionSetting;
use App\Services\HomepageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    public function __construct(protected HomepageService $homepageService) {}

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

        $selectedProducts = $section->products()->get();
        $products = Product::all();

        return view('admin.sections.bestsellers', compact('section', 'selectedProducts', 'products'));
    }

    /**
     * Update the bestsellers configuration.
     */
    public function updateBestsellers(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_title' => 'required|string|max:255',
            'section_subtitle' => 'nullable|string|max:255',
            'mode' => 'required|in:organic,custom',
            'limit' => 'required|integer|min:1|max:50',
            'is_visible' => 'nullable',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $this->homepageService->updateSectionSetting('bestsellers', $data);

        return back()->with([
            'message' => 'Bestsellers section updated successfully',
            'alert-type' => 'success',
        ]);
    }
}
