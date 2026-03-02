<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ProductService $productService): void
    {
        // Create Categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $laptops = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent_id' => $electronics->id,
        ]);

        $fashion = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
        ]);

        $tshirts = Category::create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
            'parent_id' => $fashion->id,
        ]);

        // Create Products using ProductService
        $productService->storeProduct([
            'category_id' => $electronics->id,
            'sub_category_id' => $laptops->id,
            'name' => 'MacBook Pro M2',
            'description' => 'Powerful laptop from Apple with M2 chip.',
            'variants' => [
                [
                    'size' => '14-inch',
                    'color' => 'Space Gray',
                    'price' => 1999.99,
                ],
                [
                    'size' => '16-inch',
                    'color' => 'Silver',
                    'price' => 2499.99,
                ],
            ],
        ]);

        $productService->storeProduct([
            'category_id' => $fashion->id,
            'sub_category_id' => $tshirts->id,
            'name' => 'Classic Cotton T-Shirt',
            'description' => 'High quality cotton t-shirt for daily wear.',
            'variants' => [
                ['size' => 'S', 'color' => 'Black', 'price' => 19.99],
                ['size' => 'M', 'color' => 'Black', 'price' => 19.99],
                ['size' => 'L', 'color' => 'Black', 'price' => 19.99],
                ['size' => 'S', 'color' => 'White', 'price' => 19.99],
                ['size' => 'M', 'color' => 'White', 'price' => 19.99],
            ],
        ]);
    }
}
