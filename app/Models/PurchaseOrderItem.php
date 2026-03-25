<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_variant_id',
        'order_quantity',
        'received_quantity',
        'damaged_quantity',
        'missing_quantity',
        'serial_numbers',
        'unit_cost',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'order_quantity' => 'integer',
            'received_quantity' => 'integer',
            'damaged_quantity' => 'integer',
            'missing_quantity' => 'integer',
            'serial_numbers' => 'json',
            'unit_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
