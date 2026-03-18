<?php

namespace App\Services;

use App\HelperClass;
use App\Models\SectionSetting;
use App\Models\Slider;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomepageService
{
    /**
     * Get all sliders with search and sorting.
     */
    public function getAllSliders(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Slider::query();

        $filters = [];
        if (isset($params['is_active']) && $params['is_active'] !== '') {
            $filters['is_active'] = $params['is_active'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['title', 'subtitle', 'subtext', 'button_name'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('title', 'asc');
                break;
            case 'z-a':
                $query->orderBy('title', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

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

            $updateData = [
                'section_title' => $data['section_title'] ?? $section->section_title,
                'section_subtitle' => $data['section_subtitle'] ?? $section->section_subtitle,
                'mode' => $data['mode'] ?? $section->mode,
                'limit' => $data['limit'] ?? $section->limit,
                'is_visible' => isset($data['is_visible']),
            ];

            if (isset($data['background_image'])) {
                if ($section->background_image) {
                    HelperClass::file_delete($section->background_image);
                }
                $updateData['background_image'] = HelperClass::file_upload($data['background_image'], 'sections');
            }

            $section->update($updateData);

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

        // Custom mode: Logic is the same for all sections
        if ($section->mode === 'custom') {
            $products = $section->products()->with(['primaryImage', 'variants'])->get();
            if ($products->isNotEmpty()) {
                return $products;
            }
        }

        // Organic mode: Logic depends on the section
        $query = \App\Models\Product::with(['primaryImage', 'variants'])->latest();

        switch ($sectionName) {
            case 'bestsellers':
                return $query->orderBy('sales_count', 'desc')->limit($section->limit)->get();

            case 'hot_deals':
                return $query->where('is_hot_deal', true)->limit(2)->get();

            case 'featured':
                return $query->where('is_featured', true)->limit(4)->get();

            case 'recently_added':
                return $query->limit($section->limit)->get();

            case 'top_picks':
                return $query->where('is_top_pick', true)->limit($section->limit)->get();
        }

        return collect();
    }
}
