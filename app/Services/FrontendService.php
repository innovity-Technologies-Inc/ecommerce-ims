<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FrontendService
{
    public function __construct(protected FlexSearch $flexSearch) {}

    /**
     * Get paginated products with filtering, searching, and sorting.
     */
    public function getFilteredProducts(array $data, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::with(['primaryImage', 'images', 'category', 'brand', 'variants'])->active();

        $filters = [];

        // Handle Category Filtering (Expanded to include subcategories automatically)
        if (! empty($data['category']) || ! empty($data['category_nav'])) {
            $categoryVal = $data['category'] ?? $data['category_nav'];
            $selectedCategoryIds = (array) $categoryVal;

            // Get all child category IDs for the selected categories
            $allCategoryIds = Category::whereIn('id', $selectedCategoryIds)
                ->orWhereIn('parent_id', $selectedCategoryIds)
                ->pluck('id')
                ->toArray();

            // Merge original selection with children
            $categoryIds = array_unique(array_merge($selectedCategoryIds, $allCategoryIds));

            $query->where(function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds)
                    ->orWhereIn('sub_category_id', $categoryIds);
            });
        }

        // Handle Brand Filtering (Multiple selection OR logic)
        if (! empty($data['brand'])) {
            $brandIds = (array) $data['brand'];
            $query->whereIn('brand_id', $brandIds);
        }

        // Handle Flash Sale Filtering (Multiple selection OR logic)
        if (! empty($data['flash_sale'])) {
            $flashSaleIds = (array) $data['flash_sale'];
            $query->whereHas('flashSaleItems', function ($q) use ($flashSaleIds) {
                $q->whereIn('flash_sale_id', $flashSaleIds);
            });
        }

        // Price Filtering (Manually handle to include variants and prioritized discounted prices)
        if (! empty($data['min_price']) || ! empty($data['max_price'])) {
            $minPrice = (float) ($data['min_price'] ?? 0);
            $maxPrice = (float) ($data['max_price'] ?? 999999);

            $query->where(function ($q) use ($minPrice, $maxPrice) {
                // 1. Check product base price (if it exists and is > 0)
                $q->where(function ($q1) use ($minPrice, $maxPrice) {
                    $q1->where('regular_price', '>', 0)
                        ->where(function ($q2) use ($minPrice, $maxPrice) {
                            $q2->whereBetween(DB::raw('IF(discount_price > 0, discount_price, regular_price)'), [$minPrice, $maxPrice]);
                        });
                })
                // 2. OR check variants (prioritize variant discount price, then regular price)
                    ->orWhereHas('variants', function ($v) use ($minPrice, $maxPrice) {
                        $v->where(function ($v1) use ($minPrice, $maxPrice) {
                            $v1->whereBetween(DB::raw('IF(discount_price > 0, discount_price, regular_price)'), [$minPrice, $maxPrice]);
                        });
                    });
            });
        }

        // Apply Search and dynamic filters via your FlexSearch package
        $searchTerm = $data['search'] ?? null;
        $searchableColumns = ['name', 'description', 'brand.name', 'category.name'];

        $query = $this->flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Apply Sorting
        $sort = $data['sort'] ?? 'latest';
        switch ($sort) {
            case 'newness':
                $query->latest();
                break;
            case 'price-low':
                $query->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->select('products.*')
                    ->groupBy('products.id')
                    ->orderByRaw('MIN(LEAST(
                        IF(products.discount_price > 0, products.discount_price, products.regular_price),
                        IF(product_variants.discount_price > 0, product_variants.discount_price, product_variants.regular_price)
                    )) ASC');
                break;
            case 'price-high':
                $query->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->select('products.*')
                    ->groupBy('products.id')
                    ->orderByRaw('MAX(GREATEST(
                        IF(products.discount_price > 0, products.discount_price, products.regular_price),
                        IF(product_variants.discount_price > 0, product_variants.discount_price, product_variants.regular_price)
                    )) DESC');
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'in-stock':
                $query->where(function ($q) {
                    $q->whereHas('variants', function ($v) {
                        $v->where('stock', '>', 0);
                    })->orWhere('sales_count', '>=', 0);
                });
                break;
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get related products based on category or subcategory.
     */
    public function getRelatedProducts(Product $product, int $limit = 10): Collection
    {
        return Product::where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id);
                if ($product->sub_category_id) {
                    $q->orWhere('sub_category_id', $product->sub_category_id);
                }
            })
            ->with(['primaryImage', 'variants', 'brand'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
