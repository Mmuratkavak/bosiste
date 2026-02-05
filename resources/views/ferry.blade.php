<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Gökçeada Feribot Saatleri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-[#FFFBF0] flex items-center justify-center min-h-screen p-4 font-sans text-gray-800">

    @php
        $hasAnnouncement = !empty($settings->has_announcement) && $settings->has_announcement;
        $annType = $settings->announcement_type ?? 'info';
    @endphp

    <div x-data="{ 
        showModal: {{ $hasAnnouncement ? 'true' : 'false' }},
        type: '{{ $annType }}'
    }" x-cloak class="w-full max-w-md">

        <div class="bg-[#2D8B96] rounded-t-3xl p-6 text-center text-white shadow-lg relative">
             <div class="text-xs font-bold tracking-[0.2em] opacity-80 mb-1">GESTAŞ</div>
             <h1 class="text-2xl font-bold">{{ $settings->title ?? 'Feribot Saatleri' }}</h1>
             <p class="text-xs mt-1 opacity-90">{{ \Carbon\Carbon::now()->locale('tr')->isoFormat('D MMMM YYYY') }}</p>
        </div>

        <div class="bg-white rounded-b-3xl shadow-xl p-6 relative -top-4 pt-8 pb-8">
            
            @if($hasAnnouncement)
                <div @click="showModal = true"
                     class="mb-6 p-4 rounded-xl border-2 cursor-pointer shadow-sm hover:shadow-md transition"
                     :class="{
                        'bg-red-50 border-[#FF6B6B]': type === 'danger',
                        'bg-green-50 border-green-200': type === 'success',
                        'bg-yellow-50 border-yellow-200': type !== 'danger' && type !== 'success'
                     }">
                    <div class="flex items-center gap-3">
                        <div class="text-3xl" x-text="type === 'danger' ? '🚫' : (type === 'success' ? '✅' : '📢')"></div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 text-sm" 
                                x-text="type === 'danger' ? 'SEFER İPTALLERİ MEVCUT' : (type === 'success' ? 'EK SEFERLER VAR' : 'ÖNEMLİ DUYURU')">
                            </h3>
                            <p class="text-[10px] text-gray-500 mt-1 line-clamp-1">Detaylar için tıklayın...</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="rounded-xl border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-2 bg-gray-50 p-3 border-b border-gray-100">
                    <div class="text-center font-bold text-gray-700 text-sm">Kabatepe <span class="block text-[9px] text-gray-400 font-normal">KALKIŞ</span></div>
                    <div class="text-center font-bold text-gray-700 text-sm">Gökçeada <span class="block text-[9px] text-gray-400 font-normal">KALKIŞ</span></div>
                </div>
                
                @if(isset($schedules) && count($schedules) > 0)
                    @foreach($schedules as $schedule)
                    <div class="grid grid-cols-2 p-3 border-b border-gray-50 hover:bg-[#FFFBF0] transition">
                        <div class="text-center font-mono text-lg font-bold text-gray-700">
                            {{ $schedule->departure_time }}
                        </div>
                         <div class="text-center font-mono text-lg font-bold text-gray-700">
                            {{ $schedule->arrival_time }}
                         </div>
                    </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-gray-400 text-sm">
                        Henüz veri güncellenmedi.
                    </div>
                @endif
            </div>
            
            <a href="https://online.gdu.com.tr" target="_blank" class="block mt-6 bg-[#4FD1C5] hover:bg-[#38B2AC] text-white text-center font-bold py-3 rounded-xl transition shadow-md shadow-teal-100 text-sm">
                🎫 Online Bilet Al
            </a>
        </div>

        <div x-show="showModal" 
             class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
             x-transition:enter="transition ease-out duration-300"
             style="display: none;">
            
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden relative"
                 @click.away="showModal = false">
                
                <div class="p-5 text-center text-white"
                     :class="{
                        'bg-[#FF6B6B]': type === 'danger',
                        'bg-[#2D8B96]': type !== 'danger'
                     }">
                    <div class="text-4xl mb-2" x-text="type === 'danger' ? '🚫' : '📢'"></div>
                    <h3 class="text-lg font-bold" x-text="type === 'danger' ? 'DİKKAT' : 'DUYURU'"></h3>
                </div>

                <div class="p-6 bg-[#FFFBF0] max-h-[60vh] overflow-y-auto">
                    <p class="text-gray-800 whitespace-pre-line text-sm leading-relaxed font-medium">
                        {!! nl2br(e($settings->announcement_text ?? '')) !!}
                    </p>
                </div>

                <div class="p-4 bg-white border-t border-gray-100">
                    <button @click="showModal = false" 
                            class="w-full py-3 rounded-xl text-white font-bold shadow-lg transition active:scale-95"
                            :class="{
                                'bg-[#FF6B6B] hover:bg-[#ff5252]': type === 'danger',
                                'bg-[#2D8B96] hover:bg-[#257a85]': type !== 'danger'
                            }">
                        Tamam, Anlaşıldı
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
