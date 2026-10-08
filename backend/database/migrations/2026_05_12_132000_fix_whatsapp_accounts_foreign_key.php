<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('whats_app_accounts')) {
            Schema::table('whats_app_accounts', function (Blueprint $table) {
                // Drop the invalid foreign key that points to templates instead of portfolios
                try {
                    // Try different possible constraint names just in case
                    $table->dropForeign('waba_portfolio_fk');
                } catch (\Exception $e) {
                    // Ignore if already dropped or name is different
                }

                // Change the column to string so it can store Meta Business IDs directly
                $table->string('whatsapp_business_portfolio_id')->nullable()->change();

                // Make box_id nullable so we can pre-register accounts from webhooks
                // without knowing the CRM box context yet
                $table->unsignedBigInteger('box_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('whats_app_accounts')) {
            Schema::table('whats_app_accounts', function (Blueprint $table) {
                $table->unsignedBigInteger('whatsapp_business_portfolio_id')->nullable()->change();
            });
        }
    }
};
