<?php

namespace Database\Seeders;

use App\Models\SectionSetting;
use Illuminate\Database\Seeder;

class SectionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section_name' => 'bestsellers',
                'section_title' => 'Best Selling Products',
                'section_subtitle' => 'Our most popular picks for you.',
                'mode' => 'organic',
                'limit' => 8,
                'is_visible' => true,
            ],
            [
                'section_name' => 'hot_deals',
                'section_title' => 'Hot Deals',
                'section_subtitle' => 'Unbeatable prices for a limited time.',
                'mode' => 'organic',
                'limit' => 2,
                'is_visible' => true,
            ],
            [
                'section_name' => 'featured',
                'section_title' => 'Featured Products',
                'section_subtitle' => 'Handpicked items just for you.',
                'mode' => 'organic',
                'limit' => 4,
                'is_visible' => true,
            ],
            [
                'section_name' => 'recently_added',
                'section_title' => 'New Arrivals',
                'section_subtitle' => 'Stay ahead with our latest additions.',
                'mode' => 'organic',
                'limit' => 8,
                'is_visible' => true,
            ],
        ];

        foreach ($sections as $section) {
            SectionSetting::create($section);
        }
    }
}
