<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_deductions', function (Blueprint $table) {
            $table->decimal('tds_sum_amount', 12, 2)->default(0)->after('tds');
            $table->decimal('esi_sum_amoutn', 12, 2)->default(0)->after('othersdeductions');
            $table->decimal('pf_sum_amount', 12, 2)->default(0)->after('esi_sum_amoutn');
        });
    }

    public function down(): void
    {
        Schema::table('salary_deductions', function (Blueprint $table) {
            $table->dropColumn(['tds_sum_amount', 'esi_sum_amoutn', 'pf_sum_amount']);
        });
    }
};
