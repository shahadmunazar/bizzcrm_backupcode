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
            $table->boolean('short_break_enabled_2')->default(false)->after('short_break_hours');
            $table->time('short_break_out_2')->nullable()->after('short_break_enabled_2');
            $table->time('short_break_in_2')->nullable()->after('short_break_out_2');
            $table->string('short_break_hours_2')->nullable()->after('short_break_in_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn([
                'short_break_enabled_2',
                'short_break_out_2',
                'short_break_in_2',
                'short_break_hours_2',
            ]);
        });
    }
};
