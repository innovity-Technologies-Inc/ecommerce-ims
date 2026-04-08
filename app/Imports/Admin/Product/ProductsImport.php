<?php

namespace App\Imports\Admin\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected ?Product $lastProduct = null;

    protected string $lastProductName = '';

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $productName = trim($row['product_name'] ?? '');

            // If product name is provided and different from last one, it's a new product or updating existing one
            if (! empty($productName) && $productName !== $this->lastProductName) {
                $categoryName = trim($row['category'] ?? '');
                $subCategoryName = trim($row['subcategory'] ?? '');
                $brandName = trim($row['brand'] ?? '');

                $category = Category::where('name', $categoryName)->whereNull('parent_id')->first();
                $subCategory = null;
                if ($category && ! empty($subCategoryName)) {
                    $subCategory = Category::where('name', $subCategoryName)->where('parent_id', $category->id)->first();
                }
                $brand = Brand::where('name', $brandName)->first();

                $regularPrice = $row['regular_price'] ?? 0;
                $discountPercentage = $row['discount_percentage'] ?? 0;
                $discountPrice = $this->calculateDiscountPrice($regularPrice, $discountPercentage);

                $productData = [
                    'category_id' => $category?->id,
                    'sub_category_id' => $subCategory?->id,
                    'brand_id' => $brand?->id,
                    'name' => $productName,
                    'slug' => Str::slug($productName),
                    'short_description' => $row['short_description'] ?? null,
                    'description' => $row['description'] ?? null,
                    'regular_price' => $regularPrice,
                    'discount_price' => $discountPrice,
                    'discount_percentage' => $discountPercentage,
                    'is_new_arrival' => (bool) ($row['is_new_arrival'] ?? false),
                    'is_hot_deal' => (bool) ($row['is_hot_deal'] ?? false),
                    'is_featured' => (bool) ($row['is_featured'] ?? false),
                    'status' => strtolower(trim($row['status'] ?? '')) === 'active' ? 1 : 0,
                    'min_stock_global' => $row['min_stock_global'] ?? 0,
                    'min_stock_type' => $row['min_stock_type'] ?? 'global',
                ];

                $this->lastProduct = Product::updateOrCreate(
                    ['name' => $productName],
                    $productData
                );
                $this->lastProductName = $productName;
            }

            // If we have a variant for the current product
            $variantName = trim($row['variant_name'] ?? '');
            if ($this->lastProduct && ! empty($variantName)) {
                $vRegularPrice = $row['variant_regular_price'] ?? $this->lastProduct->regular_price;
                $vDiscountPercentage = $row['variant_discount_percentage'] ?? 0;
                $vDiscountPrice = $this->calculateDiscountPrice($vRegularPrice, $vDiscountPercentage);

                $variantData = [
                    'variant_name' => $variantName,
                    'size' => $row['variant_size'] ?? null,
                    'color' => $row['variant_color'] ?? null,
                    'sku' => $row['variant_sku'] ?? (Str::slug($this->lastProduct->name).'-'.Str::slug($variantName).'-'.rand(1000, 9999)),
                    'regular_price' => $vRegularPrice,
                    'discount_price' => $vDiscountPrice,
                    'discount_percentage' => $vDiscountPercentage,
                    'min_stock_global' => $row['variant_min_stock_global'] ?? 0,
                    'min_stock_type' => $row['variant_min_stock_type'] ?? 'global',
                ];

                ProductVariant::updateOrCreate(
                    ['product_id' => $this->lastProduct->id, 'variant_name' => $variantName],
                    $variantData
                );
            }
        }
    }

    protected function calculateDiscountPrice($regularPrice, $percentage): float
    {
        if ($regularPrice > 0 && $percentage > 0) {
            return $regularPrice - ($regularPrice * ($percentage / 100));
        }

        return 0;
    }

    public function rules(): array
    {
        return [
            '*.product_name' => ['nullable', 'string', 'max:255'],
            '*.category' => ['nullable', 'string'],
            '*.brand' => ['nullable', 'string'],
            '*.regular_price' => ['nullable', 'numeric'],
            '*.discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            '*.min_stock_global' => ['nullable', 'integer', 'min:0'],
            '*.variant_min_stock_global' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
