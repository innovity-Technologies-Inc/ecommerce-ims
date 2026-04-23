<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'is_time_tracking',
        'is_clocked_in',
        'salary_type',
        'salary_amount',
        'daily_work_hours',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_time_tracking' => 'boolean',
            'is_clocked_in' => 'boolean',
            'salary_amount' => 'decimal:2',
            'daily_work_hours' => 'decimal:2',
        ];
    }

    /**
     * Get the attendances for the admin.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(AdminAttendance::class);
    }

    /**
     * Get the payslips for the admin.
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
