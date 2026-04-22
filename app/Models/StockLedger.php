<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLedger extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'warehouse_id',
        'batch_id',
        'batch_serial_id',
        'supplier_id',
        'change_qty',
        'transaction_type',
        'reason_code',
        'reference_id',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(BatchSerial::class, 'batch_serial_id');
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
