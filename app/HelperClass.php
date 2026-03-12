<?php

namespace App;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactSetting;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HelperClass
{
    public static function generalSettings()
    {
        return GeneralSetting::first();
    }

    public static function contactSettings()
    {
        return ContactSetting::first();
    }

    public static function getCategories()
    {
        return Category::whereNull('parent_id')->with('subcategories')->get();
    }

    public static function getBrands()
    {
        return Brand::active()->get();
    }

    public static function wishlistCount()
    {
        if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
            return \App\Models\Wishlist::where('user_id', \Illuminate\Support\Facades\Auth::guard('web')->id())->count();
        }

        return 0;
    }

    public static function cartCount()
    {
        return app(\App\Services\CartService::class)->getCartCount();
    }

    public static function getCartItems()
    {
        return app(\App\Services\CartService::class)->getCartItems();
    }

    public static function indexNumberSerialization($data)
    {
        $sl = ($data->currentPage() - 1) * $data->perPage() + 1;

        return $sl;
    }

    public static function file_upload($file, $folder_name)
    {
        $file_name = time().Str::random(10).'.'.$file->getClientOriginalExtension();
        $file_path = $file->storeAs('upload/'.$folder_name, $file_name, 'public');

        return $file_path;
    }

    public static function file_delete($file_path)
    {
        Storage::disk('public')->delete($file_path);
    }

    public static function getProductPriceRange($product)
    {
        $prices = collect();

        if ($product->variants->count() > 0) {
            foreach ($product->variants as $variant) {
                // If variant doesn't have its own price, it should fallback to product's base price.
                $price = $variant->discount_price ?? $variant->regular_price ?? $product->discount_price ?? $product->regular_price;
                if ($price) {
                    $prices->push((float) $price);
                }
            }
        }

        // Always include product's base prices as well, if variants don't fully override them.
        $basePrice = $product->discount_price ?? $product->regular_price;
        if ($basePrice) {
            $prices->push((float) $basePrice);
        }

        $prices = $prices->unique();

        $maxDiscount = $product->variants->count() > 0
            ? $product->variants->max('discount_percentage')
            : $product->discount_percentage;

        return [
            'min' => $prices->min() ?? 0,
            'max' => $prices->max() ?? 0,
            'has_range' => $prices->min() != $prices->max(),
            'has_discount' => $maxDiscount > 0,
            'max_discount_percentage' => $maxDiscount ?? 0,
            'min_regular_price' => $product->variants->count() > 0
                ? ($product->variants->min('regular_price') ?? $product->regular_price)
                : $product->regular_price,
        ];
    }
}
