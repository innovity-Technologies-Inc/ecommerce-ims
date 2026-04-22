<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'parent_id',
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
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get active subcategories.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->where('status', true);
    }

    /**
     * Get parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }
}
