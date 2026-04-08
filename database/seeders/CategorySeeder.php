<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'subcategories' => ['Laptops', 'Smartphones', 'Cameras', 'Accessories'],
            ],
            [
                'name' => 'Fashion',
                'subcategories' => ['T-Shirts', 'Jeans', 'Shoes', 'Watches'],
            ],
            [
                'name' => 'Home & Garden',
                'subcategories' => ['Furniture', 'Kitchen', 'Decor', 'Lighting'],
            ],
            [
                'name' => 'Beauty',
                'subcategories' => ['Skincare', 'Makeup', 'Fragrance', 'Haircare'],
            ],
        ];

        foreach ($categories as $catData) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'status' => 1,
                    'parent_id' => null,
                ]
            );

            foreach ($catData['subcategories'] as $subName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($subName)],
                    [
                        'name' => $subName,
                        'status' => 1,
                        'parent_id' => $parent->id,
                    ]
                );
            }
        }
    }
}
