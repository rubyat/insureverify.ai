<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('type');
            // Stripe columns retained for backward compatibility
            $table->string('stripe_id')->unique()->nullable();
            $table->string('stripe_status')->nullable();
            $table->string('stripe_price')->nullable();
            // Authorize.Net subscription fields
            $table->string('anet_profile_id')->nullable();
            $table->string('anet_payment_profile_id')->nullable();
            $table->string('anet_subscription_id')->nullable()->unique();
            $table->string('anet_status')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamp('last_renewed_at')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'stripe_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
