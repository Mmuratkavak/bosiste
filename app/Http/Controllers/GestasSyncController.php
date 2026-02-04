<?php

namespace App\Http\Controllers;

use App\Models\FerrySchedule;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GestasSyncController extends Controller
{
    public function syncSchedules(Request $request): JsonResponse
    {
        // Basit shared secret kontrolü (n8n tarafında header olarak gönderilecek)
        $token = $request->header('X-Gestas-Token');
        $expected = config('services.gestas.token');

        if (! $expected || $token !== $expected) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'route' => 'sometimes|string',
            'items' => 'required|array',
            'items.*.kabatepe_kalkis' => 'required|string',
            'items.*.gokceada_kalkis' => 'required|string',
            'items.*.status' => 'nullable|string',
            'items.*.tarife_gecerlilik' => 'nullable|string',
            'items.*.uyari_notu' => 'nullable|string',
            'items.*.kayit_tarihi' => 'nullable|string',
        ]);

        $routeName = $data['route'] ?? 'Kabatepe - Gökçeada';

        $created = 0;

        foreach ($data['items'] as $item) {
            $date = null;

            if (! empty($item['kayit_tarihi'])) {
                try {
                    $date = Carbon::parse($item['kayit_tarihi'])->toDateString();
                } catch (\Throwable) {
                    $date = now()->toDateString();
                }
            } else {
                $date = now()->toDateString();
            }

            $departure = $item['kabatepe_kalkis'];

            $noteParts = [];
            if (! empty($item['tarife_gecerlilik'])) {
                $noteParts[] = $item['tarife_gecerlilik'];
            }
            if (! empty($item['gokceada_kalkis'])) {
                $noteParts[] = 'Gökçeada kalkış: '.$item['gokceada_kalkis'];
            }
            if (! empty($item['uyari_notu'])) {
                $noteParts[] = $item['uyari_notu'];
            }

            $note = implode(' | ', $noteParts);

            try {
                FerrySchedule::updateOrCreate(
                    [
                        'route' => $routeName,
                        'date' => $date,
                        'departure_time' => $departure,
                    ],
                    [
                        'status' => $item['status'] ?? null,
                        'note' => $note ?: null,
                        'source_updated_at' => now(),
                    ],
                );

                $created++;
            } catch (QueryException $e) {
                // Aynı route+date+departure_time için UNIQUE hatası gelirse, mevcut kaydı güncelle
                if ($e->getCode() === '23000') {
                    FerrySchedule::where('route', $routeName)
                        ->whereDate('date', $date)
                        ->where('departure_time', $departure)
                        ->update([
                            'status' => $item['status'] ?? null,
                            'note' => $note ?: null,
                            'source_updated_at' => now(),
                        ]);
                } else {
                    throw $e;
                }
            }
        }

        return response()->json([
            'message' => 'OK',
            'route' => $routeName,
            'count' => $created,
        ]);
    }
}
