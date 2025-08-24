<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['trialing','active','past_due','paused','canceled','incomplete'])->index();
            $table->timestamp('trial_ends_at')->nullable();

            $table->dateTime('current_period_start');
            $table->dateTime('current_period_end');
            $table->timestamp('renews_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->boolean('cancel_at_period_end')->default(false);

            $table->string('currency', 3)->default('USD');
            $table->integer('price_monthly_cents');
            $table->integer('included_verifications')->nullable();
            $table->integer('overage_price_per_unit_cents')->nullable();

            $table->string('provider')->nullable();
            $table->string('provider_customer_id')->nullable();
            $table->string('provider_subscription_id')->nullable()->unique();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'current_period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
