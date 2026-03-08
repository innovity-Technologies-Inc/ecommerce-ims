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
        Slider::create([
            'image' => 'sliders/sample1.jpg',
            'title' => 'Special Spring Sale',
            'subtitle' => 'Get up to 50% off on all items',
            'btn_url' => '/products',
            'position' => 1,
            'is_active' => true,
        ]);

        Slider::create([
            'image' => 'sliders/sample2.jpg',
            'title' => 'New Arrivals',
            'subtitle' => 'Check out the latest tech gadgets',
            'btn_url' => '/products?category=electronics',
            'position' => 2,
            'is_active' => true,
        ]);
    }
}
