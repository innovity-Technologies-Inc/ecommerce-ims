<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'is_quarantine',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function inventoryLevels()
    {
        return $this->hasMany(InventoryLevel::class);
    }
}
