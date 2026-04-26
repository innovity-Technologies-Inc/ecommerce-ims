<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayslipGeneration extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'total_employees',
        'total_amount',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the payslips for this generation.
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
