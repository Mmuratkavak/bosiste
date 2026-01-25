<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('business_profiles', 'short_description')) {
                $table->string('short_description')->nullable()->after('description');
            }

            if (!Schema::hasColumn('business_profiles', 'cover_image_path')) {
                $table->string('cover_image_path')->nullable()->after('short_description');
            }

            if (!Schema::hasColumn('business_profiles', 'gallery')) {
                $table->json('gallery')->nullable()->after('cover_image_path');
            }

            if (!Schema::hasColumn('business_profiles', 'google_place_id')) {
                $table->string('google_place_id')->nullable()->after('gallery');
            }

            if (!Schema::hasColumn('business_profiles', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->nullable()->after('google_place_id');
            }

            if (!Schema::hasColumn('business_profiles', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('avg_rating');
            }

            if (!Schema::hasColumn('business_profiles', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('last_synced_at');
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'short_description',
                'cover_image_path',
                'gallery',
                'google_place_id',
                'avg_rating',
                'last_synced_at',
                'latitude',
                'longitude',
            ]);
        });
    }
};
