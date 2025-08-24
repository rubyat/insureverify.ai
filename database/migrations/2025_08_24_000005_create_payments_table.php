<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('provider_payment_intent_id')->nullable()->index();
            $table->enum('status', ['requires_action','processing','succeeded','failed','canceled'])->index();
            $table->integer('amount_cents');
            $table->string('currency', 3)->default('USD');
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
