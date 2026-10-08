<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_reports', function (Blueprint $table) {
            $table->decimal('total_working_days', 5, 2)->default(0.00)->after('net_salary');
    $table->decimal('total_weekly_offs', 5, 2)->default(0.00)->after('total_working_days');
    $table->decimal('total_holidays', 5, 2)->default(0.00)->after('total_weekly_offs');
    $table->decimal('paid_days', 5, 2)->default(0.00)->after('total_holidays');
    $table->decimal('leave_days', 5, 2)->default(0.00)->after('paid_days');
        });
    }

    public function down(): void
    {
        Schema::table('salary_reports', function (Blueprint $table) {
            $table->dropColumn([
                'total_working_days',
                'total_weekly_offs',
                'total_holidays',
                'paid_days',
                'leave_days',
            ]);
        });
    }
};
