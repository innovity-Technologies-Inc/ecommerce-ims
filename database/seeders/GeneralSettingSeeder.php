<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GeneralSetting::create([
            'business_name' => 'smart-ecom',
            'meta_title' => 'smart-ecom | Your Best E-commerce Platform',
            'meta_description' => 'A modern e-commerce platform built with Laravel 12.',
            'currency' => 'USD',
            // Icons/Logos can be added as placeholders
            'dark_logo' => 'admin_assets/assets/images/logo-dark.png',
            'light_logo' => 'admin_assets/assets/images/logo-light.png',
            'favicon' => 'admin_assets/assets/images/favicon.ico',
            'breadcrumb_image' => 'admin_assets/assets/images/breadcrumb.jpg',
        ]);
    }
}
