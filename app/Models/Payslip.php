<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    protected $fillable = [
        'payslip_generation_id',
        'admin_id',
        'payslip_number',
        'month',
        'year',
        'salary_type',
        'salary_amount',
        'total_hours',
        'net_salary',
        'status',
        'payment_date',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'salary_amount' => 'decimal:2',
            'total_hours' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'payment_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the generation batch this payslip belongs to.
     */
    public function generation(): BelongsTo
    {
        return $this->belongsTo(PayslipGeneration::class, 'payslip_generation_id');
    }

    /**
     * Get the admin that owns the payslip.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
