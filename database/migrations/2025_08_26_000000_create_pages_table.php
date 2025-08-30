<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->unsignedTinyInteger('status')->default(1); // 1: published, 0: draft
            $table->boolean('is_home')->default(false);
            $table->longText('content')->nullable();
            $table->json('template')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status', 'idx_pages_status');
            $table->index('is_home', 'idx_pages_is_home');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

