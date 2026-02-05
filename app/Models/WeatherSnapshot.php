<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WeatherSnapshot extends Model {
    protected $guarded = [];
    protected $casts = [
        'payload' => 'array' // Veritabanından çekerken otomatik Array yap
    ];
}
