<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'code',
        'apply_for',
        'min_spend',
        'discount_type',
        'discount_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'active_on',
        'expired_on',
        'status',
    ];

    /**
     * Get the usages for this coupon.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_spend' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'active_on' => 'date',
            'expired_on' => 'date',
            'status' => 'boolean',
        ];
    }

    /**
     * Check if the coupon is currently valid.
     */
    public function isValid(?int $userId = null): bool
    {
        if (! $this->status) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->active_on->toDateString() > $today) {
            return false;
        }

        if ($this->expired_on->toDateString() < $today) {
            return false;
        }

        // Global usage limit
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
