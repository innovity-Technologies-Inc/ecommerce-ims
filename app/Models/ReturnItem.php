<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'return_id',
        'product_id',
        'product_variant_id',
        'batch_id',
        'batch_serial_id',
        'quantity',
        'unit_price',
        'total_price',
        'condition',
        'is_received',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'is_received' => 'boolean',
        ];
    }

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(BatchSerial::class, 'batch_serial_id');
    }
}
