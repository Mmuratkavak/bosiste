<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Namaz Vakitleri | Visit Gökçeada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 min-h-screen font-[Inter]">
    <nav class="bg-teal-700 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="font-bold text-xl text-white no-underline">Visit Gökçeada</a>
            <div class="space-x-4 text-sm">
                <a href="/hava-durumu" class="text-white no-underline hover:text-teal-200">Hava Durumu</a>
                <a href="/nobetci-eczaneler" class="text-white no-underline hover:text-teal-200">Eczane</a>
            </div>
        </div>
    </nav>

    <div class="bg-teal-600 text-white py-12 text-center shadow-inner">
        <h1 class="text-4xl font-bold mb-2 uppercase tracking-tight">Gökçeada Namaz Vakitleri</h1>
        <p class="text-teal-100 opacity-90">{{ now()->translatedFormat('d F Y l') }}</p>
    </div>

    <div class="container mx-auto px-4 -mt-10 mb-12">
        @if(isset($prayer))
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach(['imsak' => 'İmsak', 'gunes' => 'Güneş', 'ogle' => 'Öğle', 'ikindi' => 'İkindi', 'aksam' => 'Akşam', 'yatsı' => 'Yatsı'] as $key => $label)
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center border-b-4 {{ $currentVakit == $key ? 'border-orange-500 scale-105 ring-2 ring-orange-200' : 'border-teal-500' }} transition-all">
                    <p class="text-gray-400 font-bold text-xs uppercase mb-2">{{ $label }}</p>
                    <p class="text-3xl font-black text-slate-800 tracking-tighter">{{ $prayer->$key }}</p>
                    @if($currentVakit == $key)
                        <span class="inline-block mt-3 bg-orange-100 text-orange-600 text-[10px] font-extrabold px-2 py-1 rounded-full animate-pulse">ŞU AN</span>
                    @endif
                </div>
            @endforeach
        </div>
        @else
        <div class="bg-white p-12 rounded-2xl shadow text-center">
            <p class="text-xl font-bold text-gray-500">Bugün için vakit bilgisi bulunamadı.</p>
        </div>
        @endif
    </div>

    <footer class="text-center py-8 text-gray-400 text-xs mt-auto">
        &copy; {{ date('Y') }} Visit Gökçeada - Tüm Hakları Saklıdır
    </footer>
</body>
</html>
