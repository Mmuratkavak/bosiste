<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tenant_working_hours')) {
            return;
        }

        Schema::create('tenant_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Sezonluk tarihler
            $table->date('season_start_date')->nullable();
            $table->date('season_end_date')->nullable();

            // Haftalık çalışma saatleri (her gün için ayrı alanlar)
            $table->time('monday_open')->nullable();
            $table->time('monday_close')->nullable();
            $table->boolean('monday_closed')->default(false);

            $table->time('tuesday_open')->nullable();
            $table->time('tuesday_close')->nullable();
            $table->boolean('tuesday_closed')->default(false);

            $table->time('wednesday_open')->nullable();
            $table->time('wednesday_close')->nullable();
            $table->boolean('wednesday_closed')->default(false);

            $table->time('thursday_open')->nullable();
            $table->time('thursday_close')->nullable();
            $table->boolean('thursday_closed')->default(false);

            $table->time('friday_open')->nullable();
            $table->time('friday_close')->nullable();
            $table->boolean('friday_closed')->default(false);

            $table->time('saturday_open')->nullable();
            $table->time('saturday_close')->nullable();
            $table->boolean('saturday_closed')->default(false);

            $table->time('sunday_open')->nullable();
            $table->time('sunday_close')->nullable();
            $table->boolean('sunday_closed')->default(false);

            // Özel günler ve kapalı günler JSON olarak tutulacak (ileri kullanım için)
            $table->json('special_days')->nullable();

            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_working_hours');
    }
};
