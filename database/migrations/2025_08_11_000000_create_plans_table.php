<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_plan_id');
            $table->string('anet_plan_id')->nullable();
            // price can be nullable to allow "Custom Pricing" style plans (e.g., Enterprise)
            $table->decimal('price', 10, 2)->nullable();
            // legacy usage limit (kept); can be repurposed or ignored by frontend
            $table->integer('image_limit')->default(0);
            // marketing/display fields for public pricing sections
            $table->text('description')->nullable();
            $table->integer('verifications_included')->nullable();
            $table->json('features')->nullable();
            $table->string('cta_label')->default('Get Started');
            // store a Laravel route name for CTA; e.g., 'plans.index' or 'contact'
            $table->string('cta_route')->default('plans.index');
            $table->integer('sort_order')->default(0);
            // plan visibility in pricing sections or internal only
            $table->enum('visibility', ['Public', 'Private'])->default('Public');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};


