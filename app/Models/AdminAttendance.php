<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAttendance extends Model
{
    protected $fillable = [
        'admin_id',
        'date',
        'clock_in',
        'clock_out',
        'total_minutes',
        'is_manual',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_manual' => 'boolean',
        ];
    }

    /**
     * Get the admin that owns the attendance.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
