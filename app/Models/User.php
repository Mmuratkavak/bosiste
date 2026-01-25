<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Kullanıcı Modeli
 * 
 * Sistem kullanıcılarını temsil eder.
 * Her kullanıcı birden fazla işletmeye (Tenant) sahip olabilir.
 */
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',         // Kullanıcı adı soyadı
        'email',        // Email adresi
        'phone',        // Telefon numarası
        'password',     // Şifresi
        'avatar',       // Avatar dosya adı
        'is_banned',    // Engelli mi (erişim reddedilecek mi)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_banned' => 'boolean',
    ];

    /**
     * Kullanıcının işletmeleri (Tenant)
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'owner_id');
    }

    /**
     * Admin paneline erişim kontrolü
     * 
     * Engelli değilse ve super_admin veya business_owner rolüne sahipse,
     * veya @visitgokceada.com email kullanıyorsa erişim izni verilir.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->is_banned) {
            return false;
        }
        return $this->hasRole(['super_admin', 'business_owner']) || str_ends_with($this->email, '@visitgokceada.com');        
    }
}