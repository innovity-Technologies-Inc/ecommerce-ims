<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple', 'icon' => 'client/assets/images/brands/apple.png'],
            ['name' => 'Samsung', 'icon' => 'client/assets/images/brands/samsung.png'],
            ['name' => 'Nike', 'icon' => 'client/assets/images/brands/nike.png'],
            ['name' => 'Adidas', 'icon' => 'client/assets/images/brands/adidas.png'],
            ['name' => 'Zara', 'icon' => 'client/assets/images/brands/zara.png'],
            ['name' => 'HP', 'icon' => 'client/assets/images/brands/hp.png'],
            ['name' => 'Dell', 'icon' => 'client/assets/images/brands/dell.png'],
            ['name' => 'Sony', 'icon' => 'client/assets/images/brands/sony.png'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'icon' => $brand['icon'],
                    'status' => 1,
                ]
            );
        }
    }
}
