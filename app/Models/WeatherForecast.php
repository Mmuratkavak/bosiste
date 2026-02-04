<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'forecast_time',
        'formatted_date',
        'time_label',
        'temperature_c',
        'wind_speed_kmh',
        'wind_direction',
        'rain_mm',
        'humidity',
        'pressure_hpa',
        'model',
        'raw_payload',
    ];

    protected $casts = [
        'forecast_time' => 'datetime',
        'temperature_c' => 'float',
        'wind_speed_kmh' => 'float',
        'rain_mm' => 'float',
        'humidity' => 'integer',
        'pressure_hpa' => 'integer',
        'raw_payload' => 'array',
    ];
}
