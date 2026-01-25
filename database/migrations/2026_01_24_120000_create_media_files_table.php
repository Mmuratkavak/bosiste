<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['logo', 'cover', 'gallery', '360'])->default('gallery');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('webp_path')->nullable();
            $table->integer('order')->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('size')->nullable(); // bytes
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
