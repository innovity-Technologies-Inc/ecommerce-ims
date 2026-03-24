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
        Warehouse::create(['name' => 'Main Warehouse', 'location' => 'Downtown']);
        Warehouse::create(['name' => 'Secondary Warehouse', 'location' => 'Uptown']);
        Warehouse::create(['name' => 'North Warehouse', 'location' => 'North Point']);
    }
}
