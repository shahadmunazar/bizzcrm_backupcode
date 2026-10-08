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
        Schema::table('boxes', function (Blueprint $table) {
            if (!Schema::hasColumn('boxes', 'whatsapp_business_portfolio_id')) {
                $table->string('whatsapp_business_portfolio_id')->nullable()->after('whatsapp_phone_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn('whatsapp_business_portfolio_id');
        });
    }
};
