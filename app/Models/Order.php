<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_no',
        'invoice_date',
        'user_id',
        'name',
        'email',
        'mobile',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'subtotal',
        'shipping_charge',
        'shipping_method_id',
        'shipping_method_name',
        'coupon_id',
        'discount',
        'total_amount',
        'total_cost',
        'payment_method',
        'payment_status',
        'order_status',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'datetime',
            'subtotal' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderedProductBatches(): HasMany
    {
        return $this->hasMany(OrderedProductBatch::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('changed_at', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
