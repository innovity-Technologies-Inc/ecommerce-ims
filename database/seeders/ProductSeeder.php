<?php

namespace Database\Seeders;

use App\Models\Brand;
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
        $electronics = Category::where('slug', 'electronics')->first();
        $laptops = Category::where('slug', 'laptops')->first();
        $apple = Brand::where('slug', 'apple')->first();

        $fashion = Category::where('slug', 'fashion')->first();
        $tshirts = Category::where('slug', 't-shirts')->first();
        $zara = Brand::where('slug', 'zara')->first();

        // MacBook Pro
        $productService->storeProduct([
            'category_id' => $electronics->id,
            'sub_category_id' => $laptops->id,
            'brand_id' => $apple->id,
            'name' => 'MacBook Pro M2',
            'short_description' => 'The ultimate pro laptop.',
            'description' => 'Powerful laptop from Apple with M2 chip, 16GB RAM, 512GB SSD.',
            'regular_price' => 1999.99,
            'is_featured' => true,
            'variants' => [
                [
                    'variant_name' => '14-inch Space Gray',
                    'size' => '14-inch',
                    'color' => 'Space Gray',
                    'regular_price' => 1999.99,
                    'stock' => 10,
                ],
                [
                    'variant_name' => '16-inch Silver',
                    'size' => '16-inch',
                    'color' => 'Silver',
                    'regular_price' => 2499.99,
                    'stock' => 5,
                ],
            ],
        ]);

        // T-Shirt
        $productService->storeProduct([
            'category_id' => $fashion->id,
            'sub_category_id' => $tshirts->id,
            'brand_id' => $zara->id,
            'name' => 'Classic Cotton T-Shirt',
            'short_description' => '100% Organic Cotton.',
            'description' => 'High quality cotton t-shirt for daily wear. Breathable and comfortable.',
            'regular_price' => 25.00,
            'discount_percentage' => 20,
            'is_new_arrival' => true,
            'variants' => [
                ['variant_name' => 'Black / S', 'size' => 'S', 'color' => 'Black', 'regular_price' => 25.00, 'stock' => 50],
                ['variant_name' => 'Black / M', 'size' => 'M', 'color' => 'Black', 'regular_price' => 25.00, 'stock' => 50],
                ['variant_name' => 'White / S', 'size' => 'S', 'color' => 'White', 'regular_price' => 25.00, 'stock' => 50],
            ],
        ]);
    }
}
