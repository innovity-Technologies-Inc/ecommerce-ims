<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SectionSetting extends Model
{
    protected $fillable = [
        'section_name',
        'section_title',
        'section_subtitle',
        'background_image',
        'mode',
        'limit',
        'is_visible',
    ];

    /**
     * Get the products for the section (if in custom mode).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'section_product')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Scope a query to only include visible sections.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
