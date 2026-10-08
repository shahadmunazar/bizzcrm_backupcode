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
            $table->index(['box_id', 'deleted_at', 'lead_source_id', 'lead_status_id'], 'leads_stats_source_status_idx');
            $table->index(['box_id', 'deleted_at', 'lead_status_id', 'lead_source_id'], 'leads_stats_status_source_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_stats_source_status_idx');
            $table->dropIndex('leads_stats_status_source_idx');
        });
    }
};
