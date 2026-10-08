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
            $table->unsignedBigInteger('last_assigned_to')->nullable()->after('assigned_to');
            $table->unsignedBigInteger('last_lead_status')->nullable()->after('lead_status_id');

            // Foreign keys
            $table->foreign('last_assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('last_lead_status')->references('id')->on('contact_stage')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['last_assigned_to']);
            $table->dropForeign(['last_lead_status']);
            $table->dropColumn(['last_assigned_to', 'last_lead_status']);
        });
    }
};
