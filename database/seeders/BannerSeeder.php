<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'slug' => 'home_1_left',
                'image' => 'client/assets/images/banner-image/8.jpg',
                'link' => '#',
            ],
            [
                'slug' => 'home_1_middle',
                'image' => 'client/assets/images/banner-image/9.jpg',
                'link' => '#',
            ],
            [
                'slug' => 'home_1_right',
                'image' => 'client/assets/images/banner-image/10.jpg',
                'link' => '#',
            ],
            [
                'slug' => 'home_2_full',
                'image' => 'client/assets/images/banner-image/17.jpg',
                'link' => '#',
            ],
            [
                'slug' => 'cart_sidebar',
                'image' => 'client/assets/images/banner-image/5.jpg',
                'link' => '#',
            ],
            [
                'slug' => 'menu_banner',
                'image' => 'client/assets/images/banner-image/banner-menu.jpg',
                'link' => '#',
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(['slug' => $banner['slug']], $banner);
        }
    }
}
