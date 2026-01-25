<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaFile extends Model
{
    protected $fillable = [
        'tenant_id',
        'type',
        'path',
        'thumbnail_path',
        'webp_path',
        'order',
        'width',
        'height',
        'size',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'order' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Tenant planına göre maksimum yükleme sayısını döndür
     */
    public static function getUploadLimit(Tenant $tenant, string $type): int
    {
        if ($type === 'logo' || $type === 'cover') {
            return 1;
        }

        if ($type === '360') {
            return $tenant->plan === 'pro' ? 10 : 0;
        }

        // gallery
        return $tenant->plan === 'pro' ? 15 : 3;
    }

    /**
     * Tenant'ın belirli tipteki mevcut dosya sayısı
     */
    public static function getCurrentCount(Tenant $tenant, string $type): int
    {
        return static::where('tenant_id', $tenant->id)
            ->where('type', $type)
            ->count();
    }
}
