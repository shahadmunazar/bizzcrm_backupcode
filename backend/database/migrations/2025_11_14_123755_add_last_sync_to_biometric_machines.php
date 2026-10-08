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
            $table->dateTime('last_sync')->nullable()->after('state_id');
            $table->enum('sync_status', ['green', 'red'])->default('red')->after('last_sync');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometric_machines', function (Blueprint $table) {
            $table->dropColumn(['last_sync', 'sync_status']);
        });
    }
};
