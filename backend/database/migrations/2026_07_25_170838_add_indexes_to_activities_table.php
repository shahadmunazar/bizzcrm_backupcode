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
        Schema::table('activities', function (Blueprint $table) {
            $indexesFound = Schema::getIndexes('activities');
            $indexNames = array_column($indexesFound, 'name');
            
            if (!in_array('act_box_del_created_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'created_at'], 'act_box_del_created_idx');
            }
            if (!in_array('act_box_del_status_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'status'], 'act_box_del_status_idx');
            }
            if (!in_array('act_box_del_actdate_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'activity_date'], 'act_box_del_actdate_idx');
            }
            if (!in_array('act_box_del_status_actdate_idx', $indexNames)) {
                $table->index(['box_id', 'deleted_at', 'status', 'activity_date'], 'act_box_del_status_actdate_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('act_box_del_created_idx');
            $table->dropIndex('act_box_del_status_idx');
            $table->dropIndex('act_box_del_actdate_idx');
        });
    }
};
