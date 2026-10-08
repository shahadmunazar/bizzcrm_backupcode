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
        Schema::table('boxes', function (Blueprint $table) {
            $table->enum('leave_credit_mode', ['last_day_of_every_month', 'start_day_of_every_month', 'specific_number'])->nullable()->after('day_end_time');
            $table->string('attendace_module_date')->nullable()->after('leave_credit_mode');
            $table->string('leave_credit_time')->nullable()->after('attendace_module_date');
            $table->integer('start_day_of_every_month')->nullable()->after('leave_credit_time');
            $table->integer('last_day_of_every_month')->nullable()->after('start_day_of_every_month');
            $table->string('leave_credit_selected_days')->nullable()->after('last_day_of_every_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn([
                'leave_credit_mode',
                'attendace_module_date',
                'leave_credit_time',
                'start_day_of_every_month',
                'last_day_of_every_month',
                'leave_credit_selected_days'
            ]);
        });
    }
};
