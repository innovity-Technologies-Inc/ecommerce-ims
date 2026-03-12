<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Get all products with search and sorting.
     */
    public function getAllProducts(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::with(['primaryImage', 'category', 'brand']);

        // Apply Search using FlexSearch
        if (! empty($params['search'])) {
            $flexSearch = app(FlexSearch::class);
            $query = $flexSearch->apply($query, [], $params['search'], ['name', 'slug', 'category.name', 'brand.name']);
        }

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a newly created product with variants and images.
     */
    public function storeProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $regularPrice = $data['regular_price'] ?? null;
            $discountPercentage = isset($data['discount_percentage']) ? (int) $data['discount_percentage'] : null;
            $discountPrice = null;

            if ($regularPrice && $discountPercentage && $discountPercentage > 0) {
                $discountPrice = $regularPrice - ($regularPrice * ($discountPercentage / 100));
            }

            $product = Product::create([
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'brand_id' => $data['brand_id'] ?? null,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'short_description' => $data['short_description'] ?? null,
                'regular_price' => $regularPrice,
                'discount_price' => $discountPrice,
                'discount_percentage' => $discountPercentage,
                'description' => $data['description'] ?? null,
                'is_new_arrival' => isset($data['is_new_arrival']),
                'is_hot_deal' => isset($data['is_hot_deal']),
                'is_featured' => isset($data['is_featured']),
                'status' => isset($data['status']),
                'sales_count' => 0,
            ]);

            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($product->id, $variantData);
                }
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $index => $image) {
                    $this->createProductImage($product->id, $image, $index === 0);
                }
            }

            return $product;
        });
    }

    /**
     * Update the specified product.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $regularPrice = $data['regular_price'] ?? null;
            $discountPercentage = isset($data['discount_percentage']) ? (int) $data['discount_percentage'] : null;
            $discountPrice = null;

            if ($regularPrice && $discountPercentage && $discountPercentage > 0) {
                $discountPrice = $regularPrice - ($regularPrice * ($discountPercentage / 100));
            }

            $product->update([
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'brand_id' => $data['brand_id'] ?? null,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'short_description' => $data['short_description'] ?? null,
                'regular_price' => $regularPrice,
                'discount_price' => $discountPrice,
                'discount_percentage' => $discountPercentage,
                'description' => $data['description'] ?? null,
                'is_new_arrival' => isset($data['is_new_arrival']),
                'is_hot_deal' => isset($data['is_hot_deal']),
                'is_featured' => isset($data['is_featured']),
                'status' => isset($data['status']),
            ]);

            // Simple strategy for variants: Replace them
            $product->variants()->delete();
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($product->id, $variantData);
                }
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $image) {
                    $this->createProductImage($product->id, $image, false);
                }
            }

            return $product;
        });
    }

    /**
     * Delete a product and its associated files.
     */
    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                HelperClass::file_delete($image->image_path);
            }
            $product->delete();
        });
    }

    /**
     * Toggle the status of a product.
     */
    public function toggleStatus(int $productId): bool
    {
        $product = Product::findOrFail($productId);
        $product->status = ! $product->status;

        return $product->save();
    }

    /**
     * Create a product variant.
     */
    protected function createVariant(int $productId, array $variantData): ProductVariant
    {
        $regularPrice = isset($variantData['regular_price']) ? (float) $variantData['regular_price'] : null;
        $discountPercentage = isset($variantData['discount_percentage']) ? (int) $variantData['discount_percentage'] : null;
        $discountPrice = null;

        if ($regularPrice && $discountPercentage && $discountPercentage > 0) {
            $discountPrice = $regularPrice - ($regularPrice * ($discountPercentage / 100));
        }

        return ProductVariant::create([
            'product_id' => $productId,
            'variant_name' => $variantData['variant_name'],
            'size' => $variantData['size'] ?? null,
            'color' => $variantData['color'] ?? null,
            'sku' => $variantData['sku'] ?? $this->generateSku($productId, $variantData),
            'regular_price' => $regularPrice,
            'discount_price' => $discountPrice,
            'discount_percentage' => $discountPercentage,
            'stock' => $variantData['stock'] ?? null,
        ]);
    }

    /**
     * Create a product image.
     */
    protected function createProductImage(int $productId, $file, bool $isPrimary): ProductImage
    {
        $path = HelperClass::file_upload($file, 'products');

        return ProductImage::create([
            'product_id' => $productId,
            'image_path' => $path,
            'is_primary' => $isPrimary,
        ]);
    }

    /**
     * Generate SKU for a variant.
     */
    protected function generateSku(int $productId, array $variantData): string
    {
        return 'PROD-'.$productId.'-'.strtoupper(Str::random(5));
    }

    /**
     * Fetch parent categories with their sub-categories.
     */
    public function getCategoriesForDropdown()
    {
        return \App\Models\Category::whereNull('parent_id')
            ->with('subcategories')
            ->get();
    }

    /**
     * Fetch all brands.
     */
    public function getBrandsForDropdown()
    {
        return \App\Models\Brand::all();
    }
}
