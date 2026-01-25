<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('business_profiles', 'neighborhood')) {
                $table->string('neighborhood')->nullable()->after('district');
            }

            if (!Schema::hasColumn('business_profiles', 'street')) {
                $table->string('street')->nullable()->after('neighborhood');
            }

            if (!Schema::hasColumn('business_profiles', 'building_number')) {
                $table->string('building_number', 20)->nullable()->after('street');
            }

            if (!Schema::hasColumn('business_profiles', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('building_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'neighborhood',
                'street',
                'building_number',
                'postal_code',
            ]);
        });
    }
};
