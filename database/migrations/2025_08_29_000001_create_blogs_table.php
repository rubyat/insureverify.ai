<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->unsignedTinyInteger('status')->default(1); // 1: published, 0: draft
            $table->longText('content')->nullable();
            $table->json('template')->nullable();
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->string('author')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->json('tags')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status', 'idx_blogs_status');
            $table->index('publish_date', 'idx_blogs_publish_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
