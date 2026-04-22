<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wastage extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'warehouse_id',
        'batch_id',
        'quantity',
        'reason',
        'return_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}
