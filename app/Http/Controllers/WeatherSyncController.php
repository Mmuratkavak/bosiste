<?php

namespace App\Http\Controllers;

use App\Models\WeatherForecast;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherSyncController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        // Shared secret kontrolü (n8n tarafında header olarak gönderilecek)
        $token = $request->header('X-Gestas-Token');
        $expected = config('services.weather.token') ?? config('services.gestas.token');

        if (! $expected || $token !== $expected) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        if (isset($payload['items']) && is_array($payload['items'])) {
            // Beklenen standart biçim: { "items": [ ... ] }
            $items = $payload['items'];
        } elseif (isset($payload['data']['items']) && is_array($payload['data']['items'])) {
            // n8n gibi araçlar tek bir data nesnesi içinde items gönderebilir: { "data": { "items": [ ... ] } }
            $items = $payload['data']['items'];
        } elseif (is_array($payload) && isset($payload[0]) && is_array($payload[0])) {
            // items doğrudan kök dizide gönderilmişse onu kullan
            $items = $payload;
        } else {
            return response()->json([
                'message' => 'Invalid payload',
            ], 422);
        }

        \Log::info('weather_sync_request_sample', [
            'items_count' => is_countable($items) ? count($items) : null,
            'sample_items' => collect($items)->take(3),
        ]);

        $created = 0;

        foreach ($items as $item) {
            $location = $item['lokasyon'] ?? null;
            $model = $item['model'] ?? 'open-meteo';

            if (! $location) {
                continue;
            }

            $forecastTime = null;
            if (! empty($item['timestamp'])) {
                try {
                    // n8n tarafında timestamp milisaniye (Date.getTime) olarak geliyorsa onu saniyeye çevir
                    if (is_numeric($item['timestamp'])) {
                        $seconds = (int) floor(((int) $item['timestamp']) / 1000);
                        $forecastTime = Carbon::createFromTimestamp($seconds);
                    } else {
                        // ISO string vb. geliyorsa klasik parse
                        $forecastTime = Carbon::parse($item['timestamp']);
                    }
                } catch (\Throwable) {
                    $forecastTime = null;
                }
            }

            $temperature = isset($item['sicaklik']) && $item['sicaklik'] !== ''
                ? (float) $item['sicaklik']
                : null;

            $rain = isset($item['yagis_mm']) && $item['yagis_mm'] !== ''
                ? (float) $item['yagis_mm']
                : null;

            $windSpeed = isset($item['ruzgar_hizi_kmh']) && $item['ruzgar_hizi_kmh'] !== ''
                ? (float) $item['ruzgar_hizi_kmh']
                : null;

            $humidity = isset($item['nem_yuzde']) && $item['nem_yuzde'] !== ''
                ? (int) $item['nem_yuzde']
                : null;

            $pressure = isset($item['basinc_hpa']) && $item['basinc_hpa'] !== ''
                ? (int) $item['basinc_hpa']
                : null;

            try {
                WeatherForecast::updateOrCreate(
                    [
                        'location' => $location,
                        'model' => $model,
                        'forecast_time' => $forecastTime,
                    ],
                    [
                        'forecast_time' => $forecastTime,
                        'formatted_date' => $item['tarih_formatli'] ?? null,
                        'time_label' => $item['saat'] ?? null,
                        'temperature_c' => $temperature,
                        'wind_speed_kmh' => $windSpeed,
                        'wind_direction' => $item['ruzgar_yonu'] ?? null,
                        'rain_mm' => $rain,
                        'humidity' => $humidity,
                        'pressure_hpa' => $pressure,
                        'raw_payload' => $item,
                    ],
                );

                $created++;
            } catch (QueryException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }

                WeatherForecast::where('location', $location)
                    ->where('model', $model)
                    ->update([
                        'forecast_time' => $forecastTime,
                        'formatted_date' => $item['tarih_formatli'] ?? null,
                        'time_label' => $item['saat'] ?? null,
                        'temperature_c' => $temperature,
                        'wind_speed_kmh' => $windSpeed,
                        'wind_direction' => $item['ruzgar_yonu'] ?? null,
                        'rain_mm' => $rain,
                        'humidity' => $humidity,
                        'pressure_hpa' => $pressure,
                        'raw_payload' => $item,
                    ]);
            }
        }

        return response()->json([
            'message' => 'OK',
            'count' => $created,
        ]);
    }
}
