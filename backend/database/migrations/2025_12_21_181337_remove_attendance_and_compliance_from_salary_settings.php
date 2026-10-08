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
            $columnsToDrop = [
                'paid_leave', 'casual_leave', 'sick_leave', 'loss_of_pay', 'holiday_weekly_off',
                'pan_number', 'aadhaar_number', 'pf_uan_number', 'esi_number',
                'bank_name', 'bank_account_number', 'ifsc_code', 'nominee_details', 'tax_regime',
                'investment_details'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('salary_settings', $column)) {
                    $table->dropColumn($column);
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
            // Restore string columns
            $stringColumns = [
                'paid_leave', 'casual_leave', 'sick_leave', 'loss_of_pay', 'holiday_weekly_off',
                'pan_number', 'aadhaar_number', 'pf_uan_number', 'esi_number',
                'bank_name', 'bank_account_number', 'ifsc_code', 'nominee_details', 'tax_regime'
            ];
            foreach ($stringColumns as $column) {
                $table->string($column)->nullable();
            }

            // Restore text column
            $table->text('investment_details')->nullable();
        });
    }
};
