<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->enum('status', ['draft','open','paid','void','uncollectible'])->default('draft')->index();
            $table->string('currency', 3)->default('USD');

            $table->integer('subtotal_cents');
            $table->integer('discount_cents')->default(0);
            $table->integer('tax_cents')->default(0);
            $table->integer('total_cents');

            $table->dateTime('period_start');
            $table->dateTime('period_end');

            $table->string('provider')->nullable();
            $table->string('provider_invoice_id')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['subscription_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
