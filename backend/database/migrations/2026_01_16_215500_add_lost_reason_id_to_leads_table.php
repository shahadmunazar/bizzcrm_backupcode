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
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('lost_reason_id')->nullable()->after('lead_status_id');
            // Assuming lost_reasons table exists
             $table->foreign('lost_reason_id')->references('id')->on('lost_reasons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lost_reason_id']);
            $table->dropColumn('lost_reason_id');
        });
    }
};
