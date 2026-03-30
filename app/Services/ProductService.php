<?php

namespace App\Services;

use App\Exports\Admin\Product\ProductTemplateExport;
use App\HelperClass;
use App\Imports\Admin\Product\ProductsImport;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductService
{
    /**
     * Get all products with search and sorting.
     */
    public function getAllProducts(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::with(['primaryImage', 'category', 'subCategory', 'brand']);

        $filters = [];
        if (! empty($params['category_id'])) {
            $filters['category_id'] = $params['category_id'];
        }
        if (! empty($params['sub_category_id'])) {
            $filters['sub_category_id'] = $params['sub_category_id'];
        }
        if (! empty($params['brand_id'])) {
            $filters['brand_id'] = $params['brand_id'];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $filters['status'] = $params['status'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'slug', 'category.name', 'brand.name'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

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
     * Import products from Excel/CSV.
     */
    public function importProducts($file): void
    {
        DB::transaction(function () use ($file) {
            Excel::import(new ProductsImport, $file);
        });
    }

    /**
     * Generate template for product import.
     */
    public function generateImportTemplate(string $format = 'csv'): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $fileName = 'product_import_template.'.$format;

        if ($format === 'xlsx') {
            return Excel::download(new ProductTemplateExport, $fileName);
        }

        // Default to CSV
        return Excel::download(new ProductTemplateExport, $fileName, \Maatwebsite\Excel\Excel::CSV);
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
                'min_stock_global' => $data['min_stock_global'] ?? 0,
                'min_stock_type' => $data['min_stock_type'] ?? 'global',
            ]);

            $this->updateWarehouseLimits($product->id, null, $data['warehouse_limits'] ?? []);

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
                'min_stock_global' => $data['min_stock_global'] ?? 0,
                'min_stock_type' => $data['min_stock_type'] ?? 'global',
            ]);

            $this->updateWarehouseLimits($product->id, null, $data['warehouse_limits'] ?? []);

            // Handle variants
            if (isset($data['variants']) && is_array($data['variants'])) {
                // To maintain inventory links, we should probably update existing variants instead of deleting all
                // but for simplicity in this standard logic, we'll keep the replace strategy but note it's destructive for stock levels.
                // Better approach: track variant IDs.
                
                $keepVariantIds = [];
                foreach ($data['variants'] as $variantData) {
                    if (isset($variantData['id'])) {
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant) {
                            $this->updateVariant($variant, $variantData);
                            $keepVariantIds[] = $variant->id;
                            continue;
                        }
                    }
                    $newVariant = $this->createVariant($product->id, $variantData);
                    $keepVariantIds[] = $newVariant->id;
                }
                $product->variants()->whereNotIn('id', $keepVariantIds)->delete();
            } else {
                $product->variants()->delete();
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

        $variant = ProductVariant::create([
            'product_id' => $productId,
            'variant_name' => $variantData['variant_name'],
            'size' => $variantData['size'] ?? null,
            'color' => $variantData['color'] ?? null,
            'sku' => $variantData['sku'] ?? $this->generateSku($productId, $variantData),
            'regular_price' => $regularPrice,
            'discount_price' => $discountPrice,
            'discount_percentage' => $discountPercentage,
            'min_stock_global' => $variantData['min_stock_global'] ?? 0,
            'min_stock_type' => $variantData['min_stock_type'] ?? 'global',
        ]);

        $this->updateWarehouseLimits($productId, $variant->id, $variantData['warehouse_limits'] ?? []);

        return $variant;
    }

    /**
     * Update an existing product variant.
     */
    protected function updateVariant(ProductVariant $variant, array $variantData): void
    {
        $regularPrice = isset($variantData['regular_price']) ? (float) $variantData['regular_price'] : null;
        $discountPercentage = isset($variantData['discount_percentage']) ? (int) $variantData['discount_percentage'] : null;
        $discountPrice = null;

        if ($regularPrice && $discountPercentage && $discountPercentage > 0) {
            $discountPrice = $regularPrice - ($regularPrice * ($discountPercentage / 100));
        }

        $variant->update([
            'variant_name' => $variantData['variant_name'],
            'sku' => $variantData['sku'] ?? $variant->sku,
            'regular_price' => $regularPrice,
            'discount_price' => $discountPrice,
            'discount_percentage' => $discountPercentage,
            'min_stock_global' => $variantData['min_stock_global'] ?? 0,
            'min_stock_type' => $variantData['min_stock_type'] ?? 'global',
        ]);

        $this->updateWarehouseLimits($variant->product_id, $variant->id, $variantData['warehouse_limits'] ?? []);
    }

    /**
     * Update warehouse-specific stock limits for a product/variant.
     */
    protected function updateWarehouseLimits(int $productId, ?int $variantId, array $limits): void
    {
        // First remove old limits for this product/variant
        \App\Models\WarehouseStockLimit::where('product_id', $productId)
            ->when($variantId, fn($q) => $q->where('product_variant_id', $variantId))
            ->when(!$variantId, fn($q) => $q->whereNull('product_variant_id'))
            ->delete();

        foreach ($limits as $warehouseId => $minStock) {
            if ($minStock !== null && $minStock !== '') {
                \App\Models\WarehouseStockLimit::create([
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                    'warehouse_id' => $warehouseId,
                    'min_stock' => $minStock,
                ]);
            }
        }
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
            ->active()
            ->with('subcategories')
            ->get();
    }

    /**
     * Fetch all active brands.
     */
    public function getBrandsForDropdown()
    {
        return \App\Models\Brand::active()->get();
    }
}
