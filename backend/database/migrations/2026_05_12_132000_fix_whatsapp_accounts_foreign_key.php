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
            // Drop the foreign key on whatsapp_business_portfolio_id only if it actually exists in the database
            try {
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                      AND TABLE_NAME = 'whats_app_accounts' 
                      AND (CONSTRAINT_NAME = 'waba_portfolio_fk' OR COLUMN_NAME = 'whatsapp_business_portfolio_id')
                      AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                foreach ($foreignKeys as $fk) {
                    try {
                        DB::statement("ALTER TABLE `whats_app_accounts` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    } catch (\Throwable $e) {
                        // Ignore if already dropped or cannot be dropped
                    }
                }
            } catch (\Throwable $e) {
                // Ignore check failure
            }

            Schema::table('whats_app_accounts', function (Blueprint $table) {
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
