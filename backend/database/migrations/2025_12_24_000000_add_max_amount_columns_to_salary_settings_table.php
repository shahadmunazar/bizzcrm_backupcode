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
        Schema::table('salary_settings', function (Blueprint $table) {
            // All fields that can have max amount when percentage is selected
            $maxAmountColumns = [
                // Earnings
                'basic_max', 'da_max', 'hra_max', 'conveyance_max', 'allowance_max', 'medical_allowance_max', 
                'special_allowance_max', 'leave_travel_allowance_max', 'city_compensatory_allowance_max', 
                'sales_target_incentives_max', 'shift_allowance_max', 'transport_allowance_max', 
                'food_allowance_max', 'overtime_allowance_max', 'uniform_allowance_max', 
                'mobile_internet_allowance_max', 'project_allowance_max', 'education_allowance_max', 
                'professional_allowance_max', 'arrears_earnings_max', 'others_max',
                // Reimbursements
                'travel_reimbursement_max', 'fuel_reimbursement_max', 'telephone_reimbursement_max', 
                'driver_reimbursement_max', 'medical_reimbursement_max', 'uniform_reimbursement_max',
                // Contributions
                'employer_pf_max', 'employer_esi_max', 'gratuity_max', 'superannuation_max', 'employer_lwf_max',
                // Deductions
                'employee_pf_max', 'employee_esi_max', 'professional_tax_max', 'tds_max',
                'salary_advance_recovery_max', 'loan_emi_recovery_max', 'canteen_deduction_max', 
                'attendance_loss_max', 'notice_period_recovery_max', 'arrears_recovery_max', 
                'insurance_premium_employee_max',
                // One-Time
                'joining_bonus_max', 'retention_bonus_max', 'performance_bonus_max', 'ex_gratia_max',
                'full_final_settlement_max', 'leave_encashment_max', 'gratuity_payout_max', 
                'severance_compensation_max'
            ];

            foreach ($maxAmountColumns as $column) {
                if (!Schema::hasColumn('salary_settings', $column)) {
                    $table->decimal($column, 12, 2)->default(0)->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $maxAmountColumns = [
                // Earnings
                'basic_max', 'da_max', 'hra_max', 'conveyance_max', 'allowance_max', 'medical_allowance_max', 
                'special_allowance_max', 'leave_travel_allowance_max', 'city_compensatory_allowance_max', 
                'sales_target_incentives_max', 'shift_allowance_max', 'transport_allowance_max', 
                'food_allowance_max', 'overtime_allowance_max', 'uniform_allowance_max', 
                'mobile_internet_allowance_max', 'project_allowance_max', 'education_allowance_max', 
                'professional_allowance_max', 'arrears_earnings_max', 'others_max',
                // Reimbursements
                'travel_reimbursement_max', 'fuel_reimbursement_max', 'telephone_reimbursement_max', 
                'driver_reimbursement_max', 'medical_reimbursement_max', 'uniform_reimbursement_max',
                // Contributions
                'employer_pf_max', 'employer_esi_max', 'gratuity_max', 'superannuation_max', 'employer_lwf_max',
                // Deductions
                'employee_pf_max', 'employee_esi_max', 'professional_tax_max', 'tds_max',
                'salary_advance_recovery_max', 'loan_emi_recovery_max', 'canteen_deduction_max', 
                'attendance_loss_max', 'notice_period_recovery_max', 'arrears_recovery_max', 
                'insurance_premium_employee_max',
                // One-Time
                'joining_bonus_max', 'retention_bonus_max', 'performance_bonus_max', 'ex_gratia_max',
                'full_final_settlement_max', 'leave_encashment_max', 'gratuity_payout_max', 
                'severance_compensation_max'
            ];

            foreach ($maxAmountColumns as $column) {
                if (Schema::hasColumn('salary_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

