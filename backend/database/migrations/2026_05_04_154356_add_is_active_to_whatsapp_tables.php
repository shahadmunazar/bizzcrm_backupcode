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
        if (Schema::hasTable('whats_app_accounts')) {
            Schema::table('whats_app_accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('whats_app_accounts', 'is_active')) {
                    $table->boolean('is_active')->default(false)->after('account_status');
                }
            });
        }

        if (Schema::hasTable('whats_app_numbers')) {
            Schema::table('whats_app_numbers', function (Blueprint $table) {
                if (!Schema::hasColumn('whats_app_numbers', 'is_active')) {
                    $table->boolean('is_active')->default(false)->after('status');
                }
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
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasTable('whats_app_numbers')) {
            Schema::table('whats_app_numbers', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
