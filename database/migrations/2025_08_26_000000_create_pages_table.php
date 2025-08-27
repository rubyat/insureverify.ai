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
            $table->text('short_desc')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('custom_logo')->nullable();
            $table->string('header_style', 100)->nullable();
            $table->unsignedTinyInteger('show_template')->default(1);
            $table->longText('content')->nullable();
            $table->json('template')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status', 'idx_pages_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
