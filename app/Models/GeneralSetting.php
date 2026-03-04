<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'business_name',
        'dark_logo',
        'light_logo',
        'breadcrumb_image',
        'meta_title',
        'meta_description',
        'favicon',
        'currency',
    ];
}
