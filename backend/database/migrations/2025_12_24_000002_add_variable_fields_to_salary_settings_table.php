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
            if (!Schema::hasColumn('salary_settings', 'variable_amount')) {
                $table->decimal('variable_amount', 12, 2)->default(0)->nullable()->after('severance_compensation');
            }
            if (!Schema::hasColumn('salary_settings', 'variable_amount_type')) {
                $table->string('variable_amount_type', 20)->default('fixed')->nullable()->after('variable_amount');
            }
            if (!Schema::hasColumn('salary_settings', 'variable_amount_max')) {
                $table->decimal('variable_amount_max', 12, 2)->default(0)->nullable()->after('variable_amount_type');
            }
            if (!Schema::hasColumn('salary_settings', 'variable_label')) {
                $table->string('variable_label')->nullable()->after('variable_amount_max');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            if (Schema::hasColumn('salary_settings', 'variable_label')) {
                $table->dropColumn('variable_label');
            }
            if (Schema::hasColumn('salary_settings', 'variable_amount_max')) {
                $table->dropColumn('variable_amount_max');
            }
            if (Schema::hasColumn('salary_settings', 'variable_amount_type')) {
                $table->dropColumn('variable_amount_type');
            }
            if (Schema::hasColumn('salary_settings', 'variable_amount')) {
                $table->dropColumn('variable_amount');
            }
        });
    }
};
