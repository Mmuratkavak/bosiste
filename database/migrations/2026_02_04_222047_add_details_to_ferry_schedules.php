<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ferry_schedules', function (Blueprint $table) {
            // Eger 'arrival_time' sütunu yoksa ekle
            if (!Schema::hasColumn('ferry_schedules', 'arrival_time')) {
                $table->string('arrival_time')->nullable()->after('departure_time');
            }
            // Eger 'route' sütunu yoksa ekle
            if (!Schema::hasColumn('ferry_schedules', 'route')) {
                $table->string('route')->default('KABATEPE-GOKCEADA')->after('arrival_time');
            }
        });
    }

    public function down()
    {
        Schema::table('ferry_schedules', function (Blueprint $table) {
            $table->dropColumn(['arrival_time', 'route']);
        });
    }
};
