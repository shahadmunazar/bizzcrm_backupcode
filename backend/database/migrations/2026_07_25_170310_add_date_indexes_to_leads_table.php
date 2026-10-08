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
            $indexesFound = Schema::getIndexes('leads');
            $indexNames = array_column($indexesFound, 'name');
            
            if (!in_array('leads_created_at_index', $indexNames)) {
                $table->index('created_at', 'leads_created_at_index');
            }
            if (!in_array('leads_updated_at_index', $indexNames)) {
                $table->index('updated_at', 'leads_updated_at_index');
            }
            if (!in_array('leads_box_del_created_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'created_at'], 'leads_box_del_created_idx');
            }
            if (!in_array('leads_box_del_assigned_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'assigned_to', 'duplicate_leads'], 'leads_box_del_assigned_idx');
            }
            if (!in_array('leads_box_del_duplicate_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'duplicate_leads'], 'leads_box_del_duplicate_idx');
            }
            if (!in_array('leads_box_del_status_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'lead_status_id'], 'leads_box_del_status_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_created_at_index');
            $table->dropIndex('leads_updated_at_index');
            $table->dropIndex('leads_box_del_created_idx');
            $table->dropIndex('leads_box_del_assigned_idx');
            $table->dropIndex('leads_box_del_duplicate_idx');
            $table->dropIndex('leads_box_del_status_idx');
        });
    }
};
