<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nöbetçi Eczaneler | Visit Gökçeada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <nav class="bg-teal-700 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2 font-bold text-lg">
                <span class="bg-white text-teal-700 rounded-full w-8 h-8 flex items-center justify-center">💊</span>
                Visit Gökçeada
            </a>
            <div class="space-x-4 text-sm font-medium">
                <a href="/" class="hover:text-teal-200">Ana Sayfa</a>
                <a href="/feribot-saatleri" class="hover:text-teal-200">Feribot</a>
            </div>
        </div>
    </nav>

    <div class="bg-teal-600 text-white py-10 text-center shadow-md">
        <h1 class="text-3xl md:text-4xl font-bold mb-2">Nöbetçi Eczaneler</h1>
        <p class="text-teal-100">Gökçeada güncel nöbet listesi</p>
    </div>

    <div class="container mx-auto px-4 -mt-6 mb-12 flex-grow z-10 relative">
        @if($pharmacies->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pharmacies as $pharmacy)
                    @php
                        $dateObj = \Carbon\Carbon::parse($pharmacy->duty_date);
                        $tarih = $dateObj->format('d.m.Y');
                        $isToday = $dateObj->isToday();
                        $gunler = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
                        $gunAdi = $gunler[$dateObj->dayOfWeek];
                        $mapsLink = "https://www.google.com/maps/search/?api=1&query=" . urlencode($pharmacy->pharmacy_name . " Eczanesi Gökçeada");
                    @endphp

                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden flex flex-col border border-gray-100">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-gray-700 text-lg">{{ $tarih }}</span>
                                <span class="text-xs text-gray-500 block uppercase font-semibold">{{ $gunAdi }}</span>
                            </div>
                            @if($isToday)
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-200 animate-pulse">
                                    BUGÜN
                                </span>
                            @endif
                        </div>

                        <div class="p-5 flex-grow">
                            <h2 class="text-2xl font-bold text-gray-800 mb-3">{{ $pharmacy->pharmacy_name }}</h2>
                            <div class="flex items-start text-gray-600 mb-4 bg-gray-50 p-3 rounded-lg text-sm">
                                <span class="mr-2">📍</span>
                                <span>{{ $pharmacy->address ?: 'Adres bilgisi girilmemiş.' }}</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mt-auto">
                                @if($pharmacy->phone)
                                    <a href="tel:{{ $pharmacy->phone }}" class="flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-sm">
                                        📞 Ara
                                    </a>
                                @else
                                    <button disabled class="bg-gray-100 text-gray-400 font-bold py-2 px-4 rounded-lg cursor-not-allowed">Tel Yok</button>
                                @endif

                                <a href="{{ $mapsLink }}" target="_blank" class="flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-sm">
                                    🗺️ Yol Tarifi
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white p-8 rounded-xl shadow text-center max-w-md mx-auto mt-4">
                <div class="text-4xl mb-2">📅</div>
                <h3 class="font-bold text-gray-800 text-lg">Liste Güncelleniyor</h3>
                <p class="text-gray-500 text-sm">Veriler kısa süre içinde yüklenecektir.</p>
            </div>
        @endif
    </div>

    <footer class="bg-gray-800 text-gray-400 py-4 text-center text-xs mt-auto">
        <p>© {{ date('Y') }} Visit Gökçeada</p>
    </footer>
</body>
</html>
