<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Önce temizlik yapalım (Hata almamak için)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Rolleri Oluştur (Türkçe isimlerle ama kodlar İngilizce standart kalsın)
        // name: Kod tarafında kullanacağımız isim
        // guard_name: web (Standart)
        
        $roles = [
            'super_admin',      // Süper Yönetici (Sen)
            'business_owner',   // İşletme Sahibi
            'tourist',          // Turist/Ziyaretçi
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}