<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SectionSetting;
use App\Services\HomepageService;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function __construct(protected HomepageService $homepageService) {}

    public function home()
    {
        $sliders = $this->homepageService->getActiveSliders();
        $bestsellerSection = SectionSetting::where('section_name', 'bestsellers')->first();
        $bestsellingProducts = $this->homepageService->getSectionProducts('bestsellers');

        $hotDealProducts = Product::where('is_hot_deal', true)
            ->with(['primaryImage', 'variants'])
            ->latest()
            ->limit(10)
            ->get();

        $newArrivalProducts = Product::where('is_new_arrival', true)
            ->with(['primaryImage', 'variants'])
            ->latest()
            ->limit(10)
            ->get();

        $featuredProducts = Product::where('is_featured', true)
            ->with(['primaryImage', 'variants'])
            ->latest()
            ->limit(10)
            ->get();

        $recentlyAddedProducts = Product::with(['primaryImage', 'variants'])
            ->latest()
            ->limit(17) // Matches the static count in feature_1 (2 * 8 + 1)
            ->get();

        return view('client.homepage', compact(
            'sliders', 
            'bestsellerSection', 
            'bestsellingProducts', 
            'hotDealProducts', 
            'newArrivalProducts', 
            'featuredProducts',
            'recentlyAddedProducts'
        ));
    }

    public function products(Request $request, FlexSearch $flexSearch)
    {
        $query = Product::with(['primaryImage', 'images', 'category', 'brand', 'variants']);

        // Handle sidebar/navbar filters
        $filters = [];

        // Category filter (refined for accuracy)
        if ($request->filled('category')) {
            $categoryVal = $request->input('category');

            if (is_array($categoryVal)) {
                // From sidebar: array of parent category IDs
                $filters['category_id'] = $categoryVal;
            } else {
                // From navbar: single ID (could be parent or sub)
                $category = \App\Models\Category::find($categoryVal);
                if ($category) {
                    if ($category->parent_id) {
                        // It's a subcategory
                        $filters['sub_category_id'] = $category->id;
                    } else {
                        // It's a parent category
                        $filters['category_id'] = $category->id;
                    }
                }
            }
        } elseif ($request->filled('category_nav')) {
            $category = \App\Models\Category::find($request->input('category_nav'));
            if ($category) {
                if ($category->parent_id) {
                    $filters['sub_category_id'] = $category->id;
                } else {
                    $filters['category_id'] = $category->id;
                }
            }
        }

        if ($request->filled('brand')) {
            $filters['brand_id'] = $request->input('brand');
        }

        // Price Filtering (Check against Base Product Price OR Variant Price)
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                // Check Base Product Price
                $q->where(function ($base) use ($request) {
                    $base->where(function ($sub) use ($request) {
                        $sub->where('products.discount_price', '>=', $request->min_price)
                            ->orWhere(function ($reg) use ($request) {
                                $reg->whereNull('products.discount_price')
                                    ->where('products.regular_price', '>=', $request->min_price);
                            });
                    });
                })
                // OR Check Variant Prices
                    ->orWhereHas('variants', function ($v) {
                        $v->where(function ($sub) {
                            $sub->where('product_variants.discount_price', '>=', request('min_price'))
                                ->orWhere(function ($reg) {
                                    $reg->whereNull('product_variants.discount_price')
                                        ->where('product_variants.regular_price', '>=', request('min_price'));
                                });
                        });
                    });
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                // Check Base Product Price
                $q->where(function ($base) use ($request) {
                    $base->where(function ($sub) use ($request) {
                        $sub->where('products.discount_price', '<=', $request->max_price)
                            ->orWhere(function ($reg) use ($request) {
                                $reg->whereNull('products.discount_price')
                                    ->where('products.regular_price', '<=', $request->max_price);
                            });
                    });
                })
                // OR Check Variant Prices
                    ->orWhereHas('variants', function ($v) {
                        $v->where(function ($sub) {
                            $sub->where('product_variants.discount_price', '<=', request('max_price'))
                                ->orWhere(function ($reg) {
                                    $reg->whereNull('product_variants.discount_price')
                                        ->where('product_variants.regular_price', '<=', request('max_price'));
                                });
                        });
                    });
            });
        }

        // Apply Search
        $searchTerm = $request->input('search');
        $searchableColumns = ['name', 'description', 'brand.name', 'category.name'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Apply Sorting
        if ($request->filled('sort')) {
            switch ($request->input('sort')) {
                case 'newness':
                    $query->latest();
                    break;
                case 'price-low':
                    $query->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                        ->select('products.*')
                        ->groupBy('products.id')
                        ->orderByRaw('MIN(LEAST(
                            COALESCE(products.discount_price, products.regular_price, 999999),
                            COALESCE(product_variants.discount_price, product_variants.regular_price, 999999)
                        )) ASC');
                    break;
                case 'price-high':
                    $query->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                        ->select('products.*')
                        ->groupBy('products.id')
                        ->orderByRaw('MAX(GREATEST(
                            COALESCE(products.discount_price, products.regular_price, 0),
                            COALESCE(product_variants.discount_price, product_variants.regular_price, 0)
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
            }
        } else {
            $query->latest();
        }
        $products = $query->paginate(12)->withQueryString();

        return view('client.products', [
            'products' => $products,
            'title' => 'Shop Products',
            'section' => 'Products',
        ]);
    }

    public function productDetails(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['primaryImage', 'images', 'category', 'brand', 'variants'])
            ->firstOrFail();

        // Related Products: Matches same category OR same subcategory
        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id);
                if ($product->sub_category_id) {
                    $q->orWhere('sub_category_id', $product->sub_category_id);
                }
            })
            ->with(['primaryImage', 'variants', 'brand'])
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return view('client.product_details', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'title' => $product->name,
            'section' => 'Product Details',
        ]);
    }
}
