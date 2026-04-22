<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'company_name',
        'company_email',
        'phone_number',
        'address',
        'map_link',
        'facebook_url',
        'facebook_status',
        'instagram_url',
        'instagram_status',
        'tiktok_url',
        'tiktok_status',
        'x_url',
        'x_status',
        'thread_url',
        'thread_status',
        'linkedin_url',
        'linkedin_status',
        'whatsapp_url',
        'whatsapp_status',
        'youtube_url',
        'youtube_status',
    ];

    protected $casts = [
        'facebook_status' => 'boolean',
        'instagram_status' => 'boolean',
        'tiktok_status' => 'boolean',
        'x_status' => 'boolean',
        'thread_status' => 'boolean',
        'linkedin_status' => 'boolean',
        'whatsapp_status' => 'boolean',
        'youtube_status' => 'boolean',
    ];
}
