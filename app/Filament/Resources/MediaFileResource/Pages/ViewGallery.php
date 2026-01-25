<?php

namespace App\Filament\Resources\MediaFileResource\Pages;

use App\Filament\Resources\MediaFileResource;
use App\Models\MediaFile;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ViewGallery extends Page
{
    protected static string $resource = MediaFileResource::class;

    protected static string $view = 'filament.resources.media-file-resource.pages.view-gallery';

    public MediaFile $record;

    public function mount(MediaFile $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return ($this->record->tenant?->name ?? 'Galeri') . ' - Medya Önizleme';
    }

    /** Kapak fotoğrafı */
    public function getCoverImage(): ?MediaFile
    {
        return MediaFile::where('tenant_id', $this->record->tenant_id)
            ->where('type', 'cover')
            ->latest('created_at')
            ->first();
    }

    /** Logo */
    public function getLogoImage(): ?MediaFile
    {
        return MediaFile::where('tenant_id', $this->record->tenant_id)
            ->where('type', 'logo')
            ->latest('created_at')
            ->first();
    }

    /** Aynı tenant'a ait tüm galeri görselleri */
    public function getGalleryImages()
    {
        return MediaFile::where('tenant_id', $this->record->tenant_id)
            ->where('type', 'gallery')
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();
    }

    /** Slayt için sadeleştirilmiş galeri verisi */
    public function getGalleryData(): array
    {
        return $this->getGalleryImages()->map(function (MediaFile $image, int $index) {
            return [
                'id' => $image->id,
                'url' => Storage::disk('public')->url($image->path),
                'label' => 'Görsel ' . ($index + 1),
            ];
        })->values()->all();
    }

    /** 360° görüntüler */
    public function get360Images()
    {
        return MediaFile::where('tenant_id', $this->record->tenant_id)
            ->where('type', '360')
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();
    }
}
