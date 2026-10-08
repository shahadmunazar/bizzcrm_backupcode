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
            if (Schema::hasColumn('shifts', 'auto_punch_out_after_shift_end')) {
                $table->dropColumn('auto_punch_out_after_shift_end');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('shifts', 'auto_punch_out_after_shift_end')) {
                $table->time('auto_punch_out_after_shift_end')->nullable()->comment('Time duration after shift end for auto punch out');
            }
        });
    }
};
