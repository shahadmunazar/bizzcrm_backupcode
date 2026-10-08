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
            $table->index(['box_id', 'lead_source_id', 'assigned_to'], 'idx_box_source_assigned');
            $table->index(['box_id', 'lead_source_id', 'lead_status_id'], 'idx_box_source_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_box_source_assigned');
            $table->dropIndex('idx_box_source_status');
        });
    }
};
