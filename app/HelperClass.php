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

    /**
     * Convert number to words (Dynamic Currency).
     */
    public static function numberToWords($number, $currencyName = 'Bangladeshi Taka'): string
    {
        if (class_exists('\NumberFormatter')) {
            $f = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $words = $f->format($number);
        } else {
            // Fallback pure PHP implementation if intl extension is missing
            $words = self::convertNumberToWordsPurePhp($number);
        }

        return ucfirst(str_replace('-', ' ', $words)).' '.($currencyName ?? 'Bangladeshi Taka').' Only.';
    }

    /**
     * Pure PHP implementation for number to words fallback.
     */
    private static function convertNumberToWordsPurePhp($number): string
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion',
        ];

        if (! is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convertNumberToWordsPurePhp only accepts numbers between -'.PHP_INT_MAX.' and '.PHP_INT_MAX,
                E_USER_WARNING
            );

            return false;
        }

        if ($number < 0) {
            return $negative.self::convertNumberToWordsPurePhp(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[(int) $hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= $conjunction.self::convertNumberToWordsPurePhp($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convertNumberToWordsPurePhp($numBaseUnits).' '.$dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convertNumberToWordsPurePhp($remainder);
                }
                break;
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}
