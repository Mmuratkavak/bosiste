<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeatherForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function storeForecasts(Request $request)
    {
        // 1. Token Kontrolü (URL öncelikli)
        $token = $request->query('token') ?? $request->header('X-Gestas-Key') ?? $request->header('X-Gestas-Token');
        $validToken = '38ofL4uafubC90OcG2UZFqhBOGB_7vSHyCEMHK3DqcNi82tSE';

        if ($token !== $validToken) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // 2. Veriyi Al
        $items = $request->input('data.items');
        if (!$items || !is_array($items)) {
             $items = $request->input('items'); 
        }

        if (!$items || !is_array($items)) {
            return response()->json(['status' => 'error', 'message' => 'Veri formatı hatalı.'], 400);
        }

        // 3. Veritabanına Eşle ve Kaydet
        try {
            $count = 0;
            foreach ($items as $item) {
                // Eşleşme Mantığı: n8n (Türkçe) -> DB (İngilizce)
                WeatherForecast::updateOrCreate(
                    [
                        // Bu üçü aynıysa güncelle, yoksa yeni oluştur
                        'location'       => $item['lokasyon'], 
                        'formatted_date' => $item['tarih_iso'], // "2026-02-04"
                        'time_label'     => $item['saat_24']    // "14:00"
                    ],
                    [
                        // Diğer sütunları güncelle
                        'forecast_time'  => $item['tarih_iso'] . ' ' . $item['saat_24'] . ':00', // Datetime formatı
                        'temperature_c'  => $item['sicaklik'],
                        'wind_speed_kmh' => $item['ruzgar_hizi_kmh'],
                        'wind_direction' => $item['ruzgar_yonu'] ?? null,
                        'rain_mm'        => $item['yagis_mm'],
                        'humidity'       => $item['nem_yuzde'],
                        'pressure_hpa'   => (in_array($item['basinc_hpa'], ['Veri Yok', null])) ? null : $item['basinc_hpa'],
                        'model'          => $item['model'] ?? 'open-meteo',
                        // 'lat' ve 'lon' veritabanında olmadığı için sildik.
                    ]
                );
                $count++;
            }
            return response()->json(['status' => 'success', 'message' => 'OK', 'count' => $count], 200);

        } catch (\Exception $e) {
            Log::error("Veritabanı Kayıt Hatası: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
