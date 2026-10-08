<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_status_id')->nullable()->after('activity_id');
            $table->unsignedBigInteger('lead_sub_status_id')->nullable()->after('lead_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['lead_status_id', 'lead_sub_status_id']);
        });
    }
};
