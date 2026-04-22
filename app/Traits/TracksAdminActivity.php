<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait TracksAdminActivity
{
    /**
     * Boot the trait and hook into model events.
     */
    public static function bootTracksAdminActivity(): void
    {
        // Automatically set created_by and updated_by when creating
        static::creating(function ($model) {
            if (Auth::guard('admin')->check()) {
                if (! $model->isDirty('created_by')) {
                    $model->created_by = Auth::guard('admin')->id();
                }
                if (! $model->isDirty('updated_by')) {
                    $model->updated_by = Auth::guard('admin')->id();
                }
            }
        });

        // Automatically set updated_by when updating
        static::updating(function ($model) {
            if (Auth::guard('admin')->check()) {
                if (! $model->isDirty('updated_by')) {
                    $model->updated_by = Auth::guard('admin')->id();
                }
            }
        });
    }

    /**
     * Get the admin who created the record.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    /**
     * Get the admin who last updated the record.
     */
    public function editor()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }
}
