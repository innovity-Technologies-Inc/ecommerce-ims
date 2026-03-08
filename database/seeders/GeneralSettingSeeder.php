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
            'site_name' => 'smart-ecom',
            'contact_email' => 'contact@smart-ecom.com',
            'contact_phone' => '+1 234 567 890',
            'address' => '123 E-commerce St, Digital City',
            'currency_name' => 'USD',
            'currency_symbol' => '$',
            'time_zone' => 'UTC',
        ]);
    }
}
