<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyPharmacy extends Model
{
    protected $table = 'duty_pharmacies';
    
    protected $fillable = [
        'pharmacy_name', 'phone', 'address', 'district', 
        'duty_date', 'start_time', 'end_time'
    ];

    protected $casts = [
        'duty_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
