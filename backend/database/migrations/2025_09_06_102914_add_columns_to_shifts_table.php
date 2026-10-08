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
            $table->string('weekly_off_3')->nullable()->after('weekly_off_2');
            $table->string('weekly_off_4')->nullable()->after('weekly_off_3');
            $table->string('check_over_time')->nullable()->after('weekly_off_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
                        $table->dropColumn(['weekly_off_3', 'weekly_off_4', 'check_over_time']);

        });
    }
};
