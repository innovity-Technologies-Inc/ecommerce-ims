<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;

class BannerService
{
    /**
     * Get all banners (always returns the 6 predefined template placements).
     */
    public function getAllBanners()
    {
        $slugs = [
            'home_1_left',
            'home_1_middle',
            'home_1_right',
            'home_2_full',
            'cart_sidebar',
            'menu_banner',
        ];

        $banners = Banner::whereIn('slug', $slugs)->get()->keyBy('slug');

        $result = collect();
        foreach ($slugs as $slug) {
            if ($banners->has($slug)) {
                $result->push($banners->get($slug));
            } else {
                $result->push(new Banner([
                    'slug' => $slug,
                    'link' => '#',
                    'image' => $slug === 'home_2_full'
                        ? 'client/assets/images/banner-image/9.jpg'
                        : ($slug === 'menu_banner' ? 'client/assets/images/banner-image/7.jpg' : 'client/assets/images/banner-image/8.jpg'),
                ]));
            }
        }

        return $result;
    }

    /**
     * Get banner by slug (or return a new instanced template if not saved).
     */
    public function getBannerBySlug(string $slug)
    {
        $slugs = [
            'home_1_left',
            'home_1_middle',
            'home_1_right',
            'home_2_full',
            'cart_sidebar',
            'menu_banner',
        ];

        if (! in_array($slug, $slugs)) {
            abort(404);
        }

        return Banner::where('slug', $slug)->first() ?? new Banner([
            'slug' => $slug,
            'link' => '#',
            'image' => $slug === 'home_2_full'
                ? 'client/assets/images/banner-image/9.jpg'
                : ($slug === 'menu_banner' ? 'client/assets/images/banner-image/7.jpg' : 'client/assets/images/banner-image/8.jpg'),
        ]);
    }

    /**
     * Update or create banner details by slug.
     */
    public function updateBanner(string $slug, array $data)
    {
        try {
            $banner = Banner::where('slug', $slug)->first() ?? new Banner(['slug' => $slug]);

            if (isset($data['image'])) {
                // Delete old image if it's not a default static asset
                if ($banner->image && ! str_contains($banner->image, 'client/assets/images/')) {
                    HelperClass::file_delete($banner->image);
                }
                $data['image'] = HelperClass::file_upload($data['image'], 'banners');
            }

            if (! $banner->exists) {
                $banner->fill($data);
                $banner->save();
            } else {
                $banner->update($data);
            }

            return $banner;
        } catch (\Exception $e) {
            Log::error('Error updating banner: '.$e->getMessage());

            return false;
        }
    }
}
