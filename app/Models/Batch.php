<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'batch_number',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'total_received_qty',
        'total_saleable_qty',
        'total_damaged_qty',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function batchProducts(): HasMany
    {
        return $this->hasMany(BatchProduct::class);
    }

    public function serials(): HasMany
    {
        return $this->hasMany(BatchSerial::class);
    }

    public function inventoryLevels(): HasMany
    {
        return $this->hasMany(InventoryLevel::class);
    }
}
