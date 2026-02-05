<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Sefer Saatleri
        if (!Schema::hasTable('ferry_schedules')) {
            Schema::create('ferry_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('route'); 
                $table->time('departure_time'); 
                $table->timestamps();
            });
        }

        // Ayarlar ve Duyurular
        if (!Schema::hasTable('ferry_settings')) {
            Schema::create('ferry_settings', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable(); 
                $table->boolean('has_announcement')->default(false); 
                $table->text('announcement_text')->nullable(); 
                $table->timestamp('last_updated_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ferry_schedules');
        Schema::dropIfExists('ferry_settings');
    }
};
