<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. İşletme Vitrin Profili (Public)
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('location')->nullable(); // Enlem/Boylam
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // 2. İşletme Gizli Verileri (Private)
        Schema::create('tenant_secrets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('private_phone')->nullable();
            $table->string('contract_file')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_secrets');
        Schema::dropIfExists('business_profiles');
    }
};