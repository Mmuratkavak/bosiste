<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('weather_forecasts')) {
            Schema::table('weather_forecasts', function (Blueprint $table) {
                // Eski benzersiz indeks: location + model
                try {
                    $table->dropUnique(['location', 'model']);
                } catch (\Throwable $e) {
                    // Index zaten yoksa sessizce yoksay
                }

                // Yeni benzersiz indeks: location + model + forecast_time (saatlik kayıtlar için)
                $table->unique(['location', 'model', 'forecast_time']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('weather_forecasts')) {
            Schema::table('weather_forecasts', function (Blueprint $table) {
                try {
                    $table->dropUnique(['location', 'model', 'forecast_time']);
                } catch (\Throwable $e) {
                    // Index zaten yoksa sessizce yoksay
                }

                $table->unique(['location', 'model']);
            });
        }
    }
};
