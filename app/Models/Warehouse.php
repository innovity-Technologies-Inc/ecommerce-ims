<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'name',
        'location',
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
