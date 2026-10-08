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
        Schema::table('salary_earnings', function (Blueprint $table) {
            $table->decimal('da_sum_amount', 12, 2)->default(0)->after('others');
            $table->decimal('hra_sum_amount', 12, 2)->default(0)->after('da_sum_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_earnings', function (Blueprint $table) {
            $table->dropColumn(['da_sum_amount', 'hra_sum_amount']);

        });
    }
};
