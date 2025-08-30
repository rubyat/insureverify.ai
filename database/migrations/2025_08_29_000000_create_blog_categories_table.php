<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->unsignedTinyInteger('status')->default(1); // 1: published, 0: draft
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status', 'idx_blog_categories_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
