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
        Schema::table('mail_filters', function (Blueprint $table) {
            $table->string('enable_shifts')->default('No')->after('round_robin_users');
            $table->json('shifts')->nullable()->after('enable_shifts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_filters', function (Blueprint $table) {
            $table->dropColumn(['enable_shifts', 'shifts']);
        });
    }
};
