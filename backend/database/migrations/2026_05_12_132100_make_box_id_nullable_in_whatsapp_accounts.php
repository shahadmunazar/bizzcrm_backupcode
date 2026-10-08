<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('whats_app_accounts')) {
            Schema::table('whats_app_accounts', function (Blueprint $table) {
                $table->unsignedBigInteger('box_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('whats_app_accounts')) {
            Schema::table('whats_app_accounts', function (Blueprint $table) {
                $table->unsignedBigInteger('box_id')->nullable(false)->change();
            });
        }
    }
};
