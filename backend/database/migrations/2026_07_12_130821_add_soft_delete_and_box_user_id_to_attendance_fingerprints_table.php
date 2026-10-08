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
        Schema::table('attendance_fingerprints', function (Blueprint $table) {
            $table->unsignedBigInteger('box_user_id')->nullable()->after('user_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_fingerprints', function (Blueprint $table) {
            $table->dropColumn(['box_user_id', 'deleted_at']);
        });
    }
};
