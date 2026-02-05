<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FerryController;
use App\Http\Controllers\Api\WeatherApiController;

Route::post('/hava-durumu-guncelle', [WeatherApiController::class, 'store']);
Route::post('/sefer-guncelle', [FerryController::class, 'update']);
