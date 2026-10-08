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
            $decimalColumns = [
                'amount', 'basic', 'da', 'hra', 'conveyance', 'allowance', 'medical_allowance', 'special_allowance',
                'leave_travel_allowance', 'city_compensatory_allowance', 'sales_target_incentives',
                'shift_allowance', 'transport_allowance', 'food_allowance', 'overtime_allowance',
                'uniform_allowance', 'mobile_internet_allowance', 'project_allowance', 'education_allowance',
                'professional_allowance', 'arrears_earnings', 'others',
                'travel_reimbursement', 'fuel_reimbursement', 'telephone_reimbursement', 'driver_reimbursement',
                'medical_reimbursement', 'uniform_reimbursement',
                'employer_pf', 'employer_esi', 'gratuity', 'superannuation', 'employer_lwf',
                'employee_pf', 'employee_esi', 'professional_tax', 'tds',
                'salary_advance_recovery', 'loan_emi_recovery', 'canteen_deduction', 'attendance_loss',
                'notice_period_recovery', 'arrears_recovery', 'insurance_premium_employee',
                'joining_bonus', 'retention_bonus', 'performance_bonus', 'ex_gratia',
                'full_final_settlement', 'leave_encashment', 'gratuity_payout', 'severance_compensation'
            ];

            $stringColumns = [
                'paid_leave', 'casual_leave', 'sick_leave', 'loss_of_pay', 'holiday_weekly_off',
                'pan_number', 'aadhaar_number', 'pf_uan_number', 'esi_number',
                'bank_name', 'bank_account_number', 'ifsc_code', 'nominee_details', 'tax_regime'
            ];

            $textColumns = [
                'investment_details'
            ];

            foreach ($decimalColumns as $column) {
                if (!Schema::hasColumn('salary_settings', $column)) {
                    $table->decimal($column, 12, 2)->default(0);
                }
            }

            foreach ($stringColumns as $column) {
                if (!Schema::hasColumn('salary_settings', $column)) {
                    $table->string($column)->nullable();
                }
            }

            foreach ($textColumns as $column) {
                if (!Schema::hasColumn('salary_settings', $column)) {
                    $table->text($column)->nullable();
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
            // No easy way to rollback since we check existence, but for completeness:
            // $table->dropColumn([...]);
        });
    }
};
