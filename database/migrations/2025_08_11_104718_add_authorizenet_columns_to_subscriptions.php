<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'anet_profile_id')) {
                $table->string('anet_profile_id')->nullable()->after('stripe_price');
            }
            if (! Schema::hasColumn('subscriptions', 'anet_payment_profile_id')) {
                $table->string('anet_payment_profile_id')->nullable()->after('anet_profile_id');
            }
            if (! Schema::hasColumn('subscriptions', 'anet_subscription_id')) {
                $table->string('anet_subscription_id')->nullable()->unique()->after('anet_payment_profile_id');
            }
            if (! Schema::hasColumn('subscriptions', 'anet_status')) {
                $table->string('anet_status')->nullable()->after('anet_subscription_id');
            }
            if (! Schema::hasColumn('subscriptions', 'renews_at')) {
                $table->timestamp('renews_at')->nullable()->after('anet_status');
            }
            if (! Schema::hasColumn('subscriptions', 'last_renewed_at')) {
                $table->timestamp('last_renewed_at')->nullable()->after('renews_at');
            }
            if (! Schema::hasColumn('subscriptions', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            foreach (['anet_profile_id','anet_payment_profile_id','anet_subscription_id','anet_status','renews_at','last_renewed_at','plan_id'] as $col) {
                if (Schema::hasColumn('subscriptions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};


