<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('business_profiles', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
            if (!Schema::hasColumn('business_profiles', 'subcategories')) {
                $table->json('subcategories')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['category', 'subcategories']);
        });
    }
};
