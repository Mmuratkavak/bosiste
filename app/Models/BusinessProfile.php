<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Jobs\OptimizeImage;
use App\Services\MediaService;
use Illuminate\Support\Facades\Storage;

/**
 * İşletme Vitrini Profili
 * 
 * Herkese açık olan işletme bilgileri.
 * Müşterilerin göreceği ad, açıklama, konum ve logo gibi bilgileri içerir.
 */
class BusinessProfile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'tenant_id',          // İlişkili işletme
        'name',               // İşletme adı
        'slug',               // URL'de kullanılan benzersiz ad
        'description',        // İşletme hakkında açıklama
        'short_description',  // Kısa özet
        'cover_image_path',   // Kapak resmi
        'gallery',            // Galeri resimleri (JSON)
        'google_place_id',    // Google Place ID
        'avg_rating',         // Ortalama puan
        'last_synced_at',     // Google ile son senkronizasyon
        'latitude',
        'longitude',
        'logo_path',          // Logo dosya yolu
        // Kategoriler
        'category',           // Üst kategori (tek seçim)
        'subcategories',      // Alt kategoriler (çoklu)
        // İletişim bilgileri
        'phone',
        'whatsapp',
        'email',
        'website',
        // Adres bilgileri
        'address',
        'city',
        'district',
        'neighborhood',     // Mahalle (ör: Cumhuriyet Mahallesi)
        'street',           // Cadde / Sokak
        'building_number',  // Bina / Kapı No
        'postal_code',      // Posta kodu
        // JSON alanlar
        'social_links',
        'working_hours',
        'features',
    ];

    protected $casts = [
        'location' => 'array',
        'gallery' => 'array',
        'avg_rating' => 'decimal:2',
        'last_synced_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'social_links' => 'array',
        'working_hours' => 'array',
        'features' => 'array',
        'subcategories' => 'array',
    ];

    protected $attributes = [
        'gallery' => '[]',
    ];

    /**
     * İlişkili işletme
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Gallery accessor - her zaman array döndür
     */
    public function getGalleryAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Logo path accessor - her zaman string veya null döndür
     */
    public function getLogoPathAttribute($value)
    {
        if (is_array($value)) {
            return isset($value[0]) ? $value[0] : null;
        }

        return $value;
    }

    /**
     * Cover image path accessor - her zaman string veya null döndür
     */
    public function getCoverImagePathAttribute($value)
    {
        if (is_array($value)) {
            return isset($value[0]) ? $value[0] : null;
        }

        return $value;
    }

    protected static function booted(): void
    {
        static::saved(function (self $model) {
            // If cover uploaded or changed, dispatch optimize job
            if ($model->wasChanged('cover_image_path') && $model->cover_image_path) {
                dispatch(new OptimizeImage($model->cover_image_path, 'public'));
            }

            // If gallery changed, handle uploaded paths and google photo_references
            if ($model->wasChanged('gallery')) {
                $gallery = $model->gallery ?? [];
                $mediaService = app(MediaService::class);

                foreach ($gallery as $item) {
                    // If item is a string path saved by Filament
                    if (is_string($item)) {
                        if (Storage::disk('public')->exists($item)) {
                            dispatch(new OptimizeImage($item, 'public'));
                        }
                        continue;
                    }

                    // If item is an array (imported photo_reference)
                    if (is_array($item) && isset($item['photo_reference'])) {
                        $photoRef = $item['photo_reference'];
                        $path = $mediaService->downloadGooglePhoto($photoRef, $model->tenant_id);
                        if ($path) {
                            // replace photo_reference with stored path
                            $galleryPathItem = $path;
                            dispatch(new OptimizeImage($path, 'public'));
                        }
                    }
                }

                // If we replaced any photo_references by local paths, save them
                $stringItems = array_filter($gallery, fn($g) => is_string($g));
                if (count($stringItems) > 0) {
                    $model->gallery = array_values($gallery);
                    $model->saveQuietly();
                }
            }
        });
    }
}