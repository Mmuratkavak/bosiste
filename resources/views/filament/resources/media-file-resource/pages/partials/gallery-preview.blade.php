@php
    $tenant = $record->tenant;

    $cover = \App\Models\MediaFile::where('tenant_id', $record->tenant_id)
        ->where('type', 'cover')
        ->latest('created_at')
        ->first();

    $logo = \App\Models\MediaFile::where('tenant_id', $record->tenant_id)
        ->where('type', 'logo')
        ->latest('created_at')
        ->first();

    $galleryImages = \App\Models\MediaFile::where('tenant_id', $record->tenant_id)
        ->where('type', 'gallery')
        ->orderBy('order')
        ->orderBy('created_at')
        ->get();

    $businessName = $tenant?->businessProfile?->name
        ?? $tenant?->name
        ?? 'İşletme';
@endphp

<div class="space-y-4">
    {{-- Kapak + Logo alanı (blur arka plan) --}}
    <div class="relative rounded-2xl overflow-hidden bg-gray-900" style="height: 200px;">
        @if ($cover)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cover->path) }}"
                alt="Kapak Fotoğrafı"
                class="w-full h-full object-cover filter blur-sm scale-110"
            />
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900"></div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent"></div>

        <div class="absolute left-4 bottom-4 flex items-center gap-3">
            <div class="w-14 h-14 rounded-full bg-white/95 backdrop-blur flex items-center justify-center overflow-hidden shadow-lg border-2 border-white/20">
                @if ($logo)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo->path) }}"
                        alt="Logo"
                        class="w-full h-full object-contain p-1"
                    />
                @else
                    <span class="text-gray-800 text-xl font-bold">
                        {{ mb_substr($businessName, 0, 1) }}
                    </span>
                @endif
            </div>
            <div>
                <p class="text-lg font-bold text-white drop-shadow-lg">{{ $businessName }}</p>
            </div>
        </div>
    </div>

    {{-- Küçük galeri ızgarası --}}
    @if ($galleryImages->count())
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
            @foreach ($galleryImages as $image)
                <div class="relative rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition group aspect-square">
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                        alt="Galeri Görseli"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    />
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-sm text-gray-500">
            Bu işletmeye ait galeri görseli bulunamadı.
        </div>
    @endif
</div>
