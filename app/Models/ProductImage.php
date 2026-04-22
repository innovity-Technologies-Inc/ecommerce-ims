<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
