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
        Schema::table('activities', function (Blueprint $table) {
            $table->dateTime('activity_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Reverting to not nullable.
            // Note: This might fail if there are null values, so handle with care in prod.
            // For now, we assume this is correct revert logic.
            $table->dateTime('activity_date')->nullable(false)->change();
        });
    }
};
