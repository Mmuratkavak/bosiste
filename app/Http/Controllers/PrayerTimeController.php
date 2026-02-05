<?php
namespace App\Http\Controllers;
use App\Models\PrayerTime;
use Illuminate\Http\Request;

class PrayerTimeController extends Controller {
    public function index() {
        $prayer = PrayerTime::where('date', now()->format('Y-m-d'))->first();
        if (!$prayer) return "Bugün için namaz vakti verisi henüz yüklenmedi.";
        
        $now = now()->format('H:i');
        $currentVakit = 'yatsı';
        if ($now >= $prayer->imsak && $now < $prayer->gunes) $currentVakit = 'imsak';
        elseif ($now >= $prayer->gunes && $now < $prayer->ogle) $currentVakit = 'gunes';
        elseif ($now >= $prayer->ogle && $now < $prayer->ikindi) $currentVakit = 'ogle';
        elseif ($now >= $prayer->ikindi && $now < $prayer->aksam) $currentVakit = 'ikindi';
        elseif ($now >= $prayer->aksam && $now < $prayer->yatsı) $currentVakit = 'aksam';

        return view('prayer-times', compact('prayer', 'currentVakit'));
    }
}
