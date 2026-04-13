<?php

namespace Database\Seeders;

use App\Models\PolicySetting;
use Illuminate\Database\Seeder;

class PolicySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PolicySetting::updateOrCreate(
            ['id' => 1],
            [
                'privacy_policy' => '<h3>Privacy Policy</h3><p>We value your privacy...</p>',
                'return_policy' => '<h3>Return Policy</h3><p>We offer a 30-day return policy...</p>',
            ]
        );
    }
}
