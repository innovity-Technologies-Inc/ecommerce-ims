<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'name',
        'status',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'end_date' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class, 'flash_sale_id', 'id');
    }

    /**
     * Check if the flash sale is currently active and not expired.
     */
    public function isActive(): bool
    {
        if (! $this->status) {
            return false;
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        return true;
    }
}
