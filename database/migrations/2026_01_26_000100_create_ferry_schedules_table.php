<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ferry_schedules')) {
            return;
        }

        Schema::create('ferry_schedules', function (Blueprint $table) {
            $table->id();

            $table->string('route')->index();
            $table->date('date')->index();
            $table->time('departure_time')->index();

            $table->string('status')->nullable();
            $table->string('vessel_name')->nullable();
            $table->text('note')->nullable();
            $table->text('raw_text')->nullable();

            $table->timestamp('source_updated_at')->nullable();

            $table->timestamps();

            $table->unique(['route', 'date', 'departure_time'], 'ferry_schedules_route_date_time_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ferry_schedules');
    }
};
