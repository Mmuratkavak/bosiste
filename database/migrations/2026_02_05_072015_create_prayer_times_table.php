<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('prayer_times')) {
            Schema::create('prayer_times', function (Blueprint $table) {
                $table->id();
                $table->date('date')->unique();
                $table->string('imsak', 5);
                $table->string('gunes', 5);
                $table->string('ogle', 5);
                $table->string('ikindi', 5);
                $table->string('aksam', 5);
                $table->string('yatsı', 5);
                $table->timestamps();
            });
        }
    }
    public function down() { Schema::dropIfExists('prayer_times'); }
};
