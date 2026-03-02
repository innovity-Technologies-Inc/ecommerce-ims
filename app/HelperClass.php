<?php

namespace App;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Client;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\IntroVideo;
use App\Models\KeyDetails;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Section;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\General;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HelperClass
{

    public static function generalSettings()
    {
        return GeneralSetting::first();
    }


    public static function file_upload($file, $folder_name)
    {
        $file_name = time() . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file_path = $file->storeAs('upload/' . $folder_name, $file_name, 'public');
        return $file_path;
    }

    public static function file_delete($file_path)
    {
        Storage::disk('public')->delete($file_path);
    }


}
