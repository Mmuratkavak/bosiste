<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantWorkingHour extends Model
{
    use HasFactory;

    protected $table = 'tenant_working_hours';

    protected $fillable = [
        'tenant_id',
        'season_start_date',
        'season_end_date',
        'monday_open', 'monday_close', 'monday_closed',
        'tuesday_open', 'tuesday_close', 'tuesday_closed',
        'wednesday_open', 'wednesday_close', 'wednesday_closed',
        'thursday_open', 'thursday_close', 'thursday_closed',
        'friday_open', 'friday_close', 'friday_closed',
        'saturday_open', 'saturday_close', 'saturday_closed',
        'sunday_open', 'sunday_close', 'sunday_closed',
        'special_days',
    ];

    protected $casts = [
        'season_start_date' => 'date',
        'season_end_date' => 'date',
        'monday_closed' => 'bool',
        'tuesday_closed' => 'bool',
        'wednesday_closed' => 'bool',
        'thursday_closed' => 'bool',
        'friday_closed' => 'bool',
        'saturday_closed' => 'bool',
        'sunday_closed' => 'bool',
        'special_days' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
