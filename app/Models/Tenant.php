<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * İşletme Modeli (Tenant)
 * 
 * Çok kiracılı yapının temel birimi.
 * Her işletme bir kullanıcıya ait ve kendi verileri izole edilmiş.
 * Hem herkese açık profil (BusinessProfile) hem de gizli veriler (TenantSecret) içerir.
 */
class Tenant extends Model
{
    use HasFactory;

    /**
     * Current resolved tenant instance (set by middleware)
     *
     * @var ?\App\Models\Tenant
     */
    protected static $current = null;

    protected $fillable = [
        'owner_id',      // İşletmeyi yöneten kullanıcı
        'status',        // Durum: pending (onay bekleniyor) veya active (aktif)
        'plan',          // Paket: free (ücretsiz) veya pro
        'domain',        // Özel domain adı
        'feature_flags', // JSON formatında özellik bayrakları
    ];

    protected $casts = [
        'feature_flags' => 'array',
    ];
    
    protected $attributes = [
        'feature_flags' => '[]',
        'status' => 'pending',
        'plan' => 'free',
    ];

    /**
     * Tenant name accessor (BusinessProfile'dan alır)
     */
    public function getNameAttribute(): string
    {
        return $this->businessProfile?->name ?? 'İsimsiz İşletme #' . $this->id;
    }

    /**
     * İşletmeyi yöneten kullanıcı
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * İşletme vitrin profili (herkese açık)
     */
    public function businessProfile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /**
     * İşletme gizli verileri (sadece sahibi görür)
     */
    public function tenantSecret(): HasOne
    {
        return $this->hasOne(TenantSecret::class);
    }

    /**
     * İşletmenin kategorileri
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * İşletmenin ürünleri
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * İşletmenin medya dosyaları
     */
    public function mediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class);
    }

    /**
     * İşletmenin çalışma saatleri
     */
    public function workingHours(): HasOne
    {
        return $this->hasOne(TenantWorkingHour::class);
    }

    /**
     * Set the current tenant instance resolved for this request.
     */
    public static function setCurrent(?self $tenant): void
    {
        static::$current = $tenant;
    }

    /**
     * Get the current tenant instance.
     */
    public static function getCurrent(): ?self
    {
        return static::$current;
    }
}