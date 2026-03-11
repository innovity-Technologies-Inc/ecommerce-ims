<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_email',
        'phone_number',
        'address',
    ];
}
