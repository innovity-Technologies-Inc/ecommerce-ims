<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;

class BannerService
{
    /**
     * Get all banners.
     */
    public function getAllBanners()
    {
        return Banner::all();
    }

    /**
     * Get banner by slug.
     */
    public function getBannerBySlug(string $slug)
    {
        return Banner::where('slug', $slug)->first();
    }

    /**
     * Get banner by id.
     */
    public function getBannerById(int $id)
    {
        return Banner::findOrFail($id);
    }

    /**
     * Update banner details.
     */
    public function updateBanner(int $id, array $data)
    {
        try {
            $banner = Banner::findOrFail($id);

            if (isset($data['image'])) {
                // Delete old image if it's not a default static asset
                if ($banner->image && ! str_contains($banner->image, 'client/assets/images/')) {
                    HelperClass::file_delete($banner->image);
                }
                $data['image'] = HelperClass::file_upload($data['image'], 'banners');
            }

            $banner->update($data);

            return $banner;
        } catch (\Exception $e) {
            Log::error('Error updating banner: '.$e->getMessage());

            return false;
        }
    }
}
