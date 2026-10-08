<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->unsignedBigInteger('salary_payroll_deduction_id')->nullable()->after('box_id');
            $table->unsignedBigInteger('salary_payroll_overtime_id')->nullable()->after('salary_payroll_deduction_id');
            $table->unsignedBigInteger('salary_payroll_addition_id')->nullable()->after('salary_payroll_overtime_id');

            // Add foreign keys (assuming related table names)
            $table->foreign('salary_payroll_deduction_id')
                ->references('id')->on('payroll_deductions')
                ->onDelete('set null');

            $table->foreign('salary_payroll_overtime_id')
                ->references('id')->on('payroll_overtimes')
                ->onDelete('set null');

            $table->foreign('salary_payroll_addition_id')
                ->references('id')->on('payroll_additions')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropForeign(['salary_payroll_deduction_id']);
            $table->dropForeign(['salary_payroll_overtime_id']);
            $table->dropForeign(['salary_payroll_addition_id']);
            $table->dropColumn(['salary_payroll_deduction_id', 'salary_payroll_overtime_id', 'salary_payroll_addition_id']);
        });
    }

};
