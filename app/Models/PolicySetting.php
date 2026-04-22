<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class PolicySetting extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'privacy_policy',
        'return_policy',
    ];
}
