@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-12">
        <h1 class="text-2xl font-extrabold mb-4">Arama sonuçları</h1>

        <form method="GET" action="{{ route('search') }}" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="q" value="{{ old('q', $query) }}" placeholder="İşletme, kategori veya adres arayın..." class="flex-1 border px-4 py-2 rounded" />
                <button class="bg-[#ff6b6b] text-white px-4 py-2 rounded">Ara</button>
            </div>
        </form>

        @if($results->isEmpty())
            <div class="text-gray-600">Sonuç bulunamadı.</div>
        @else
            <div class="grid gap-4">
                @foreach($results as $biz)
                    <div class="p-4 border rounded bg-white shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold">{{ $biz->name }}</h2>
                                <div class="text-sm text-gray-600">{{ $biz->short_description ?? Str::limit($biz->description, 140) }}</div>
                                <div class="text-xs text-gray-500 mt-2">{{ $biz->address ?? ($biz->tenant?->domain ?? '') }}</div>
                            </div>
                            <div class="text-right">
                                <a href="#" class="text-sm text-[#ff6b6b] font-semibold">İncele</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
