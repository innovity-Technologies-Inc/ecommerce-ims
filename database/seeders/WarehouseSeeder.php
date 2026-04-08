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
        $warehouses = [
            ['name' => 'Main Warehouse', 'location' => 'New York'],
            ['name' => 'West Coast Hub', 'location' => 'Los Angeles'],
            ['name' => 'Quarantine', 'location' => 'Secured Area', 'is_quarantine' => 1],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['name' => $warehouse['name']],
                [
                    'location' => $warehouse['location'],
                    'is_quarantine' => $warehouse['is_quarantine'] ?? 0,
                ]
            );
        }
    }
}
