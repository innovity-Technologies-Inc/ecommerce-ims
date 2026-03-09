<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Standard Shipping',
                'price' => 50.00,
                'short_description' => 'Delivery within 3-5 working days.',
                'status' => true,
            ],
            [
                'name' => 'Express Shipping',
                'price' => 150.00,
                'short_description' => 'Delivery within 1-2 working days.',
                'status' => true,
            ],
            [
                'name' => 'Free Shipping',
                'price' => 0.00,
                'short_description' => 'Delivery within 7-10 working days (Orders over $1000).',
                'status' => true,
            ],
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(['name' => $method['name']], $method);
        }
    }
}
