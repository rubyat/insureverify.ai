<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('object_id');
            $table->string('object_model');
            $table->string('seo_title')->nullable();
            $table->boolean('seo_index')->default(true);
            $table->text('seo_keyword')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_image')->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['object_model', 'object_id'], 'idx_seo_object');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo');
    }
};
