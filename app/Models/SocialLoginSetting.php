<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLoginSetting extends Model
{
    protected $fillable = [
        'google_client_id',
        'google_client_secret',
        'google_redirect_url',
        'google_status',
    ];

    protected function casts(): array
    {
        return [
            'google_status' => 'boolean',
            'google_client_secret' => 'encrypted',
        ];
    }
}
