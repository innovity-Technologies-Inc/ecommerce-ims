<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'image',
        'title',
        'subtitle',
        'subtext',
        'button_name',
        'button_url',
        'is_active',
        'position',
    ];

    /**
     * Scope a query to only include active sliders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full URL of the image.
     */
    public function getImageUrlAttribute(): string
    {
        return asset($this->image);
    }
}
