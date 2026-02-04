<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('weather_forecasts')) {
            Schema::create('weather_forecasts', function (Blueprint $table) {
                $table->id();
                $table->string('location');
                $table->dateTime('forecast_time')->nullable();
                $table->string('formatted_date')->nullable();
                $table->string('time_label')->nullable();
                $table->decimal('temperature_c', 5, 2)->nullable();
                $table->decimal('wind_speed_kmh', 5, 2)->nullable();
                $table->string('wind_direction')->nullable();
                $table->decimal('rain_mm', 6, 2)->nullable();
                $table->unsignedTinyInteger('humidity')->nullable();
                $table->integer('pressure_hpa')->nullable();
                $table->string('model')->nullable();
                $table->json('raw_payload')->nullable();
                $table->timestamps();

                $table->unique(['location', 'model']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_forecasts');
    }
};
