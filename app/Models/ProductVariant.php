<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_name',
        'size',
        'color',
        'sku',
        'regular_price',
        'discount_price',
        'flash_discount_price',
        'discount_percentage',
        'flash_discount_percentage',
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
            'stock' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function inventoryLevels()
    {
        return $this->hasMany(InventoryLevel::class);
    }

    public function warehouseStockLimits()
    {
        return $this->hasMany(WarehouseStockLimit::class);
    }
}
