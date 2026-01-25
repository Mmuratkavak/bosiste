@php
    $initialPanorama = Storage::url($record->path);
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Ana Giriş 360°</h2>
                <p class="text-sm text-gray-400 mt-1">
                    Sürükle • Döndür • Kaydır • Zoom • Hotspot: Geçiş Yap
                </p>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Önizleme
                </button>
                <button class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                    Düzenleme Modu
                </button>
                <button class="px-3 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                    ✕
                </button>
            </div>
        </div>

        {{-- 360 Viewer --}}
        <div class="relative bg-black rounded-xl overflow-hidden" style="height: 600px;">
            <div id="viewer360" class="w-full h-full"></div>
            
            {{-- Hotspot Example --}}
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                <button class="w-12 h-12 bg-white/20 backdrop-blur-sm border-2 border-white rounded-full flex items-center justify-center hover:bg-white/30 transition">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
                
                {{-- Hotspot Modal --}}
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-4 bg-white rounded-lg shadow-xl p-4 w-64 hidden" id="hotspotModal">
                    <h3 class="font-semibold text-gray-900 mb-2">Hotspot Ekle</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-600">HEDEF MEKAN SEÇ</label>
                            <select class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option>Vip Salonu</option>
                                @foreach($this->getOther360Images() as $image)
                                    <option>{{ $image->tenant?->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                            Geçiş Noktası Oluştur
                        </button>
                    </div>
                </div>
            </div>

            {{-- Control Buttons --}}
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-2 bg-black/50 backdrop-blur-sm rounded-lg p-2">
                <button class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </button>
                <button class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>
                <button class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
                <button class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <button class="w-10 h-10 bg-blue-600 hover:bg-blue-700 rounded-lg flex items-center justify-center text-white transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            {{-- Compass --}}
            <div class="absolute bottom-24 right-6">
                <div class="w-16 h-16 rounded-full border-2 border-blue-500 bg-black/30 backdrop-blur-sm flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-white text-xs text-center mt-1">PUSULA</p>
            </div>
        </div>

        {{-- Thumbnails --}}
        <div class="grid grid-cols-4 gap-4">
            {{-- Active Image --}}
            <div class="relative group cursor-pointer" onclick="window.loadPano('{{ $initialPanorama }}')">
                <img 
                    src="{{ $initialPanorama }}" 
                    alt="Aktif" 
                    class="w-full h-32 object-cover rounded-lg border-2 border-blue-500"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent rounded-lg flex items-end p-3">
                    <span class="text-white text-sm font-medium">AKTİF</span>
                </div>
            </div>

            {{-- Other 360 Images --}}
            @foreach($this->getOther360Images() as $image)
            @php
                $thumbUrl = Storage::url($image->path);
            @endphp
            <div class="relative group cursor-pointer" onclick="window.loadPano('{{ $thumbUrl }}')">
                <img 
                    src="{{ $thumbUrl }}" 
                    alt="{{ $image->tenant?->name }}" 
                    class="w-full h-32 object-cover rounded-lg hover:border-2 hover:border-blue-400 transition"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent rounded-lg flex items-end p-3 opacity-0 group-hover:opacity-100 transition">
                    <span class="text-white text-sm font-medium">{{ Str::upper(Str::limit($image->tenant?->name, 15)) }}</span>
                </div>
            </div>
            @endforeach

            {{-- Add New --}}
            <a href="{{ route('filament.admin.resources.media-files.create') }}" class="relative group cursor-pointer border-2 border-dashed border-gray-600 rounded-lg hover:border-blue-400 transition">
                <div class="w-full h-32 flex flex-col items-center justify-center">
                    <svg class="w-8 h-8 text-gray-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-gray-500 text-sm mt-2 group-hover:text-blue-400 transition">GÖRSEL EKLE</span>
                </div>
            </a>
        </div>
    </div>

    {{-- Pannellum 360° Viewer --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css" />
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <script>
        // Simple hotspot toggle
        document.querySelectorAll('[class*="w-12 h-12"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = document.getElementById('hotspotModal');
                if (modal) {
                    modal.classList.toggle('hidden');
                }
            });
        });

        window.panoViewer = null;

        window.loadPano = function (url) {
            const containerId = 'viewer360';

            // İlk kez veya her seferinde yeniden başlat
            window.panoViewer = pannellum.viewer(containerId, {
                type: 'equirectangular',
                panorama: url,
                autoLoad: true,
                showControls: true,
                compass: true,
                hfov: 110,
                minHfov: 50,
                maxHfov: 120,
                pitch: 0,
                yaw: 0,
            });
        };

        document.addEventListener('DOMContentLoaded', () => {
            loadPano(@js($initialPanorama));
        });
    </script>
</x-filament-panels::page>
