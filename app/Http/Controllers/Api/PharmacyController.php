<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutyPharmacy;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PharmacyController extends Controller
{
    // --- 1. EKRANA GOSTERME (WEB) ---
    public function index()
    {
        // Bugunun tarihini al
        $today = now()->format('Y-m-d');
        
        // Bugunun ve gelecegin nobetcilerini getir
        $pharmacies = DutyPharmacy::whereDate('duty_date', '>=', $today)
            ->orderBy('duty_date')
            ->get();

        // 'pharmacies.blade.php' dosyasina gonder
        return view('pharmacies', ['pharmacies' => $pharmacies]);
    }

    // --- 2. VERI KAYDETME (API - n8n) ---
    public function store(Request $request)
    {
        // 1. Gelen veriyi LOGLA
        Log::info('ECZANE API BAŞLADI. Gelen Veri:', $request->all());

        // 2. Token Kontrolü
        $token = $request->query('token') ?? $request->header('X-Gestas-Key');
        if ($token !== '38ofL4uafubC90OcG2UZFqhBOGB_7vSHyCEMHK3DqcNi82tSE') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 3. Veriyi Yakala
        $items = $request->input('data');
        if (isset($items['data'])) $items = $items['data'];

        if (!$items || !is_array($items)) {
            Log::error('ECZANE API HATASI: Veri boş veya hatalı format.');
            return response()->json(['message' => 'Veri formatı hatalı'], 400);
        }

        $successCount = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                if (empty($item['eczane_adi'])) continue;

                // Tarih Formatlama
                $rawDate = $item['nobet_tarihi'] ?? null;
                $dutyDate = now()->format('Y-m-d');

                if ($rawDate) {
                    try {
                        $dutyDate = Carbon::createFromFormat('d.m.Y', trim($rawDate))->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            $dutyDate = Carbon::parse($rawDate)->format('Y-m-d');
                        } catch (\Exception $e2) { continue; }
                    }
                }

                // KAYDET
                DutyPharmacy::updateOrCreate(
                    [
                        'pharmacy_name' => trim($item['eczane_adi']),
                        'duty_date'     => $dutyDate
                    ],
                    [
                        'district'   => $item['ilce'] ?? 'Gökçeada',
                        'phone'      => $item['telefon'] ?? null,
                        'address'    => $item['adres'] ?? null,
                    ]
                );
                $successCount++;

            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success', 
            'count' => $successCount, 
            'errors' => $errors
        ], 200);
    }
}
