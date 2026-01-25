<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Veritabanını test verileriyle doldur
     */
    public function run(): void
    {
        // Rolleri oluştur
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'business_owner', 'guard_name' => 'web']);

        // Test kullanıcısı oluştur
        $user = User::factory()->create([
            'name' => 'Test Kullanıcı',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Kullanıcıya super_admin rolü ata
        $user->assignRole('super_admin');

        // Test işletmesi oluştur
        $tenant = $user->tenants()->create([
            'name' => 'VisitGökçeada',
            'status' => 'active',
            'plan' => 'pro',
        ]);

        // Test kategorileri oluştur
        $tenant->categories()->createMany([
            ['name' => 'Tur Paketleri', 'slug' => 'tur-paketleri', 'user_id' => $user->id, 'is_active' => true],
            ['name' => 'Konaklama', 'slug' => 'konaklama', 'user_id' => $user->id, 'is_active' => true],
            ['name' => 'Yiyecek & İçecek', 'slug' => 'yiyecek-icecek', 'user_id' => $user->id, 'is_active' => true],
        ]);
    }
}
