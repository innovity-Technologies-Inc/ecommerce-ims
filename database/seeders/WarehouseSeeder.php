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
            ['name' => 'Alabama Hub', 'location' => 'Montgomery, AL'],
            ['name' => 'Alaska Logistics', 'location' => 'Juneau, AK'],
            ['name' => 'Arizona Distribution', 'location' => 'Phoenix, AZ'],
            ['name' => 'Arkansas Warehouse', 'location' => 'Little Rock, AR'],
            ['name' => 'California Central', 'location' => 'Sacramento, CA'],
            ['name' => 'Colorado Gateway', 'location' => 'Denver, CO'],
            ['name' => 'Connecticut Hub', 'location' => 'Hartford, CT'],
            ['name' => 'Delaware Depot', 'location' => 'Dover, DE'],
            ['name' => 'Florida South', 'location' => 'Tallahassee, FL'],
            ['name' => 'Georgia Hub', 'location' => 'Atlanta, GA'],
            ['name' => 'Hawaii Pacific', 'location' => 'Honolulu, HI'],
            ['name' => 'Idaho Logistics', 'location' => 'Boise, ID'],
            ['name' => 'Illinois Central', 'location' => 'Springfield, IL'],
            ['name' => 'Indiana Hub', 'location' => 'Indianapolis, IN'],
            ['name' => 'Iowa Distribution', 'location' => 'Des Moines, IA'],
            ['name' => 'Kansas Gateway', 'location' => 'Topeka, KS'],
            ['name' => 'Kentucky Depot', 'location' => 'Frankfort, KY'],
            ['name' => 'Louisiana South', 'location' => 'Baton Rouge, LA'],
            ['name' => 'Maine Logistics', 'location' => 'Augusta, ME'],
            ['name' => 'Maryland Hub', 'location' => 'Annapolis, MD'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['name' => $warehouse['name']],
                [
                    'location' => $warehouse['location'],
                ]
            );
        }
    }
}
