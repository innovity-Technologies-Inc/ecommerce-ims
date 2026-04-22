<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseStockLimit extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'warehouse_id',
        'min_stock',
        'last_alert_sent',
    ];

    protected function casts(): array
    {
        return [
            'min_stock' => 'integer',
            'last_alert_sent' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
