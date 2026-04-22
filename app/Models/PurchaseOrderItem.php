<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_variant_id',
        'order_quantity',
        'received_quantity',
        'unit_cost',
    ];

    protected function casts(): array
    {
        return [
            'order_quantity' => 'integer',
            'received_quantity' => 'integer',
            'unit_cost' => 'decimal:2',
        ];
    }

    /**
     * Get the dynamic subtotal for the item.
     */
    public function getSubtotalAttribute(): float
    {
        if ($this->purchaseOrder && $this->purchaseOrder->status === 'Delivered') {
            return $this->received_quantity * $this->unit_cost;
        }

        return $this->order_quantity * $this->unit_cost;
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
