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
        Schema::table('plans', function (Blueprint $table) {
            $table->enum('payment_mode', ['online', 'offline'])->default('online')->after('type');
            $table->enum('plan_type', ['subscription', 'topup'])->default('subscription')->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'payment_mode')) {
                $table->dropColumn('payment_mode');
            }
            if (Schema::hasColumn('plans', 'plan_type')) {
                $table->dropColumn('plan_type');
            }
        });
    }
};
