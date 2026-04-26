<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'business_name',
        'dark_logo',
        'light_logo',
        'breadcrumb_image',
        'login_banner',
        'register_banner',
        'meta_title',
        'meta_description',
        'favicon',
        'currency',
        'timezone',
        'notify_email',
        'primary_color',
        'secondary_color',
    ];
}
