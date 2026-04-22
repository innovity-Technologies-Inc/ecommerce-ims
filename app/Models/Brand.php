<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }
}
