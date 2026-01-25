<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\TenantScope;

/**
 * Ürün Modeli
 * 
 * İşletmelerin sunduğu ürünleri temsil eder.
 * Her ürün bir kategoriye ve işletmeye (Tenant) aittir.
 * Soft delete ile silinen ürünler kurtarılabilir.
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',     // Ait olduğu işletme
        'category_id',   // Ait olduğu kategori
        'name',          // Ürün adı
        'slug',          // URL dostu adı
        'description',   // Ürün açıklaması
        'price',         // Fiyatı
        'image_path',    // Resim yolu
        'is_active',     // Aktif mi
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Ürünün ait olduğu işletme
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Ürünün ait olduğu kategori
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
