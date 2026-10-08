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
        $earningColumns = [
            'special_allowance', 'leave_travel_allowance', 'city_compensatory_allowance', 
            'sales_target_incentives', 'shift_allowance', 'transport_allowance', 
            'food_allowance', 'overtime_allowance', 'uniform_allowance', 
            'mobile_internet_allowance', 'project_allowance', 'education_allowance', 
            'professional_allowance', 'arrears_earnings'
        ];

        Schema::table('salary_earnings', function (Blueprint $table) use ($earningColumns) {
            foreach ($earningColumns as $column) {
                if (!Schema::hasColumn('salary_earnings', $column)) {
                    $table->decimal($column, 12, 2)->default(0)->after('others');
                }
            }
        });

        $deductionColumns = [
            'salary_advance_recovery', 'loan_emi_recovery', 'canteen_deduction', 
            'attendance_loss', 'notice_period_recovery', 'arrears_recovery', 
            'insurance_premium_employee'
        ];

        Schema::table('salary_deductions', function (Blueprint $table) use ($deductionColumns) {
            foreach ($deductionColumns as $column) {
                if (!Schema::hasColumn('salary_deductions', $column)) {
                    $table->decimal($column, 12, 2)->default(0)->after('othersdeductions');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $earningColumns = [
            'special_allowance', 'leave_travel_allowance', 'city_compensatory_allowance', 
            'sales_target_incentives', 'shift_allowance', 'transport_allowance', 
            'food_allowance', 'overtime_allowance', 'uniform_allowance', 
            'mobile_internet_allowance', 'project_allowance', 'education_allowance', 
            'professional_allowance', 'arrears_earnings'
        ];

        Schema::table('salary_earnings', function (Blueprint $table) use ($earningColumns) {
             $table->dropColumn($earningColumns);
        });

        $deductionColumns = [
            'salary_advance_recovery', 'loan_emi_recovery', 'canteen_deduction', 
            'attendance_loss', 'notice_period_recovery', 'arrears_recovery', 
            'insurance_premium_employee'
        ];

        Schema::table('salary_deductions', function (Blueprint $table) use ($deductionColumns) {
            $table->dropColumn($deductionColumns);
        });
    }
};
