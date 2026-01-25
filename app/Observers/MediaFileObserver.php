<?php

namespace App\Observers;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;

class MediaFileObserver
{
    /**
     * Medya dosyası silinirken sunucudan da sil
     */
    public function deleting(MediaFile $mediaFile): void
    {
        // Ana dosyayı sil
        if ($mediaFile->path) {
            Storage::disk('public')->delete($mediaFile->path);
        }

        // Thumbnail'i sil
        if ($mediaFile->thumbnail_path) {
            Storage::disk('public')->delete($mediaFile->thumbnail_path);
        }

        // WebP versiyonunu sil
        if ($mediaFile->webp_path) {
            Storage::disk('public')->delete($mediaFile->webp_path);
        }
    }
}
