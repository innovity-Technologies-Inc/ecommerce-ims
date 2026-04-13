<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicySetting extends Model
{
    protected $fillable = [
        'privacy_policy',
        'return_policy',
    ];
}
