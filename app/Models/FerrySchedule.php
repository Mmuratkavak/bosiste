<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FerrySchedule extends Model
{
    use HasFactory;
    // KILIDI ACIYORUZ: Bu sutunlarin kaydedilmesine izin ver
    protected $fillable = [
        'departure_time', 
        'arrival_time', 
        'route'
    ];
}
