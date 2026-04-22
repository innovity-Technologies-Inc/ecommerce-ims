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
            // Check if the current request is an admin request and admin is logged in
            if (request()->is('admin/*') || request()->is('admin')) {
                if (Auth::guard('admin')->check()) {
                    $adminId = Auth::guard('admin')->id();
                    if ($adminId && ! $model->isDirty('created_by')) {
                        $model->created_by = $adminId;
                    }
                    if ($adminId && ! $model->isDirty('updated_by')) {
                        $model->updated_by = $adminId;
                    }
                }
            }
        });

        // Automatically set updated_by when updating
        static::updating(function ($model) {
            // Check if the current request is an admin request and admin is logged in
            if (request()->is('admin/*') || request()->is('admin')) {
                if (Auth::guard('admin')->check()) {
                    $adminId = Auth::guard('admin')->id();
                    if ($adminId && ! $model->isDirty('updated_by')) {
                        $model->updated_by = $adminId;
                    }
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
