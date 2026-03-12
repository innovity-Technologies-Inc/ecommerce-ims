<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Brand;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BrandService
{
    /**
     * Get all brands with search and sorting.
     */
    public function getAllBrands(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Brand::query();

        // Apply Search using FlexSearch
        if (! empty($params['search'])) {
            $flexSearch = app(FlexSearch::class);
            $query = $flexSearch->apply($query, [], $params['search'], ['name', 'slug']);
        }

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a newly created brand.
     */
    public function storeBrand(array $data): Brand
    {
        $brandData = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ];

        if (isset($data['icon'])) {
            $brandData['icon'] = HelperClass::file_upload($data['icon'], 'brands');
        }

        return Brand::create($brandData);
    }

    /**
     * Update the specified brand.
     */
    public function updateBrand(Brand $brand, array $data): Brand
    {
        $brandData = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ];

        if (isset($data['icon'])) {
            if ($brand->icon) {
                HelperClass::file_delete($brand->icon);
            }
            $brandData['icon'] = HelperClass::file_upload($data['icon'], 'brands');
        }

        $brand->update($brandData);

        return $brand;
    }

    /**
     * Delete the specified brand.
     */
    public function deleteBrand(Brand $brand): void
    {
        if ($brand->icon) {
            HelperClass::file_delete($brand->icon);
        }
        $brand->delete();
    }
}
