<!DOCTYPE html>
<html class="light" lang="tr">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>Gökçeada Hava Durumu - Visit Gökçeada</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
                            <span class="material-symbols-outlined text-xl">wb_sunny</span>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-lg font-extrabold tracking-tight">Gökçeada Hava Durumu</h1>
                            <p class="text-xs font-medium text-anthracite/60 dark:text-white/60">Merkez, köyler ve sahiller için son güncel tahminler</p>
                        </div>
                    </div>
                    <a href="/" class="inline-flex items-center gap-1 rounded-full border border-anthracite/10 bg-white px-4 py-1.5 text-xs font-semibold text-anthracite shadow-sm hover:bg-primary hover:text-white dark:bg-background-dark/60 dark:border-white/10">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Ana sayfaya dön
                    </a>
                </div>
            </header>

            <main class="mx-auto flex w-full max-w-[1200px] flex-1 flex-col px-6 py-6 lg:px-10 lg:py-10">
                @if ($forecastsByLocation->isEmpty())
                    <div class="mt-10 rounded-2xl bg-white p-6 text-sm font-medium text-anthracite/70 shadow-sm ring-1 ring-anthracite/5 dark:bg-background-dark/60 dark:text-white/70 dark:ring-white/10">
                        Henüz hava durumu verisi bulunamadı. n8n akışınızdan bu API'ye veri gönderildiğinde burası otomatik dolacaktır.
                    </div>
                @else
                    @php
                        $groupMap = [
                            // Merkez
                            'Gökçeada Merkez' => '🏛️ Merkez',

                            // Köyler
                            'Zeytinliköy' => '🏘️ Köyler',
                            'Tepeköy' => '🏘️ Köyler',
                            'Dereköy' => '🏘️ Köyler',
                            'Eski Bademli' => '🏘️ Köyler',
                            'Yeni Bademli' => '🏘️ Köyler',
                            'Kaleköy (Liman)' => '🏘️ Köyler',
                            'Yukarı Kaleköy' => '🏘️ Köyler',
                            'Uğurlu Köyü' => '🏘️ Köyler',
                            'Eşelek' => '🏘️ Köyler',
                            'Şirin Köy' => '🏘️ Köyler',

                            // Limanlar
                            'Kaleköy Limanı' => '⚓ Limanlar',
                            'Kuzulimanı (Feribot)' => '⚓ Limanlar',

                            // Plajlar ve koylar
                            'Yıldız Koy' => '🏖️ Plajlar ve Koylar',
                            'Mavi Koy' => '🏖️ Plajlar ve Koylar',
                            'Marmaros' => '🏖️ Plajlar ve Koylar',
                            'Aydıncık (Kefaloz) Plajı' => '🏖️ Plajlar ve Koylar',
                            'Kapıkaya Plajı' => '🏖️ Plajlar ve Koylar',
                            'Laz Koyu' => '🏖️ Plajlar ve Koylar',
                            'Uğurlu Plajı' => '🏖️ Plajlar ve Koylar',
                            'Gizli Liman' => '🏖️ Plajlar ve Koylar',
                        ];

                        $lastGroup = null;
                    @endphp

                    <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($forecastsByLocation as $location => $items)
                            @php
                                $items = $items->values();
                                /** @var \App\Models\WeatherForecast|null $latest */
                                $latest = $items->first();

                                $groupLabel = $groupMap[$location] ?? 'Diğer Lokasyonlar';
                            @endphp
                            @if ($groupLabel !== $lastGroup)
                                <div class="col-span-full mt-4 flex items-center gap-2 first:mt-0">
                                    <span class="text-[11px] font-semibold uppercase tracking-wide text-anthracite/60 dark:text-white/60">{{ $groupLabel }}</span>
                                    <span class="h-px flex-1 bg-anthracite/10 dark:bg-white/10"></span>
                                </div>
                                @php $lastGroup = $groupLabel; @endphp
                            @endif
                            <div class="flex h-full flex-col justify-between rounded-2xl bg-white p-5 shadow-sm ring-1 ring-anthracite/5 dark:bg-background-dark/60 dark:ring-white/10" x-data="{
                                index: 0,
                                items: @js($items->map(function ($f) {
                                    return [
                                        'date' => optional($f->forecast_time)->format('d.m.Y'),
                                        'time' => $f->time_label ?? optional($f->forecast_time)->format('H:i'),
                                        'temp' => $f->temperature_c,
                                        'wind_speed' => $f->wind_speed_kmh,
                                        'wind_dir' => $f->wind_direction,
                                        'rain' => $f->rain_mm,
                                        'humidity' => $f->humidity,
                                        'pressure' => $f->pressure_hpa,
                                    ];
                                })),
                                get current() { return this.items[this.index] ?? null },
                                next() { if (this.index < this.items.length - 1) this.index++ },
                                prev() { if (this.index > 0) this.index-- },
                                conditionLabel() {
                                    if (!this.current) return 'Durum bilinmiyor';
                                    const temp = this.current.temp;
                                    const rain = this.current.rain;

                                    if (typeof temp === 'number' && temp <= 0) {
                                        return 'Çok soğuk';
                                    }

                                    if (typeof rain === 'number' && rain > 0.1) {
                                        return 'Yağışlı';
                                    }

                                    return 'Güneşli / Açık';
                                },
                                conditionBadgeClass() {
                                    if (!this.current) return 'bg-anthracite/5 text-anthracite/70 dark:bg-white/10 dark:text-white/70';
                                    const temp = this.current.temp;
                                    const rain = this.current.rain;

                                    if (typeof temp === 'number' && temp <= 0) {
                                        return 'bg-sky-50 text-sky-700 dark:bg-sky-900/40 dark:text-sky-200';
                                    }

                                    if (typeof rain === 'number' && rain > 0.1) {
                                        return 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200';
                                    }

                                    return 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200';
                                },
                            }">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex flex-col gap-1">
                                        <h2 class="text-sm font-extrabold text-anthracite dark:text-white">{{ $location }}</h2>
                                        <template x-if="current">
                                            <p class="text-[11px] font-medium text-anthracite/60 dark:text-white/60">
                                                <span x-text="current.date"></span>
                                                ·
                                                <span x-text="current.time"></span>
                                            </p>
                                        </template>
                                    </div>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-blue-500 dark:bg-blue-900/30">
                                        <span class="material-symbols-outlined text-base" x-text="(() => {
                                            if (!current) return 'cloud';

                                            const temp = current.temp;
                                            const rain = current.rain;

                                            if (typeof temp === 'number' && temp <= 0) {
                                                return 'ac_unit';
                                            }

                                            if (typeof rain === 'number' && rain > 0.1) {
                                                return 'water_drop';
                                            }

                                            return 'wb_sunny';
                                        })()"></span>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-col gap-2">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <div class="text-2xl font-extrabold text-anthracite dark:text-white">
                                            <span x-text="current && current.temp !== null ? (current.temp.toFixed(1).replace('.', ',') + '°C') : 'Veri yok'"></span>
                                        </div>
                                        <div class="flex items-center gap-1 text-[10px] font-medium text-anthracite/60 dark:text-white/60">
                                            <button type="button" @click="prev" :disabled="index === 0" class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-anthracite/10 bg-white disabled:opacity-40 dark:bg-background-dark/60 dark:border-white/10">
                                                <span class="material-symbols-outlined text-[14px]">chevron_left</span>
                                            </button>
                                            <span x-text="(index + 1) + ' / ' + items.length"></span>
                                            <button type="button" @click="next" :disabled="index === items.length - 1" class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-anthracite/10 bg-white disabled:opacity-40 dark:bg-background-dark/60 dark:border-white/10">
                                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                            </button>
                                        </div>
                                    </div>
                                    <template x-if="current">
                                        <div class="mt-1">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="conditionBadgeClass()" x-text="conditionLabel()"></span>
                                        </div>
                                    </template>
                                    <template x-if="current">
                                        <div>
                                            <template x-if="current.wind_dir || current.wind_speed">
                                                <div class="text-xs font-semibold text-anthracite/70 dark:text-white/70">
                                                    Rüzgar:
                                                    <span x-text="[(current.wind_dir || ''), (current.wind_speed != null ? (current.wind_speed.toFixed(1).replace('.', ',') + ' km/s') : '')].filter(Boolean).join(' ')"></span>
                                                </div>
                                            </template>
                                            <div class="mt-2 grid grid-cols-3 gap-2 text-[11px] font-medium text-anthracite/70 dark:text-white/70">
                                                <div class="rounded-xl bg-anthracite/3 px-2 py-1 dark:bg-white/5">
                                                    <div class="text-[10px] uppercase tracking-tight text-anthracite/50 dark:text-white/50">Yağış</div>
                                                    <div x-text="current.rain != null ? current.rain.toFixed(1).replace('.', ',') + ' mm' : '0.0 mm'"></div>
                                                </div>
                                                <div class="rounded-xl bg-anthracite/3 px-2 py-1 dark:bg-white/5">
                                                    <div class="text-[10px] uppercase tracking-tight text-anthracite/50 dark:text-white/50">Nem</div>
                                                    <div x-text="current.humidity != null ? current.humidity + '%' : '-' "></div>
                                                </div>
                                                <div class="rounded-xl bg-anthracite/3 px-2 py-1 dark:bg-white/5">
                                                    <div class="text-[10px] uppercase tracking-tight text-anthracite/50 dark:text-white/50">Basınç</div>
                                                    <div x-text="current.pressure != null ? current.pressure + ' hPa' : '-' "></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="items.length">
                                        <div class="mt-3 overflow-x-auto">
                                            <div class="flex min-w-max gap-1.5">
                                                <template x-for="(h, i) in items" :key="i">
                                                    <button type="button" @click="index = i" class="flex flex-col items-center rounded-xl bg-anthracite/3 px-2 py-1.5 text-[10px] font-medium text-anthracite/70 dark:bg-white/5 dark:text-white/70" :class="{ 'ring-2 ring-primary/70 bg-white dark:bg-background-dark': i === index }">
                                                        <div x-text="h.time"></div>
                                                        <span class="material-symbols-outlined text-[16px]" x-text="(() => {
                                                            const temp = h.temp;
                                                            const rain = h.rain;

                                                            if (typeof temp === 'number' && temp <= 0) {
                                                                return 'ac_unit';
                                                            }

                                                            if (typeof rain === 'number' && rain > 0.1) {
                                                                return 'water_drop';
                                                            }

                                                            return 'wb_sunny';
                                                        })()"></span>
                                                        <div class="mt-0.5 font-semibold" x-text="h.temp != null ? Math.round(h.temp) + '°' : '-' "></div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="mt-3 flex justify-end">
                                        <a href="{{ route('weather.detail', ['location' => $location]) }}" class="inline-flex items-center gap-1 rounded-full border border-anthracite/10 bg-white px-3 py-1 text-[11px] font-semibold text-anthracite shadow-sm hover:bg-primary hover:text-white dark:bg-background-dark/60 dark:border-white/10">
                                            <span class="material-symbols-outlined text-[13px]">schedule</span>
                                            Tüm saatleri gör
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </main>
        </div>

    </body>
</html>
