<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'brand_id',
        'name',
        'slug',
        'short_description',
        'regular_price',
        'discount_price',
        'discount_percentage',
        'description',
        'is_new_arrival',
        'is_hot_deal',
        'is_featured',
        'sales_count',
        'status',
        'is_flash_sale',
        'flash_discount_price',
        'flash_discount_percentage',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'discount_percentage' => 'integer',
            'flash_discount_price' => 'decimal:2',
            'flash_discount_percentage' => 'integer',
            'is_new_arrival' => 'boolean',
            'is_hot_deal' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'boolean',
            'sales_count' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id', 'id');
    }

    public function flashSaleItems()
    {
        return $this->hasMany(FlashSaleItem::class, 'product_id', 'id');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
