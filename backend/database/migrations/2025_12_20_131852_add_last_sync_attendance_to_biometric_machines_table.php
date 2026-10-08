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
        Schema::table('biometric_machines', function (Blueprint $table) {
            if (!Schema::hasColumn('biometric_machines', 'last_sync_attendance')) {
                $table->datetime('last_sync_attendance')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometric_machines', function (Blueprint $table) {
            if (Schema::hasColumn('biometric_machines', 'last_sync_attendance')) {
                $table->dropColumn('last_sync_attendance');
            }
        });
    }
};
