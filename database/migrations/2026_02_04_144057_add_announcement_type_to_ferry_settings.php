<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ferry_settings')) {
            Schema::table('ferry_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ferry_settings', 'announcement_type')) {
                    $table->string('announcement_type')->default('info')->after('announcement_text');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('ferry_settings', function (Blueprint $table) {
            $table->dropColumn('announcement_type');
        });
    }
};
