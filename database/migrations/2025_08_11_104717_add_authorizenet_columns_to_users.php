<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'anet_customer_profile_id')) {
                $table->string('anet_customer_profile_id')->nullable()->index();
            }
            if (! Schema::hasColumn('users', 'anet_customer_payment_profile_id')) {
                $table->string('anet_customer_payment_profile_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'anet_customer_profile_id')) {
                $table->dropColumn('anet_customer_profile_id');
            }
            if (Schema::hasColumn('users', 'anet_customer_payment_profile_id')) {
                $table->dropColumn('anet_customer_payment_profile_id');
            }
        });
    }
};


