<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IdentifyTenantByHost;
use App\Models\Tenant;
use App\Models\FerrySchedule;
use App\Models\WeatherForecast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::middleware([IdentifyTenantByHost::class])->group(function () {
    Route::get('/', function () {
        $nextFerry = FerrySchedule::where('route', 'Kabatepe - Gökçeada')
            ->whereDate('date', now()->toDateString())
            ->orderBy('date')
            ->orderBy('departure_time')
            ->first();

        $currentWeather = WeatherForecast::where('location', 'Gökçeada Merkez')
            ->where('forecast_time', '>=', now())
            ->orderBy('forecast_time')
            ->first();

        return view('welcome', [
            'nextFerry' => $nextFerry,
            'currentWeather' => $currentWeather,
        ]);
    });

    // Basit arama endpoint'i (tenant bağlamında çalışır)
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');

    // Hava durumu sayfası
    Route::get('/hava-durumu', function () {
        $forecasts = WeatherForecast::where('model', 'open-meteo')
            ->where('forecast_time', '>=', now())
            ->orderBy('location')
            ->orderBy('forecast_time')
            ->get()
            ->groupBy('location');

        // Özel lokasyon sıralaması: merkez, köyler, limanlar, plajlar/koylar (kullanıcının istediği sıra)
        $priorityOrder = [
            // Merkez
            'Gökçeada Merkez' => 10,

            // Köyler
            'Zeytinliköy' => 20,
            'Tepeköy' => 21,
            'Dereköy' => 22,
            'Eski Bademli' => 23,
            'Yeni Bademli' => 24,
            'Kaleköy (Liman)' => 25,
            'Yukarı Kaleköy' => 26,
            'Uğurlu Köyü' => 27,
            'Eşelek' => 28,
            'Şirin Köy' => 29,

            // Limanlar
            'Kaleköy Limanı' => 40,
            'Kuzulimanı (Feribot)' => 41,

            // Plajlar ve koylar
            'Yıldız Koy' => 60,
            'Mavi Koy' => 61,
            'Marmaros' => 62,
            'Aydıncık (Kefaloz) Plajı' => 63,
            'Kapıkaya Plajı' => 64,
            'Laz Koyu' => 65,
            'Uğurlu Plajı' => 66,
            'Gizli Liman' => 67,
        ];

        $sorted = $forecasts->sortBy(function ($items, $location) use ($priorityOrder) {
            $base = $priorityOrder[$location] ?? 1000;
            return $base . ' ' . $location;
        });

        // Harita için lokasyon + koordinat listesi (varsa raw_payload içindeki lat/lon'dan okunur)
        $mapLocations = $sorted->map(function ($items, $location) {
            /** @var \App\Models\WeatherForecast|null $first */
            $first = $items->first();

            $lat = is_array($first?->raw_payload) ? ($first->raw_payload['lat'] ?? null) : null;
            $lon = is_array($first?->raw_payload) ? ($first->raw_payload['lon'] ?? null) : null;

            return [
                'name' => $location,
                'lat' => $lat,
                'lon' => $lon,
            ];
        })->values();

        return view('weather', [
            'forecastsByLocation' => $sorted,
            'mapLocations' => $mapLocations,
        ]);
    })->name('weather.index');

    // Seçili lokasyon için detaylı hava durumu listesi
    Route::get('/hava-durumu/detay', function (Request $request) {
        $location = $request->query('location');

        if (! $location) {
            abort(404);
        }

        $forecasts = WeatherForecast::where('location', $location)
            ->where('model', 'open-meteo')
            ->where('forecast_time', '>=', now())
            ->orderBy('forecast_time')
            ->get();

        return view('weather-detail', [
            'location' => $location,
            'forecasts' => $forecasts,
        ]);
    })->name('weather.detail');

    // Public tenant pages will be added here and will have tenant resolved by host
});

// Impersonation route for super_admins to login as tenant owner (convenience for local/admin use)
Route::get('/admin/tenants/{tenant}/impersonate', function (Tenant $tenant) {
    $user = Auth::user();
    if (! $user || ! $user->hasRole('super_admin')) {
        abort(403, 'Sadece süper adminler bu işlemi yapabilir.');
    }

    // store impersonator id so we can return later
    session(['impersonator_id' => $user->id]);

    if (! $tenant->owner_id) {
        return redirect()->back()->with('error', 'Bu işletmenin sahibi atanmadı.');
    }

    Auth::loginUsingId($tenant->owner_id);

    return redirect('/admin');
})->name('admin.tenants.impersonate');

// End impersonation and return to original super_admin
Route::get('/admin/impersonation/leave', function () {
    if (! Auth::check()) {
        return redirect('/admin');
    }
    
    $impersonatorId = session()->pull('impersonator_id');
    if (! $impersonatorId) {
        return redirect('/admin');
    }

    Auth::loginUsingId($impersonatorId);
    return redirect('/admin');
})->name('admin.impersonation.leave');
