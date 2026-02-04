<!DOCTYPE html>
<html class="light" lang="tr">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>{{ $location }} - Gökçeada Hava Durumu - Visit Gökçeada</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
        <script id="tailwind-config">
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            "primary": "#ff6b6b",
                            "accent-teal": "#4ECDC4",
                            "background-light": "#FFFBF0",
                            "background-dark": "#1F2937",
                            "anthracite": "#1F2937"
                        },
                        fontFamily: {
                            "display": ["Plus Jakarta Sans", "sans-serif"]
                        },
                    },
                },
            }
        </script>
        <style type="text/tailwindcss">
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-background-light text-anthracite dark:bg-background-dark dark:text-white min-h-screen">
        <div class="relative flex min-h-screen w-full flex-col">
            <header class="sticky top-0 z-40 w-full border-b border-anthracite/5 bg-background-light/80 backdrop-blur-md dark:bg-background-dark/80 dark:border-white/10">
                <div class="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4 lg:px-10">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-white">
                            <span class="material-symbols-outlined text-xl">schedule</span>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-lg font-extrabold tracking-tight">{{ $location }} - Saatlik Tahminler</h1>
                            <p class="text-xs font-medium text-anthracite/60 dark:text-white/60">Önümüzdeki saatler için ayrıntılı hava durumu</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('weather.index') }}" class="inline-flex items-center gap-1 rounded-full border border-anthracite/10 bg-white px-4 py-1.5 text-xs font-semibold text-anthracite shadow-sm hover:bg-primary hover:text-white dark:bg-background-dark/60 dark:border-white/10">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            Tüm lokasyonlar
                        </a>
                    </div>
                </div>
            </header>

            <main class="mx-auto flex w-full max-w-[1200px] flex-1 flex-col px-6 py-6 lg:px-10 lg:py-10">
                @if ($forecasts->isEmpty())
                    <div class="mt-10 rounded-2xl bg-white p-6 text-sm font-medium text-anthracite/70 shadow-sm ring-1 ring-anthracite/5 dark:bg-background-dark/60 dark:text-white/70 dark:ring-white/10">
                        Bu lokasyon için geleceğe yönelik hava durumu verisi bulunamadı.
                    </div>
                @else
                    @php
                        /** @var \App\Models\WeatherForecast|null $first */
                        $first = $forecasts->first();
                    @endphp

                    <section class="mb-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-anthracite/5 dark:bg-background-dark/60 dark:ring-white/10">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-base font-extrabold text-anthracite dark:text-white">Genel Bakış</h2>
                                @if ($first?->formatted_date || $first?->time_label)
                                    <p class="mt-1 text-xs font-medium text-anthracite/60 dark:text-white/60">
                                        İlk tahmin: {{ $first?->forecast_time?->translatedFormat('j F Y l') ?? $first?->formatted_date }}@if($first?->time_label) · {{ $first?->time_label }} @endif
                                    </p>
                                @endif
                            </div>
                            @if ($first && $first->temperature_c !== null)
                                <div class="text-right">
                                    <div class="text-3xl font-extrabold text-anthracite dark:text-white">
                                        {{ number_format($first->temperature_c, 1, ',', '.') }}°C
                                    </div>
                                    @if ($first->wind_speed_kmh !== null || $first->wind_direction)
                                        <div class="mt-1 text-[11px] font-semibold text-anthracite/70 dark:text-white/70">
                                            Rüzgar: {{ trim(($first->wind_direction ?? '') . ' ' . ($first->wind_speed_kmh !== null ? number_format($first->wind_speed_kmh, 1, ',', '.') . ' km/s' : '')) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </section>

                    @php
                        // Gün bazlı grupla: her gün için ayrı kart
                        $groupedByDate = $forecasts->groupBy(function ($forecast) {
                            return optional($forecast->forecast_time)->format('Y-m-d') ?? $forecast->formatted_date;
                        });
                    @endphp

                    @foreach ($groupedByDate as $dateKey => $dayForecasts)
                        @php
                            /** @var \App\Models\WeatherForecast|null $dayFirst */
                            $dayFirst = $dayForecasts->first();
                            $displayDate = $dayFirst?->forecast_time
                                ? $dayFirst->forecast_time->translatedFormat('j F Y l')
                                : ($dayFirst->formatted_date ?? $dateKey);
                        @endphp

                        <section class="mb-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-anthracite/5 dark:bg-background-dark/60 dark:ring-white/10">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <h2 class="text-sm font-extrabold text-anthracite dark:text-white">{{ $displayDate }}</h2>
                                <p class="text-[11px] font-medium text-anthracite/60 dark:text-white/60">
                                    Bu gün için {{ $dayForecasts->count() }} tahmin kaydı
                                </p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full border-collapse text-left text-xs">
                                    <thead>
                                        <tr class="border-b border-anthracite/10 dark:border-white/10 bg-anthracite/3 dark:bg-white/5">
                                            <th class="px-3 py-2 font-semibold">Durum</th>
                                            <th class="px-3 py-2 font-semibold">Saat</th>
                                            <th class="px-3 py-2 font-semibold">Sıcaklık</th>
                                            <th class="px-3 py-2 font-semibold">Rüzgar</th>
                                            <th class="px-3 py-2 font-semibold">Yağış</th>
                                            <th class="px-3 py-2 font-semibold">Nem</th>
                                            <th class="px-3 py-2 font-semibold">Basınç</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dayForecasts as $forecast)
                                            @php
                                                $temp = $forecast->temperature_c;
                                                $rain = $forecast->rain_mm;
                                                $statusIcon = 'wb_sunny';
                                                $statusLabel = 'Güneşli / Açık';
                                                $statusClass = 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200';

                                                if (is_numeric($temp) && $temp <= 0) {
                                                    $statusIcon = 'ac_unit';
                                                    $statusLabel = 'Çok soğuk';
                                                    $statusClass = 'bg-sky-50 text-sky-700 dark:bg-sky-900/40 dark:text-sky-200';
                                                } elseif (is_numeric($rain) && $rain > 0.1) {
                                                    $statusIcon = 'water_drop';
                                                    $statusLabel = 'Yağışlı';
                                                    $statusClass = 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200';
                                                }
                                            @endphp
                                            <tr class="border-b border-anthracite/5 dark:border-white/5 hover:bg-anthracite/3 dark:hover:bg-white/5">
                                                <td class="px-3 py-1.5">
                                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $statusClass }}">
                                                        <span class="material-symbols-outlined text-[14px]">{{ $statusIcon }}</span>
                                                        <span>{{ $statusLabel }}</span>
                                                    </span>
                                                </td>
                                                <td class="px-3 py-1.5">{{ $forecast->time_label ?? $forecast->forecast_time?->format('H:i') }}</td>
                                                <td class="px-3 py-1.5">{{ $forecast->temperature_c !== null ? number_format($forecast->temperature_c, 1, ',', '.') . '°C' : '-' }}</td>
                                                <td class="px-3 py-1.5">
                                                    @php
                                                        $windParts = [];
                                                        if ($forecast->wind_direction) {
                                                            $windParts[] = $forecast->wind_direction;
                                                        }
                                                        if ($forecast->wind_speed_kmh !== null) {
                                                            $windParts[] = number_format($forecast->wind_speed_kmh, 1, ',', '.') . ' km/s';
                                                        }
                                                    @endphp
                                                    {{ implode(' ', $windParts) ?: '-' }}
                                                </td>
                                                <td class="px-3 py-1.5">{{ $forecast->rain_mm !== null ? number_format($forecast->rain_mm, 1, ',', '.') . ' mm' : '0.0 mm' }}</td>
                                                <td class="px-3 py-1.5">{{ $forecast->humidity !== null ? $forecast->humidity . '%' : '-' }}</td>
                                                <td class="px-3 py-1.5">{{ $forecast->pressure_hpa !== null ? $forecast->pressure_hpa . ' hPa' : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endforeach
                @endif
            </main>
        </div>
    </body>
</html>
