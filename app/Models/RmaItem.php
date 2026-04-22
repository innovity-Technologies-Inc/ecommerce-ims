<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RmaItem extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'supplier_rma_id',
        'batch_id',
        'product_id',
        'product_variant_id',
        'batch_serial_id',
        'quantity',
    ];

    public function supplierRma(): BelongsTo
    {
        return $this->belongsTo(SupplierRma::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(BatchSerial::class, 'batch_serial_id');
    }
}
