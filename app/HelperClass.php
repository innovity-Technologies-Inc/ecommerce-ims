<?php

namespace App;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HelperClass
{
    public static function generalSettings()
    {
        return GeneralSetting::first();
    }

    public static function getCategories()
    {
        return Category::whereNull('parent_id')->with('subcategories')->get();
    }

    public static function getBrands()
    {
        return Brand::all();
    }

    public static function wishlistCount()
    {
        if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
            return \App\Models\Wishlist::where('user_id', \Illuminate\Support\Facades\Auth::guard('web')->id())->count();
        }
        return 0;
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
}
