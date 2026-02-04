<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GestasSyncController;
use App\Http\Controllers\WeatherSyncController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// n8n'den gelen Kabatepe - Gökçeada sefer verilerini kaydetmek için endpoint
Route::post('/gestas/schedules', [GestasSyncController::class, 'syncSchedules']);

// n8n'den gelen hava durumu verilerini kaydetmek için endpoint
Route::post('/weather/forecasts', [WeatherSyncController::class, 'sync']);
