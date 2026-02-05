<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\PrayerTime;

class PrayerTimeSeeder extends Seeder {
    public function run() {
        PrayerTime::truncate();
        $data = [
            ['2026-02-05', '06:49', '08:15', '13:35', '16:20', '18:46', '20:06'],
            ['2026-02-06', '06:48', '08:14', '13:35', '16:21', '18:47', '20:07'],
            ['2026-02-07', '06:47', '08:13', '13:35', '16:22', '18:48', '20:08']
        ];
        foreach ($data as $row) {
            PrayerTime::create([
                'date' => $row[0], 'imsak' => $row[1], 'gunes' => $row[2],
                'ogle' => $row[3], 'ikindi' => $row[4], 'aksam' => $row[5], 'yatsı' => $row[6]
            ]);
        }
    }
}
