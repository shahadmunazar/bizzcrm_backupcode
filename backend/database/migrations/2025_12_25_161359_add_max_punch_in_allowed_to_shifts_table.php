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
            if (!Schema::hasColumn('shifts', 'max_punch_in_allowed')) {
                $table->integer('max_punch_in_allowed')->nullable()->after('early_login_minutes')->comment('Maximum number of punch-ins allowed per day');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            if (Schema::hasColumn('shifts', 'max_punch_in_allowed')) {
                $table->dropColumn('max_punch_in_allowed');
            }
        });
    }
};
