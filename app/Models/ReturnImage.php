<?php

namespace App\Models;

use App\Traits\TracksAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnImage extends Model
{
    use TracksAdminActivity;

    protected $fillable = [
        'return_id',
        'image_path',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}
