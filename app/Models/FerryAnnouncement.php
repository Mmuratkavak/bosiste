<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FerryAnnouncement extends Model
{
    protected $fillable = [
        'route',
        'title',
        'body',
        'is_active',
        'published_at',
        'source_updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'source_updated_at' => 'datetime',
    ];
}
