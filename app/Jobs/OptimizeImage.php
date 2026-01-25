<?php

namespace App\Jobs;

use App\Models\MediaFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class OptimizeImage implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected string $disk;
    protected string $path;
    protected ?int $mediaFileId;

    public function __construct(string $path, string $disk = 'public', ?int $mediaFileId = null)
    {
        $this->path = $path;
        $this->disk = $disk;
        $this->mediaFileId = $mediaFileId;
    }

    public function handle(): void
    {
        $fullPath = Storage::disk($this->disk)->path($this->path);
        if (! file_exists($fullPath)) {
            return;
        }

        $contents = file_get_contents($fullPath);
        if ($contents === false) {
            return;
        }

        // GD ile optimizasyon ve çoklu boyut oluşturma
        if (function_exists('imagecreatefromstring')) {
            $image = @imagecreatefromstring($contents);
            if ($image !== false) {
                $width = imagesx($image);
                $height = imagesy($image);

                // Orijinal boyutları kaydet
                if ($this->mediaFileId) {
                    MediaFile::where('id', $this->mediaFileId)->update([
                        'width' => $width,
                        'height' => $height,
                        'size' => filesize($fullPath),
                    ]);
                }

                // 1. Ana görsel optimize et - Akıllı boyutlandırma
                $optimized = $this->optimizeMainImage($image, $width, $height);
                if ($optimized) {
                    // Kalite korunarak JPEG kaydet (%92 kalite)
                    imagejpeg($optimized, $fullPath, 92);
                    imagedestroy($optimized);
                }

                // 2. Thumbnail oluştur (400px)
                $thumbnailPath = $this->generateThumbnail($image, $width, $height);

                // 3. WebP versiyonu oluştur (modern tarayıcılar için)
                $webpPath = $this->generateWebP($fullPath);

                // MediaFile kaydını güncelle
                if ($this->mediaFileId && ($thumbnailPath || $webpPath)) {
                    MediaFile::where('id', $this->mediaFileId)->update([
                        'thumbnail_path' => $thumbnailPath,
                        'webp_path' => $webpPath,
                        'size' => filesize($fullPath), // Optimize edilmiş boyut
                    ]);
                }

                imagedestroy($image);
            }
        }
    }

    /**
     * Ana görseli akıllı şekilde optimize et (kalite korunur)
     */
    protected function optimizeMainImage($sourceImage, int $originalWidth, int $originalHeight)
    {
        // Maksimum boyutlar (responsive için)
        $maxWidth = 1920;
        $maxHeight = 1920;

        // Zaten küçükse dokunma
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return $sourceImage;
        }

        // Oran koru
        $ratio = $originalWidth / $originalHeight;
        
        if ($ratio > 1) {
            // Yatay (landscape)
            $newWidth = min($maxWidth, $originalWidth);
            $newHeight = (int) ($newWidth / $ratio);
        } else {
            // Dikey (portrait)
            $newHeight = min($maxHeight, $originalHeight);
            $newWidth = (int) ($newHeight * $ratio);
        }

        // Bicubic interpolation ile yeniden boyutlandır (en kaliteli)
        return imagescale($sourceImage, $newWidth, $newHeight, IMG_BICUBIC);
    }

    /**
     * Thumbnail oluştur (400px, küçük önizlemeler için)
     */
    protected function generateThumbnail($sourceImage, int $originalWidth, int $originalHeight): ?string
    {
        $thumbSize = 400;
        $ratio = $originalWidth / $originalHeight;
        
        if ($ratio > 1) {
            $thumbWidth = $thumbSize;
            $thumbHeight = (int) ($thumbSize / $ratio);
        } else {
            $thumbHeight = $thumbSize;
            $thumbWidth = (int) ($thumbSize * $ratio);
        }

        $thumbnail = imagescale($sourceImage, $thumbWidth, $thumbHeight, IMG_BICUBIC);
        if (!$thumbnail) {
            return null;
        }

        $pathInfo = pathinfo($this->path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
        $fullThumbnailPath = Storage::disk($this->disk)->path($thumbnailPath);

        // Thumbnail için %88 kalite (daha küçük dosya, yeterli kalite)
        imagejpeg($thumbnail, $fullThumbnailPath, 88);
        imagedestroy($thumbnail);

        return $thumbnailPath;
    }

    /**
     * WebP versiyonu oluştur (boyut kazancı: %25-35, kalite kayıpsız)
     */
    protected function generateWebP(string $jpegPath): ?string
    {
        if (!function_exists('imagewebp')) {
            return null;
        }

        $image = @imagecreatefromjpeg($jpegPath);
        if (!$image) {
            return null;
        }

        $pathInfo = pathinfo($this->path);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        $fullWebpPath = Storage::disk($this->disk)->path($webpPath);

        // WebP kalite: 90 (mükemmel kalite, yine de %30 daha küçük)
        imagewebp($image, $fullWebpPath, 90);
        imagedestroy($image);

        return $webpPath;
    }
}
