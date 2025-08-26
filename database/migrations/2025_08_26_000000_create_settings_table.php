<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // Human readable label
            $table->string('code'); // Grouping code (tab), e.g., general, appearance
            $table->string('key')->unique(); // Unique setting key, e.g., site_title
            $table->longText('value')->nullable(); // Can store text/number/json serialized
            $table->integer('sort_order')->default(0);
            $table->enum('type', ['input', 'textarea', 'file', 'ck_editor']);
            $table->json('meta')->nullable(); // optional extra info (placeholder, help, accept, etc)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
