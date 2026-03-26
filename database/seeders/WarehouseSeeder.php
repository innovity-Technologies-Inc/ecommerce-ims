<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::firstOrCreate(
            ['name' => 'Main Warehouse'],
            ['location' => 'Central Hub', 'is_quarantine' => false]
        );

        Warehouse::firstOrCreate(
            ['name' => 'Secondary Warehouse'],
            ['location' => 'North Branch', 'is_quarantine' => false]
        );

        Warehouse::firstOrCreate(
            ['name' => 'Quarantine'],
            ['location' => 'Restricted Area', 'is_quarantine' => true]
        );
    }
}
