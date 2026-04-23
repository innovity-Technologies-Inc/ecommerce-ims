<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'slug',
        'image',
        'link',
        'created_by',
        'updated_by',
    ];
}
