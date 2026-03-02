<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Store a new product with its variants and images.
     */
    public function storeProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'brand_id' => $data['brand_id'] ?? null,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'] ?? null,
            ]);

            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($product->id, $variantData);
                }
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $index => $image) {
                    $path = HelperClass::file_upload($image, 'products');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => ($index === 0),
                    ]);
                }
            }

            return $product;
        });
    }

    /**
     * Update an existing product with its variants and images.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'brand_id' => $data['brand_id'] ?? null,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'] ?? null,
            ]);

            // Simple strategy for variants: Replace them
            $product->variants()->delete();
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($product->id, $variantData);
                }
            }

            // For images: Add new ones if provided (optional: add logic to delete existing specific images)
            if (isset($data['images']) && is_array($data['images'])) {
                // If there are no existing images, the first new one is primary
                $hasPrimary = $product->images()->where('is_primary', true)->exists();

                foreach ($data['images'] as $index => $image) {
                    $path = HelperClass::file_upload($image, 'products');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => (!$hasPrimary && $index === 0),
                    ]);
                    $hasPrimary = true;
                }
            }

            return $product;
        });
    }

    /**
     * Delete a product and its related records.
     */
    public function deleteProduct(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Delete images from storage
            foreach ($product->images as $image) {
                HelperClass::file_delete($image->image_path);
            }

            // Delete database records (assuming cascade isn't set, we do it manually)
            $product->images()->delete();
            $product->variants()->delete();

            return $product->delete();
        });
    }

    /**
     * Create a single variant for a product.
     */
    protected function createVariant(int $productId, array $variantData): ProductVariant
    {
        // Explicitly check if product exists (as requested: "Because we are avoiding foreign keys, your Service Class becomes the Guard")
        if (! Product::where('id', $productId)->exists()) {
            throw new \Exception("Product with ID {$productId} does not exist.");
        }

        return ProductVariant::create([
            'product_id' => $productId,
            'size' => $variantData['size'] ?? null,
            'color' => $variantData['color'] ?? null,
            'sku' => $variantData['sku'] ?? $this->generateSku($productId, $variantData),
            'price' => $variantData['price'],
        ]);
    }

    /**
     * Generate SKU for a variant.
     */
    protected function generateSku(int $productId, array $variantData): string
    {
        $sku = 'PROD-'.$productId;
        if (! empty($variantData['size'])) {
            $sku .= '-'.strtoupper($variantData['size']);
        }
        if (! empty($variantData['color'])) {
            $sku .= '-'.strtoupper($variantData['color']);
        }
        $sku .= '-'.Str::random(4);

        return strtoupper($sku);
    }

    /**
     * Fetch parent categories with their sub-categories.
     */
    public function getCategoriesForDropdown()
    {
        return Category::whereNull('parent_id')
            ->with('subcategories')
            ->get();
    }
}
