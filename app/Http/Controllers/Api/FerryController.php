<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FerrySetting;
use App\Models\FerrySchedule;
use Illuminate\Support\Facades\Log;

class FerryController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->input('sefer_verisi', []);
        
        // 1. AYARLARI KAYDET
        $s = FerrySetting::firstOrNew();
        $s->title = $data['tarife_basligi'] ?? 'Gökçeada Feribot Saatleri';
        $s->has_announcement = filter_var($data['duyuru_aktif']??false, FILTER_VALIDATE_BOOLEAN);
        $s->announcement_text = $data['duyuru_metni'] ?? null;
        $s->announcement_type = $data['duyuru_turu'] ?? 'info';
        $s->save();

        // 2. ESKİLERİ SİL
        FerrySchedule::truncate();

        $count = 0;
        if (!empty($data['seferler']) && is_array($data['seferler'])) {
            foreach ($data['seferler'] as $sf) {
                // Ham verileri al
                $k = $sf['kabatepe_kalkis'] ?? '-';
                $g = $sf['gokceada_kalkis'] ?? '-';

                // --- TEMİZLİK (ÇÖP VERİLERİ AT) ---
                
                // 1. Eğer içinde harf varsa (ECEAB, KALKIŞ vb.) bu bir saat değildir -> '-' yap.
                if (preg_match('/[a-zA-Z]/', $k)) $k = '-';
                if (preg_match('/[a-zA-Z]/', $g)) $g = '-';

                // 2. Sadece ve sadece "00:00" formatına uyanları al (Regex)
                // Başında ve sonunda boşluk olmasın, tam eşleşsin.
                if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $k)) $k = '-';
                if (!preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $g)) $g = '-';

                // İki taraf da boşsa veya '-' ise kaydetme, geç.
                if ($k === '-' && $g === '-') continue;

                // Veritabanına Yaz
                FerrySchedule::create([
                    'departure_time' => $k,
                    'arrival_time'   => $g,
                    'route'          => 'KABATEPE-GOKCEADA'
                ]);
                $count++;
            }
        }
        
        Log::info("TEMIZLIK YAPILDI. Kaydedilen Net Sefer Sayısı: $count");
        return response()->json(['status' => 'success', 'count' => $count]);
    }

    public function index()
    {
        return view('ferry', [
            'settings' => FerrySetting::first(),
            'schedules' => FerrySchedule::all()
        ]);
    }
}
