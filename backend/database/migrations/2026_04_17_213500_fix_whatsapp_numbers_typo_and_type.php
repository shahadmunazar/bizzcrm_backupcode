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
        // Fix Enums in whats_app_numbers (formerly whatsapp_phone_numbers)
        if (Schema::hasTable('whats_app_numbers')) {
            Schema::table('whats_app_numbers', function (Blueprint $table) {
                // Change enum to string to handle new statuses like 'EXPIRED'
                $table->string('status')->nullable()->change();
                $table->string('verfification_code')->nullable()->change();
                $table->string('quality_rating')->nullable()->change();
            });
            
            // Fix the typo if it exists
            if (Schema::hasColumn('whats_app_numbers', 'verfification_code')) {
                Schema::table('whats_app_numbers', function (Blueprint $table) {
                    $table->renameColumn('verfification_code', 'verification_status');
                });
            }
        }

        // Fix Enums in whats_app_accounts (formerly whatsapp_business_accounts)
        if (Schema::hasTable('whats_app_accounts')) {
            Schema::table('whats_app_accounts', function (Blueprint $table) {
                $table->string('account_status')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('whats_app_numbers')) {
            if (Schema::hasColumn('whats_app_numbers', 'verification_status')) {
                Schema::table('whats_app_numbers', function (Blueprint $table) {
                    $table->renameColumn('verification_status', 'verfification_code');
                });
            }
        }
    }
};
