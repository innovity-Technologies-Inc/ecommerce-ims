<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'brand_id',
        'name',
        'short_description',
        'slug',
        'regular_price',
        'discount_price',
        'discount_percentage',
        'flash_discount_price',
        'flash_discount_percentage',
        'description',
        'is_new_arrival',
        'is_hot_deal',
        'is_featured',
        'is_top_pick',
        'status',
        'is_flash_sale',
        'stock',
        'min_stock_global',
        'min_stock_type',
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
            'is_top_pick' => 'boolean',
            'status' => 'boolean',
            'is_flash_sale' => 'boolean',
            'sales_count' => 'integer',
            'stock' => 'integer',
            'min_stock_global' => 'integer',
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

    public function inventoryLevels()
    {
        return $this->hasMany(InventoryLevel::class);
    }

    public function warehouseStockLimits()
    {
        return $this->hasMany(WarehouseStockLimit::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
