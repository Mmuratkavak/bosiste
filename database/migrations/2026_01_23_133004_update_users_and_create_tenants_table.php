<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users Tablosunu Güncelleme
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->unique()->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'is_banned')) {
                $table->boolean('is_banned')->default(false)->after('password');
            }
        });

        // Tenants Tablosunu Oluşturma
        if (!Schema::hasTable('tenants')) {
            Schema::create('tenants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
                $table->string('name')->nullable(); // İşletme adı
                $table->enum('status', ['pending', 'active'])->default('pending');
                $table->enum('plan', ['free', 'pro'])->default('free');
                $table->string('domain')->unique()->nullable();
                $table->json('feature_flags')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar_path', 'is_banned']);
        });
    }
};