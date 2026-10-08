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
        Schema::table('payroll_additions', function (Blueprint $table) {
            $table->json('employee_ids')->nullable()->after('assignee_type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_additions', function (Blueprint $table) {
            $table->dropColumn('employee_ids');
        });
    }
};
