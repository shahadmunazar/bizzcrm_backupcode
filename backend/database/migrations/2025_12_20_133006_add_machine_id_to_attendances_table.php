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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'machine_id')) {
                $table->unsignedBigInteger('machine_id')->nullable()->after('box_id')->index();
                $table->foreign('machine_id')->references('id')->on('biometric_machines')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'machine_id')) {
                $table->dropForeign(['machine_id']);
                $table->dropColumn('machine_id');
            }
        });
    }
};
