<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\SliderRequest;
use App\Models\Slider;
use App\Services\HomepageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SliderController extends Controller
{
    public function __construct(protected HomepageService $homepageService) {}

    /**
     * Display a listing of the sliders.
     */
    public function index(Request $request)
    {
        $sliders = $this->homepageService->getAllSliders($request->all());

        if ($request->ajax()) {
            return view('admin.sliders.partials.table', compact('sliders'))->render();
        }

        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new slider.
     */
    public function create(): View
    {
        return view('admin.sliders.create');
    }

    /**
     * Store a newly created slider in storage.
     */
    public function store(SliderRequest $request): RedirectResponse
    {
        $this->homepageService->storeSlider($request->validated());

        return redirect()->route('admin.sliders.index')->with([
            'message' => 'Slider created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified slider.
     */
    public function edit(Slider $slider): View
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified slider in storage.
     */
    public function update(SliderRequest $request, Slider $slider): RedirectResponse
    {
        $this->homepageService->updateSlider($slider, $request->validated());

        return redirect()->route('admin.sliders.index')->with([
            'message' => 'Slider updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified slider from storage.
     */
    public function destroy(Slider $slider): RedirectResponse
    {
        $this->homepageService->deleteSlider($slider);

        return back()->with([
            'message' => 'Slider deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
