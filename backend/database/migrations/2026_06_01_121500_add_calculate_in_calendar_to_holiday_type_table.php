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
        Schema::table('holiday_type', function (Blueprint $table) {
            $table->string('calculate_in_calendar')->default('No')->after('max_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holiday_type', function (Blueprint $table) {
            $table->dropColumn('calculate_in_calendar');
        });
    }
};
