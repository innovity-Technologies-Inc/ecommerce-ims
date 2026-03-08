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
            'Electronics' => ['Laptops', 'Smartphones', 'Accessories'],
            'Fashion' => ['T-Shirts', 'Jeans', 'Shoes'],
            'Home & Garden' => ['Furniture', 'Decor', 'Kitchen'],
        ];

        foreach ($categories as $parentName => $subCategories) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
            ]);

            foreach ($subCategories as $subName) {
                Category::create([
                    'name' => $subName,
                    'slug' => Str::slug($subName),
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
