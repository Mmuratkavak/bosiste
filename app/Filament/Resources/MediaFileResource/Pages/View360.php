<?php

namespace App\Filament\Resources\MediaFileResource\Pages;

use App\Filament\Resources\MediaFileResource;
use App\Models\MediaFile;
use Filament\Resources\Pages\Page;

class View360 extends Page
{
    protected static string $resource = MediaFileResource::class;

    protected static string $view = 'filament.resources.media-file-resource.pages.view360';

    public MediaFile $record;

    public function mount(MediaFile $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return $this->record->tenant?->name . ' - 360° Görünüm';
    }

    /**
     * Aynı tenant'ın diğer 360 görselleri
     */
    public function getOther360Images()
    {
        return MediaFile::where('tenant_id', $this->record->tenant_id)
            ->where('type', '360')
            ->where('id', '!=', $this->record->id)
            ->orderBy('order')
            ->get();
    }
}
