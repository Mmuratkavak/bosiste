<x-filament-panels::page>
    @php
        $cover = $this->getCoverImage();
        $logo = $this->getLogoImage();
        $galleryImages = $this->getGalleryImages();
        $images360 = $this->get360Images();
        $businessName = $this->record->tenant?->businessProfile?->name
            ?? $this->record->tenant?->name
            ?? 'İşletme';
    @endphp

    <div class="space-y-6">
        {{-- Başlık --}}
        <header class="space-y-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Fotoğraf Galerisi</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Restoranınızın kapak, logo ve diğer görsellerini buradan yönetin.
            </p>
        </header>

        {{-- Kapak + Logo birleşik alan --}}
        <section>
            <div class="relative rounded-2xl overflow-hidden bg-gray-900" style="height: 240px;">
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

                {{-- Logo + İşletme Adı --}}
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
                    <div class="space-y-1">
                        <p class="text-2xl font-bold text-white drop-shadow-lg">{{ $businessName }}</p>
                        @if ($logo)
                            <p class="text-xs text-gray-200/80">Logo görseli</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Aksiyon Butonları --}}
        <section class="flex items-center justify-end gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('filament.admin.resources.media-files.index') }}"
                    class="px-4 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition shadow-sm"
                >
                    Seçimi Sil
                </a>
                <a
                    href="{{ route('filament.admin.resources.media-files.create', ['tenant_id' => $this->record->tenant_id]) }}"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition shadow-sm flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Fotoğraf Yükle
                </a>
            </div>
        </section>

        {{-- Galeri Grid --}}
        <section class="space-y-4">
            @if ($galleryImages->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
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
                <div class="text-center py-12">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bu işletmeye ait galeri görseli henüz eklenmemiş.</p>
                </div>
            @endif
        </section>

        {{-- 360° Görseller --}}
        <section class="space-y-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">360° Fotoğraflar</h2>

            @if ($images360->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach ($images360 as $image)
                        <a
                            href="{{ route('filament.admin.resources.media-files.view-360', $image) }}"
                            target="_blank"
                            class="group block relative rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition aspect-square"
                        >
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                                alt="360 Görsel"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="absolute bottom-3 left-3 right-3 text-white">
                                    <p class="text-xs font-medium">360° Görsel</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12" x-show="activeFilter === '360'">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bu işletmeye ait 360° fotoğraf henüz eklenmemiş.</p>
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
