<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Special Spring Sale',
                'subtitle' => 'Get up to 50% off on all items',
                'subtext' => 'Don\'t miss out on our limited time offers.',
                'image' => 'client/assets/images/sliders/sample1.jpg',
                'button_name' => 'Shop Now',
                'button_url' => '/products',
                'position' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'New Arrivals',
                'subtitle' => 'Check out the latest tech gadgets',
                'subtext' => 'Latest devices at unbeatable prices.',
                'image' => 'client/assets/images/sliders/sample2.jpg',
                'button_name' => 'Explore',
                'button_url' => '/products?category=electronics',
                'position' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::updateOrCreate(
                ['title' => $slider['title']],
                $slider
            );
        }
    }
}
