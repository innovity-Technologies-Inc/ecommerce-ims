<?php

namespace App\Http\Controllers;

use App\Models\Product;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        return view('client.homepage');
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

        if ($request->filled('size')) {
            $filters['variants.size'] = $request->input('size');
        }

        if ($request->filled('color')) {
            $filters['variants.color'] = $request->input('color');
        }

        // Price Filtering
        if ($request->filled('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
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
                    $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                        ->select('products.*')
                        ->groupBy('products.id')
                        ->orderByRaw('MIN(product_variants.price) ASC');
                    break;
                case 'price-high':
                    $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                        ->select('products.*')
                        ->groupBy('products.id')
                        ->orderByRaw('MAX(product_variants.price) DESC');
                    break;
                case 'a-z':
                    $query->orderBy('name', 'asc');
                    break;
                case 'z-a':
                    $query->orderBy('name', 'desc');
                    break;
                case 'in-stock':
                    $query->whereHas('variants', function ($q) {
                        $q->where('stock', '>', 0);
                    });
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view('client.products', compact('products'));
    }
}
