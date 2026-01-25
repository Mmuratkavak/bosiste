<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            // Logo ve görsel alanları
            if (!Schema::hasColumn('business_profiles', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('slug');
            }

            // İletişim bilgileri
            if (!Schema::hasColumn('business_profiles', 'phone')) {
                $table->string('phone')->nullable()->after('description');
            }

            if (!Schema::hasColumn('business_profiles', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('business_profiles', 'email')) {
                $table->string('email')->nullable()->after('whatsapp');
            }

            if (!Schema::hasColumn('business_profiles', 'website')) {
                $table->string('website')->nullable()->after('email');
            }

            // Adres bilgileri
            if (!Schema::hasColumn('business_profiles', 'address')) {
                $table->text('address')->nullable()->after('website');
            }

            if (!Schema::hasColumn('business_profiles', 'city')) {
                $table->string('city')->nullable()->after('address');
            }

            if (!Schema::hasColumn('business_profiles', 'district')) {
                $table->string('district')->nullable()->after('city');
            }

            // Sosyal medya linkleri (JSON array)
            if (!Schema::hasColumn('business_profiles', 'social_links')) {
                $table->json('social_links')->nullable()->after('district');
            }

            // Çalışma saatleri (JSON array: {monday: {open: '09:00', close: '18:00'}, ...})
            if (!Schema::hasColumn('business_profiles', 'working_hours')) {
                $table->json('working_hours')->nullable()->after('social_links');
            }

            // Etiketler/Özellikler (JSON array: ['wifi', 'parking', 'pet-friendly'])
            if (!Schema::hasColumn('business_profiles', 'features')) {
                $table->json('features')->nullable()->after('working_hours');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'phone',
                'whatsapp',
                'email',
                'website',
                'address',
                'city',
                'district',
                'social_links',
                'working_hours',
                'features',
            ]);
        });
    }
};
