<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandService
{
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
