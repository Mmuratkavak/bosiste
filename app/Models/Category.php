<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\TenantScope;

/**
 * Kategori Modeli
 * 
 * Ürünleri kategorize etmek için kullanılır.
 * Her kategori bir işletmeye (Tenant) aittir.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',   // Ait olduğu işletme
        'name',        // Kategori adı
        'slug',        // URL dostu adı
        'is_active',   // Aktif mi
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Kategorinin ait olduğu işletme
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Kategorinin ürünleri
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
