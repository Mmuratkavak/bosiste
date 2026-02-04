<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ferry_announcements')) {
            return;
        }

        Schema::create('ferry_announcements', function (Blueprint $table) {
            $table->id();

            $table->string('route')->nullable()->index();
            $table->string('title')->nullable();
            $table->text('body');

            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('source_updated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ferry_announcements');
    }
};
