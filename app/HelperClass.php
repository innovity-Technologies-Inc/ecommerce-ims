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
        return GeneralSetting::first() ?? new GeneralSetting;
    }

    public static function contactSettings()
    {
        return ContactSetting::first() ?? new ContactSetting;
    }

    public static function getCategories()
    {
        return Category::whereNull('parent_id')->active()->with('subcategories')->get();
    }

    public static function getBrands()
    {
        return Brand::active()->get();
    }

    public static function getBanner(string $slug)
    {
        return \App\Models\Banner::where('slug', $slug)->first();
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

    public static function file_upload_from_url($url, $folder_name)
    {
        try {
            $contents = file_get_contents($url);
            if (! $contents) {
                return null;
            }

            $file_name = time().Str::random(10).'.jpg';
            $file_path = 'upload/'.$folder_name.'/'.$file_name;

            Storage::disk('public')->put($file_path, $contents);

            return $file_path;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error uploading file from URL: '.$e->getMessage());

            return null;
        }
    }

    public static function file_delete($file_path)
    {
        Storage::disk('public')->delete($file_path);
    }

    public static function getProductTotalStock($product)
    {
        if ($product->variants->count() > 0) {
            return $product->variants->sum('stock') ?? 0;
        }

        return $product->stock ?? 0;
    }

    public static function getProductPriceRange($product)
    {
        $prices = collect();

        if ($product->variants->count() > 0) {
            foreach ($product->variants as $variant) {
                // 1. Determine Regular Price for this variant
                $regPrice = ($variant->regular_price > 0) ? $variant->regular_price : $product->regular_price;

                // 2. Determine Selling Price for this variant
                $price = $regPrice;

                if ($product->is_flash_sale) {
                    if ($variant->flash_discount_price > 0) {
                        $price = $variant->flash_discount_price;
                    } elseif ($product->flash_discount_price > 0) {
                        $price = $product->flash_discount_price;
                    } elseif ($variant->discount_price > 0) {
                        $price = $variant->discount_price;
                    } elseif ($product->discount_price > 0) {
                        $price = $product->discount_price;
                    }
                } else {
                    if ($variant->discount_price > 0) {
                        $price = $variant->discount_price;
                    } elseif ($product->discount_price > 0) {
                        $price = $product->discount_price;
                    }
                }

                if ($price > 0) {
                    $prices->push((float) $price);
                }
            }
        } else {
            // Check base product price
            $regPrice = $product->regular_price;
            $price = $regPrice;

            if ($product->is_flash_sale) {
                if ($product->flash_discount_price > 0) {
                    $price = $product->flash_discount_price;
                } elseif ($product->discount_price > 0) {
                    $price = $product->discount_price;
                }
            } else {
                if ($product->discount_price > 0) {
                    $price = $product->discount_price;
                }
            }

            if ($price > 0) {
                $prices->push((float) $price);
            }
        }

        $prices = $prices->unique();

        if ($product->is_flash_sale) {
            $maxDiscount = $product->variants->count() > 0
                ? $product->variants->max('flash_discount_percentage')
                : $product->flash_discount_percentage;
            if (! $maxDiscount && $product->flash_discount_percentage > 0) {
                $maxDiscount = $product->flash_discount_percentage;
            }
        } else {
            $maxDiscount = $product->variants->count() > 0
                ? $product->variants->max('discount_percentage')
                : $product->discount_percentage;
            if (! $maxDiscount && $product->discount_percentage > 0) {
                $maxDiscount = $product->discount_percentage;
            }
        }

        // Determine Min Regular Price
        if ($product->variants->count() > 0) {
            $minReg = $product->variants->where('regular_price', '>', 0)->min('regular_price') ?? $product->regular_price;
        } else {
            $minReg = $product->regular_price;
        }

        return [
            'min' => $prices->min() ?? 0,
            'max' => $prices->max() ?? 0,
            'has_range' => $prices->min() != $prices->max(),
            'has_discount' => $maxDiscount > 0,
            'max_discount_percentage' => $maxDiscount ?? 0,
            'min_regular_price' => $minReg,
        ];
    }
}
