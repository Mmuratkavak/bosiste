<!DOCTYPE html>
<html class="light" lang="tr">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>Visit Gökçeada Ana Sayfa</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        <script>
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: { "primary": "#ff6b6b", "accent-teal": "#4ECDC4", "background-light": "#FFFBF0", "background-dark": "#1F2937", "anthracite": "#1F2937" },
                        fontFamily: { "display": ["Plus Jakarta Sans", "sans-serif"] },
                        borderRadius: { "DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px" },
                    },
                },
            }
        </script>
        <style type="text/tailwindcss">
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .custom-scrollbar::-webkit-scrollbar { height: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(31,41,55,0.1); border-radius: 10px; }
        </style>
    </head>
    <body class="bg-background-light text-anthracite dark:bg-background-dark dark:text-white transition-colors duration-300">
        <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
            <header class="sticky top-0 z-50 w-full border-b border-anthracite/5 bg-background-light/80 backdrop-blur-md dark:bg-background-dark/80 dark:border-white/10">
                <div class="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4 lg:px-10">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
                            <span class="material-symbols-outlined text-2xl">sailing</span>
                        </div>
                        <h2 class="text-xl font-extrabold tracking-tight text-anthracite dark:text-white">Visit Gökçeada</h2>
                    </div>
                    <div class="flex items-center gap-4 lg:gap-8">
                        <nav class="hidden items-center gap-6 md:flex">
                            <a class="text-sm font-semibold text-anthracite/70 hover:text-primary dark:text-white/70" href="#">İşletme Paneli</a>
                        </nav>
                        <div class="flex items-center gap-3">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/admin') }}" class="hidden text-sm font-bold text-anthracite hover:text-primary dark:text-white md:block px-4 py-2">İşletme Paneli</a>
                                @else
                                    <a href="{{ route('login') }}" class="hidden text-sm font-bold text-anthracite hover:text-primary dark:text-white md:block px-4 py-2">Giriş Yap</a>
                                @endauth
                            @endif
                            <button class="flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary/20 transition-transform hover:scale-105 active:scale-95">İşletmeni Ekle</button>
                        </div>
                    </div>
                </div>
            </header>
            <main class="mx-auto flex w-full max-w-[1200px] flex-1 flex-col px-6 py-8 lg:px-10 lg:py-16">
                <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
                    <div class="flex flex-col gap-8">
                        <div class="flex flex-col gap-4">
                            <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-anthracite dark:text-white md:text-5xl lg:text-6xl">
                                Gökçeada'daki en iyi <span class="text-primary">işletmeleri</span> keşfet
                            </h1>
                            <p class="max-w-[500px] text-base font-medium leading-relaxed text-anthracite/60 dark:text-white/60 md:text-lg">
                                Ege'nin ortasında zamanın durduğu, doğanın ve tarihin iç içe geçtiği adanın en özel noktalarını keşfedin.
                            </p>
                        </div>

                        <div class="relative w-full max-w-[600px]">
                            <form method="GET" action="/search" class="group flex h-16 w-full items-center gap-2 rounded-2xl bg-white p-2 shadow-xl shadow-anthracite/5 ring-1 ring-anthracite/5 transition-all focus-within:ring-primary/30 dark:bg-background-dark/50 dark:ring-white/10">
                                <div class="flex items-center pl-3 text-anthracite/40"><span class="material-symbols-outlined">search</span></div>
                                <input name="q" class="h-full w-full border-none bg-transparent text-sm font-medium text-anthracite placeholder:text-anthracite/40 focus:ring-0 dark:text-white md:text-base" placeholder="Kaleköy kahvaltı, Aydıncık plajı, bağ evi..." type="text"/>
                                <button class="flex h-12 min-w-[120px] items-center justify-center rounded-xl bg-primary px-6 text-sm font-bold text-white transition-all hover:bg-primary/90 md:text-base" type="submit">Keşfet</button>
                            </form>
                        </div>

                        <div class="mt-4 flex w-full gap-3 overflow-x-auto pb-2 custom-scrollbar lg:flex-wrap">
                            {{-- HAVA DURUMU KARTI --}}
                            <div class="flex flex-shrink-0 items-center gap-3 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-anthracite/5 dark:bg-background-dark/40 dark:ring-white/10">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-blue-500 dark:bg-blue-900/20">
                                    <span class="material-symbols-outlined">wb_sunny</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-anthracite/50 dark:text-white/50 uppercase tracking-tighter">Hava Durumu</span>
                                    @if (!empty($currentWeather))
                                        <span class="text-sm font-extrabold text-anthracite dark:text-white whitespace-nowrap">
                                            {{ $currentWeather->temperature_c }}°C, {{ $currentWeather->wind_speed_kmh }} km/s
                                        </span>
                                        <a href="{{ route('weather.index') }}" class="text-[11px] font-semibold text-primary hover:underline mt-0.5">Detayları gör</a>
                                    @else
                                        <span class="text-sm font-extrabold text-anthracite dark:text-white whitespace-nowrap">Veri yok</span>
                                    @endif
                                </div>
                            </div>

                            {{-- ECZANE KARTI --}}
                            @if (!empty($currentPharmacy))
                                <div class="flex flex-shrink-0 items-center gap-3 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-anthracite/5 dark:bg-background-dark/40 dark:ring-white/10">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-red-500 dark:bg-red-900/20">
                                        <span class="material-symbols-outlined">local_pharmacy</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-anthracite/50 dark:text-white/50 uppercase tracking-tighter">Nöbetçi Eczane</span>
                                        <span class="text-sm font-extrabold text-anthracite dark:text-white whitespace-nowrap">
                                            {{ $currentPharmacy->pharmacy_name }}
                                        </span>
                                        <a href="{{ route('pharmacies.index') }}" class="text-[11px] font-semibold text-primary hover:underline mt-0.5">Tüm listeyi gör</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="aspect-square w-full rounded-[2.5rem] bg-cover bg-center shadow-2xl" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCwpfv5uJR-LRQuxN9lLfsEspCBgN5isqo3MzgL5ZzKDxDB1b9GDGgd42w-PttnjtLXcO1L6b2vczx_545ahP1UvEyXrrYDo30hOEE1Bsxhuyy99xKhAaqezhJ0aIsFRud37EBf6sRFbprPlzEZaoVtcf8zCyk8l6bQUU8JCjoxUMVwZzGCkFLmdIGBRoI0-Xi9aO45xl8__sw0mk1AbIoy54H0VzfVK9Rp1vCGAO0Qu70CwtFIgxJeUN2Aew_r47oUA0PvbjXB-lQ");'>
                            <div class="absolute -bottom-6 -left-6 flex items-center gap-3 rounded-2xl bg-white p-4 shadow-xl dark:bg-background-dark">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-accent-teal text-white">
                                    <span class="material-symbols-outlined">star</span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-anthracite/50 dark:text-white/50 uppercase tracking-wider">Popüler</p>
                                    <p class="text-sm font-extrabold text-anthracite dark:text-white">Laz Koyu Plajı</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="group relative overflow-hidden rounded-2xl bg-accent-teal p-8 text-white transition-transform hover:-translate-y-1">
                        <div class="relative z-10 flex h-full flex-col justify-between gap-4">
                            <div>
                                <span class="material-symbols-outlined mb-4 text-4xl opacity-80">auto_awesome</span>
                                <h3 class="text-2xl font-extrabold">Yerel Kürasyon</h3>
                                <p class="mt-2 text-sm font-medium opacity-90 lg:text-base">Adanın en gizli kalmış lezzet durakları uzman ekibimiz tarafından seçildi.</p>
                            </div>
                            <div class="flex items-center gap-2 font-bold group-hover:underline">Keşfetmeye Başla <span class="material-symbols-outlined">arrow_forward</span></div>
                        </div>
                        <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                    </div>
                    <div class="group relative overflow-hidden rounded-2xl bg-primary p-8 text-white transition-transform hover:-translate-y-1">
                        <div class="relative z-10 flex h-full flex-col justify-between gap-4">
                            <div>
                                <span class="material-symbols-outlined mb-4 text-4xl opacity-80">landscape</span>
                                <h3 class="text-2xl font-extrabold">Ada Ruhu</h3>
                                <p class="mt-2 text-sm font-medium opacity-90 lg:text-base">Gökçeada'nın otantik atmosferini yansıtan konaklama yerleri.</p>
                            </div>
                            <div class="flex items-center gap-2 font-bold group-hover:underline">Hemen İncele <span class="material-symbols-outlined">arrow_forward</span></div>
                        </div>
                        <div class="absolute -bottom-8 -right-8 h-40 w-40 rounded-full bg-black/5 blur-3xl"></div>
                    </div>
                </div>

                <div class="mt-20 flex items-center justify-between px-2">
                    <h2 class="text-2xl font-extrabold tracking-tight text-anthracite dark:text-white">Kategoriler</h2>
                    <a class="text-sm font-bold text-primary hover:underline" href="#">Tümünü Gör</a>
                </div>
                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-5">
                    <div class="group flex cursor-pointer flex-col items-center justify-center gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-anthracite/5 transition-all hover:bg-primary/5 hover:shadow-lg hover:ring-primary/20 dark:bg-background-dark dark:ring-white/10">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#E8F5E9] text-[#2E7D32] group-hover:scale-110 transition-transform"><span class="material-symbols-outlined text-3xl">beach_access</span></div>
                        <span class="text-sm font-bold text-anthracite dark:text-white">Plajlar</span>
                    </div>
                    <div class="group flex cursor-pointer flex-col items-center justify-center gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-anthracite/5 transition-all hover:bg-primary/5 hover:shadow-lg hover:ring-primary/20 dark:bg-background-dark dark:ring-white/10">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#FFF3E0] text-[#EF6C00] group-hover:scale-110 transition-transform"><span class="material-symbols-outlined text-3xl">restaurant</span></div>
                        <span class="text-sm font-bold text-anthracite dark:text-white">Restoranlar</span>
                    </div>
                    <div class="group flex cursor-pointer flex-col items-center justify-center gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-anthracite/5 transition-all hover:bg-primary/5 hover:shadow-lg hover:ring-primary/20 dark:bg-background-dark dark:ring-white/10">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#E3F2FD] text-[#1565C0] group-hover:scale-110 transition-transform"><span class="material-symbols-outlined text-3xl">hotel</span></div>
                        <span class="text-sm font-bold text-anthracite dark:text-white">Konaklama</span>
                    </div>
                    <div class="group flex cursor-pointer flex-col items-center justify-center gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-anthracite/5 transition-all hover:bg-primary/5 hover:shadow-lg hover:ring-primary/20 dark:bg-background-dark dark:ring-white/10">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#F3E5F5] text-[#7B1FA2] group-hover:scale-110 transition-transform"><span class="material-symbols-outlined text-3xl">wine_bar</span></div>
                        <span class="text-sm font-bold text-anthracite dark:text-white">Bağ Yolları</span>
                    </div>
                </div>
            </main>
            <footer class="mt-20 border-t border-anthracite/5 bg-white/50 py-12 dark:bg-background-dark/50">
                <div class="mx-auto max-w-[1200px] px-6 lg:px-10">
                    <div class="flex flex-col items-center justify-between gap-8 md:flex-row">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-white"><span class="material-symbols-outlined text-lg">sailing</span></div>
                            <span class="text-lg font-extrabold text-anthracite dark:text-white">Visit Gökçeada</span>
                        </div>
                        <div class="flex gap-8 text-sm font-bold text-anthracite/60 dark:text-white/60">
                            <a class="hover:text-primary" href="#">Hakkımızda</a>
                            <a class="hover:text-primary" href="#">İletişim</a>
                            <a class="hover:text-primary" href="#">KVKK</a>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-anthracite/5 hover:bg-primary hover:text-white dark:bg-white/5"><span class="material-symbols-outlined text-xl">share</span></div>
                            <div class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-anthracite/5 hover:bg-primary hover:text-white dark:bg-white/5"><span class="material-symbols-outlined text-xl">location_on</span></div>
                        </div>
                    </div>
                    <div class="mt-8 text-center text-xs font-medium text-anthracite/40 dark:text-white/40">© 2024 Visit Gökçeada. Tüm hakları saklıdır.</div>
                </div>
            </footer>
        </div>
    </body>
</html>
