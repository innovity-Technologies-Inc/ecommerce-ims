<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, TracksAdminActivity;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'address',
    ];

    public function supplierRmas(): HasMany
    {
        return $this->hasMany(SupplierRma::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the average performance score for the supplier.
     */
    public function getAveragePerformanceScoreAttribute(): float
    {
        return round($this->purchaseOrders()->whereNotNull('performance_score')->avg('performance_score') ?? 0, 2);
    }
}
