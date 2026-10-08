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
        Schema::table('shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('shifts', 'all_in_out_calculation')) {
                $table->boolean('all_in_out_calculation')->default(0)->after('auto_punchout_time');
            }
            if (!Schema::hasColumn('shifts', 'first_in_last_out_calculation')) {
                $table->boolean('first_in_last_out_calculation')->default(0)->after('all_in_out_calculation');
            }
            if (!Schema::hasColumn('shifts', 'auto_punch_out_after_shift_end')) {
                $table->time('auto_punch_out_after_shift_end')->nullable()->after('first_in_last_out_calculation')->comment('Time duration after shift end for auto punch out');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            if (Schema::hasColumn('shifts', 'auto_punch_out_after_shift_end')) {
                $table->dropColumn('auto_punch_out_after_shift_end');
            }
            if (Schema::hasColumn('shifts', 'first_in_last_out_calculation')) {
                $table->dropColumn('first_in_last_out_calculation');
            }
            if (Schema::hasColumn('shifts', 'all_in_out_calculation')) {
                $table->dropColumn('all_in_out_calculation');
            }
        });
    }
};
