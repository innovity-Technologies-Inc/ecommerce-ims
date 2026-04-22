<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'name',
        'price',
        'short_description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
}
