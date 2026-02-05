<?php
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IdentifyTenantByHost;
use App\Models\FerrySchedule;
use App\Models\WeatherSnapshot;
use App\Http\Controllers\Api\FerryController;
use App\Http\Controllers\Api\PharmacyController;
use App\Http\Controllers\PrayerTimeController;

Route::middleware([IdentifyTenantByHost::class])->group(function () {
    Route::get('/', function () {
        $nextFerry = FerrySchedule::where('route', 'KABATEPE-GOKCEADA')->orderBy('departure_time')->first();
        $snapshot = WeatherSnapshot::latest()->first();
        $currentWeather = $snapshot ? ($snapshot->payload['guncel_durum'] ?? null) : null;
        return view('welcome', compact('nextFerry', 'currentWeather'));
    });

    Route::get('/hava-durumu', function () {
        $snapshot = WeatherSnapshot::latest()->first();
        $data = $snapshot ? $snapshot->payload : null;
        return view('weather', ['data' => $data]);
    })->name('weather.index');

    Route::get('/nobetci-eczaneler', [PharmacyController::class, 'index'])->name('pharmacy.index');
    Route::get('/feribot-saatleri', [FerryController::class, 'index'])->name('ferry.index_new');
    Route::get('/namaz-vakitleri', [PrayerTimeController::class, 'index'])->name('prayer.index');
});
