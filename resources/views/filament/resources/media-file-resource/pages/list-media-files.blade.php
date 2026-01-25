<x-filament-panels::page>
    @php
        $tenant = $this->getCurrentTenant();
        $cover = $tenant ? $this->getCoverImage() : null;
        $logo = $tenant ? $this->getLogoImage() : null;
        $businessName = $this->getBusinessName();
    @endphp

    <div class="space-y-6">
        {{-- Başlık --}}
        <header class="space-y-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Fotoğraf Galerisi</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Restoranınızın kapak, logo ve diğer görsellerini buradan yönetin.
            </p>
        </header>

        {{-- Kapak + Logo alanı (Medya Dosyaları sayfası için) --}}
        @if ($tenant)
            <section>
                <div class="relative rounded-2xl overflow-hidden bg-gray-900" style="height: 220px;">
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

                    <div class="absolute left-8 bottom-6 flex items-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-white/95 backdrop-blur flex items-center justify-center overflow-hidden shadow-lg border-2 border-white/20">
                            @if ($logo)
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo->path) }}"
                                    alt="Logo"
                                    class="w-full h-full object-contain p-1"
                                />
                            @else
                                <span class="text-gray-800 text-2xl font-bold">
                                    {{ mb_substr($businessName, 0, 1) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white drop-shadow-lg">{{ $businessName }}</p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Varsayılan Filament tablo içeriği --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
