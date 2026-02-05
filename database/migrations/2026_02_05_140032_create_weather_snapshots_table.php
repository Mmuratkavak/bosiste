<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->json('payload'); // n8n'den gelen tüm veri burada duracak
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('weather_snapshots'); }
};
