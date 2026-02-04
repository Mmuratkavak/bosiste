<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FerrySchedule extends Model
{
    protected $fillable = [
        'route',
        'date',
        'departure_time',
        'status',
        'vessel_name',
        'note',
        'raw_text',
        'source_updated_at',
    ];

    protected $casts = [
        'date' => 'date',
        'source_updated_at' => 'datetime',
    ];
}
