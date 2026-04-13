<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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
        $smartphones = Category::where('slug', 'smartphones')->first();
        $apple = Brand::where('slug', 'apple')->first();
        $samsung = Brand::where('slug', 'samsung')->first();

        $fashion = Category::where('slug', 'fashion')->first();
        $tshirts = Category::where('slug', 't-shirts')->first();
        $zara = Brand::where('slug', 'zara')->first();

        $products = [
            [
                'category_id' => $electronics->id,
                'sub_category_id' => $laptops->id,
                'brand_id' => $apple->id,
                'name' => 'MacBook Pro M2',
                'short_description' => 'The ultimate pro laptop.',
                'description' => 'Powerful laptop from Apple with M2 chip, 16GB RAM, 512GB SSD.',
                'regular_price' => 1999.99,
                'is_featured' => true,
                'status' => true,
                'min_stock_global' => 5,
                'min_stock_type' => 'global',
                'variants' => [
                    [
                        'variant_name' => '14-inch Space Gray',
                        'size' => '14-inch',
                        'color' => 'Space Gray',
                        'regular_price' => 1999.99,
                        'min_stock_global' => 2,
                        'sku' => 'MBP-M2-14-SG',
                    ],
                    [
                        'variant_name' => '16-inch Silver',
                        'size' => '16-inch',
                        'color' => 'Silver',
                        'regular_price' => 2499.99,
                        'min_stock_global' => 2,
                        'sku' => 'MBP-M2-16-SLV',
                    ],
                ],
            ],
            [
                'category_id' => $electronics->id,
                'sub_category_id' => $smartphones->id,
                'brand_id' => $samsung->id,
                'name' => 'Samsung Galaxy S23',
                'short_description' => 'The next generation of Galaxy.',
                'description' => 'Premium smartphone with high-end camera and performance.',
                'regular_price' => 899.99,
                'is_hot_deal' => true,
                'status' => true,
                'min_stock_global' => 10,
                'min_stock_type' => 'global',
                'variants' => [
                    ['variant_name' => 'Phantom Black / 256GB', 'color' => 'Black', 'regular_price' => 899.99, 'min_stock_global' => 5, 'sku' => 'S23-PB-256'],
                    ['variant_name' => 'Cream / 512GB', 'color' => 'Cream', 'regular_price' => 999.99, 'min_stock_global' => 5, 'sku' => 'S23-CR-512'],
                ],
            ],
            [
                'category_id' => $fashion->id,
                'sub_category_id' => $tshirts->id,
                'brand_id' => $zara->id,
                'name' => 'Classic Cotton T-Shirt',
                'short_description' => '100% Organic Cotton.',
                'description' => 'High quality cotton t-shirt for daily wear. Breathable and comfortable.',
                'regular_price' => 25.00,
                'discount_percentage' => 20,
                'is_new_arrival' => true,
                'status' => true,
                'min_stock_global' => 20,
                'min_stock_type' => 'global',
                'variants' => [
                    ['variant_name' => 'Black / S', 'size' => 'S', 'color' => 'Black', 'regular_price' => 25.00, 'min_stock_global' => 10, 'sku' => 'TSHIRT-BK-S'],
                    ['variant_name' => 'Black / M', 'size' => 'M', 'color' => 'Black', 'regular_price' => 25.00, 'min_stock_global' => 10, 'sku' => 'TSHIRT-BK-M'],
                    ['variant_name' => 'White / S', 'size' => 'S', 'color' => 'White', 'regular_price' => 25.00, 'min_stock_global' => 10, 'sku' => 'TSHIRT-WT-S'],
                ],
            ],
        ];

        foreach ($products as $data) {
            $slug = \Illuminate\Support\Str::slug($data['name']);
            $product = Product::where('slug', $slug)->first();

            if ($product) {
                $productService->updateProduct($product, $data);
            } else {
                $productService->storeProduct($data);
            }
        }
    }
}
