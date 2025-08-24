<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('metric');
            $table->unsignedInteger('used')->default(0);
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->timestamp('last_incremented_at')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id','metric','period_start','period_end'], 'subscription_usage_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usages');
    }
};
