<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
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
}
