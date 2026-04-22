<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $table = 'returns';

    protected $fillable = [
        'order_id',
        'return_id',
        'user_id',
        'reason',
        'status',
        'rejection_reason',
        'image',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function returnImages(): HasMany
    {
        return $this->hasMany(ReturnImage::class, 'return_id');
    }
}
