<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
