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
        Schema::table('shifts', function (Blueprint $table) {
            $table->decimal('absent_deduction', 5, 2)->nullable()->after('half_day_below');
            $table->decimal('half_day_deduction', 5, 2)->nullable()->after('absent_deduction');
            $table->decimal('short_leave_deduction', 5, 2)->nullable()->after('half_day_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['absent_deduction', 'half_day_deduction', 'short_leave_deduction']);
        });
    }
};
