<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * İşletme Gizli Verileri
 * 
 * Yalnızca işletme sahibinin göreceği özel bilgiler.
 * Gizli telefon numarası, sözleşme dosyaları ve yönetici notları içerir.
 */
class TenantSecret extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',       // İlişkili işletme
        'private_phone',   // Gizli iletişim numarası
        'contract_file',   // Sözleşme dosyası
        'admin_notes',     // Yönetici notları
    ];

    /**
     * İlişkili işletme
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}