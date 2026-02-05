<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gökçeada Hava Durumu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8fafc; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="text-slate-800 min-h-screen pb-10">
    <nav class="bg-[#0f766e] text-white py-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="/" class="font-bold text-2xl tracking-tight">Visit Gökçeada</a>
            <div class="space-x-6 text-sm font-medium">
                <a href="/" class="hover:text-teal-200 transition">Ana Sayfa</a>
                <a href="/feribot-saatleri" class="hover:text-teal-200 transition">Feribot</a>
            </div>
        </div>
    </nav>
    <div class="container mx-auto px-2 md:px-4 py-8 max-w-7xl">
        @if(isset($data) && $data)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8 flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-teal-50 rounded-full -mr-16 -mt-16 z-0 opacity-50"></div>
                <div class="flex items-center gap-6 relative z-10">
                    <img src="{{ $data['guncel_durum']['icon_url'] ?? '' }}" class="w-24 h-24 filter drop-shadow-md">
                    <div>
                        <div class="flex items-start gap-2">
                            <span class="text-7xl font-black text-slate-800">{{ $data['guncel_durum']['sicaklik'] ?? '-' }}°</span>
                            <span class="mt-4 text-xl text-slate-500 font-medium">{{ $data['guncel_durum']['aciklama'] ?? '' }}</span>
                        </div>
                        <div class="flex gap-4 mt-2 text-sm text-slate-500 font-medium">
                            <span class="flex items-center gap-1">🌡️ His: {{ $data['guncel_durum']['hissedilen'] ?? '-' }}°</span>
                            <span class="flex items-center gap-1">💧 Nem: %{{ $data['guncel_durum']['nem'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right relative z-10 mt-4 md:mt-0">
                    <div class="text-lg font-bold text-teal-700">{{ $data['konum'] ?? 'Gökçeada' }}</div>
                    <div class="text-xs text-slate-400">Son Güncelleme: {{ $data['guncelleme_zamani'] ?? '' }}</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8 relative group">
                <div class="bg-slate-50 px-6 py-3 border-b border-slate-200 font-bold text-slate-700 flex justify-between items-center">
                    <span>Saatlik Detaylı Tahmin</span>
                    <span class="text-xs text-slate-400 font-normal md:hidden"><< Kaydır >></span>
                </div>
                <button onclick="document.getElementById('scrollContainer').scrollLeft -= 200" class="absolute left-0 top-1/2 z-20 bg-white/90 p-2 rounded-r-lg shadow-md hover:bg-white hidden md:flex items-center justify-center h-16 w-8 text-teal-600 cursor-pointer transition border border-gray-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                <button onclick="document.getElementById('scrollContainer').scrollLeft += 200" class="absolute right-0 top-1/2 z-20 bg-white/90 p-2 rounded-l-lg shadow-md hover:bg-white hidden md:flex items-center justify-center h-16 w-8 text-teal-600 cursor-pointer transition border border-gray-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>

                <div id="scrollContainer" class="flex overflow-x-auto scrollbar-hide py-8 px-4 relative min-h-[360px] scroll-smooth">
                    <div class="absolute inset-0 pointer-events-none z-0"><div class="border-b border-slate-100 h-1/4 w-full"></div><div class="border-b border-slate-100 h-1/4 w-full"></div><div class="border-b border-slate-100 h-1/4 w-full"></div></div>
                    
                    @if(isset($data['saatlik_tahmin']) && is_array($data['saatlik_tahmin']))
                        @foreach($data['saatlik_tahmin'] as $saat)
                            @php
                                $yagisVal = floatval($saat['yagis_mm'] ?? 0);
                                $barH = min($yagisVal * 20, 70);
                                $barColor = $yagisVal > 0 ? 'bg-blue-500' : 'bg-slate-200';
                                $sicaklik = intval($saat['sicaklik'] ?? 0);
                                $topOffset = 120 - ($sicaklik * 3);
                                
                                // RÜZGAR YÖNÜ HESAPLAMA (DERECEYE ÇEVİR)
                                $yon = $saat['ruzgar_yon'] ?? 'N';
                                $derece = 0; // N
                                if($yon == 'NE') $derece = 45;
                                elseif($yon == 'E') $derece = 90;
                                elseif($yon == 'SE') $derece = 135;
                                elseif($yon == 'S') $derece = 180;
                                elseif($yon == 'SW') $derece = 225;
                                elseif($yon == 'W') $derece = 270;
                                elseif($yon == 'NW') $derece = 315;
                            @endphp
                            <div class="flex-none w-24 flex flex-col items-center justify-between h-[300px] border-r border-slate-50 relative group hover:bg-slate-50 transition-colors z-10">
                                <div class="text-sm font-bold text-slate-500 mb-2">{{ $saat['saat'] ?? '' }}</div>
                                <img src="{{ $saat['icon_url'] ?? '' }}" class="w-14 h-14 mb-2 drop-shadow-sm transform group-hover:scale-110 transition-transform">
                                <div class="absolute w-full text-center" style="top: {{ $topOffset }}px;">
                                    <div class="text-xl font-black text-slate-800 mb-1">{{ $sicaklik }}°</div>
                                    <div class="w-4 h-4 bg-yellow-400 rounded-full border-4 border-white mx-auto shadow-sm relative z-20"></div>
                                </div>
                                <div class="mt-auto w-full flex flex-col items-center pb-2">
                                    <div class="flex flex-col items-center justify-end h-[70px] w-full border-b border-slate-100 mb-2">
                                        @if($yagisVal > 0) <span class="text-[10px] text-blue-600 font-bold mb-1">{{ $yagisVal }}mm</span> @endif
                                        <div class="w-6 rounded-t-md {{ $barColor }} opacity-90 transition-all group-hover:opacity-100" style="height: {{ $barH > 0 ? $barH : 4 }}px;"></div>
                                    </div>
                                    <div class="text-center">
                                        <div style="transform: rotate({{ $derece }}deg);" class="inline-block transition-transform duration-500 mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 19V5M12 5l-4 4M12 5l4 4"/>
                                            </svg>
                                        </div>
                                        <div class="text-xs font-bold text-slate-700">{{ $saat['ruzgar_hiz'] }} <span class="text-[10px] font-normal text-slate-400">km/h</span></div>
                                        <div class="text-[10px] text-teal-600 font-bold uppercase tracking-wide mt-0.5">{{ $yon }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-4 px-2">14 Günlük Tahmin</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if(isset($data['gunluk_tahmin']) && is_array($data['gunluk_tahmin']))
                    @foreach($data['gunluk_tahmin'] as $gun)
                        <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between hover:shadow-md transition cursor-default">
                            <div class="flex items-center gap-4">
                                <div class="bg-slate-50 p-2 rounded-lg">
                                    <img src="{{ $gun['icon_url'] ?? '' }}" class="w-10 h-10">
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $gun['tarih'] ?? '' }}</div>
                                    <div class="text-xs text-slate-500 font-medium">{{ $gun['hava_durumu'] ?? '' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-slate-900">{{ $gun['max_sicaklik'] ?? '-' }}°</span>
                                <span class="text-sm text-slate-400 mx-1">/</span>
                                <span class="text-base font-medium text-slate-500">{{ $gun['min_sicaklik'] ?? '-' }}°</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-96 text-center bg-white rounded-2xl shadow-sm border border-slate-200">
                <div class="w-20 h-20 border-4 border-teal-100 border-t-teal-600 rounded-full animate-spin mb-6"></div>
                <h2 class="text-2xl font-bold text-slate-800">Veriler Hazırlanıyor...</h2>
            </div>
        @endif
    </div>
    <footer class="text-center py-8 text-slate-400 text-sm">© {{ date('Y') }} Visit Gökçeada Weather Service</footer>
</body>
</html>
