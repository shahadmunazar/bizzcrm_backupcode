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
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('holiday_off')->default(false)->after('leave_code');
            $table->string('holiday_off_details')->nullable()->after('holiday_off');
            $table->boolean('weekly_off')->default(false)->after('holiday_off_details');
            $table->string('weekly_off_details')->nullable()->after('weekly_off');
            $table->boolean('overtime_equal_shift')->default(false)->after('weekly_off_details');
            $table->string('overtime_equal_shift_details')->nullable()->after('overtime_equal_shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'holiday_off',
                'holiday_off_details',
                'weekly_off',
                'weekly_off_details',
                'overtime_equal_shift',
                'overtime_equal_shift_details'
            ]);
        });
    }
};
