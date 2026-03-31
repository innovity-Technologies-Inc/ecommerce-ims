<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierRma extends Model
{
    use HasFactory;

    protected $fillable = [
        'rma_number',
        'supplier_id',
        'purchase_order_id',
        'status',
        'notify_supplier',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'notify_supplier' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function rmaItems(): HasMany
    {
        return $this->hasMany(RmaItem::class);
    }
}
