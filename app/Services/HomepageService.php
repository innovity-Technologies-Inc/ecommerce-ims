<?php

namespace App\Services;

use App\HelperClass;
use App\Models\SectionSetting;
use App\Models\Slider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomepageService
{
    /**
     * Store a new slider.
     */
    public function storeSlider(array $data): Slider
    {
        $data['image'] = HelperClass::file_upload($data['image'], 'sliders');
        $data['is_active'] = isset($data['is_active']);

        return Slider::create($data);
    }

    /**
     * Update an existing slider.
     */
    public function updateSlider(Slider $slider, array $data): Slider
    {
        if (isset($data['image'])) {
            HelperClass::file_delete($slider->image);
            $data['image'] = HelperClass::file_upload($data['image'], 'sliders');
        }

        $data['is_active'] = isset($data['is_active']);

        $slider->update($data);

        return $slider;
    }

    /**
     * Delete a slider.
     */
    public function deleteSlider(Slider $slider): void
    {
        HelperClass::file_delete($slider->image);
        $slider->delete();
    }

    /**
     * Update section settings.
     */
    public function updateSectionSetting(string $sectionName, array $data): SectionSetting
    {
        return DB::transaction(function () use ($sectionName, $data) {
            $section = SectionSetting::firstOrCreate(['section_name' => $sectionName]);

            $section->update([
                'section_title' => $data['section_title'] ?? $section->section_title,
                'section_subtitle' => $data['section_subtitle'] ?? $section->section_subtitle,
                'mode' => $data['mode'] ?? $section->mode,
                'limit' => $data['limit'] ?? $section->limit,
                'is_visible' => isset($data['is_visible']),
            ]);

            if ($section->mode === 'custom' && isset($data['product_ids'])) {
                $syncData = [];
                foreach ($data['product_ids'] as $index => $productId) {
                    $syncData[$productId] = ['position' => $index];
                }
                $section->products()->sync($syncData);
            }

            return $section;
        });
    }

    /**
     * Get active sliders for the homepage.
     */
    public function getActiveSliders(): Collection
    {
        return Slider::active()->orderBy('position')->get();
    }

    /**
     * Get products for a specific section based on its mode.
     */
    public function getSectionProducts(string $sectionName): Collection
    {
        $section = SectionSetting::where('section_name', $sectionName)->first();

        if (! $section || ! $section->is_visible) {
            return collect();
        }

        if ($section->mode === 'custom') {
            return $section->products;
        }

        // Organic mode: Logic depends on the section
        if ($sectionName === 'bestsellers') {
            return \App\Models\Product::orderBy('sales_count', 'desc')
                ->limit($section->limit)
                ->get();
        }

        return collect();
    }
}
