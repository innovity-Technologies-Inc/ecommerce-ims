<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Services\BannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(protected BannerService $bannerService) {}

    /**
     * Display a listing of the banners.
     */
    public function index(): View
    {
        $banners = $this->bannerService->getAllBanners();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(int $id): View
    {
        $banner = $this->bannerService->getBannerById($id);

        $dimensions = [
            'home_1_left' => '330x315 px',
            'home_1_middle' => '690x315 px',
            'home_1_right' => '330x315 px',
            'home_2_full' => '1410x230 px',
            'cart_sidebar' => '690x550 px',
            'menu_banner' => '1350x170 px',
        ];

        $recommended_size = $dimensions[$banner->slug] ?? 'Unknown';

        return view('admin.banners.edit', compact('banner', 'recommended_size'));
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(UpdateBannerRequest $request, int $id): RedirectResponse
    {
        $result = $this->bannerService->updateBanner($id, $request->validated());

        if ($result) {
            return redirect()->route('admin.banners.index')->with([
                'message' => 'Banner updated successfully.',
                'alert-type' => 'success',
            ]);
        }

        return back()->with([
            'message' => 'Failed to update banner.',
            'alert-type' => 'error',
        ]);
    }
}
