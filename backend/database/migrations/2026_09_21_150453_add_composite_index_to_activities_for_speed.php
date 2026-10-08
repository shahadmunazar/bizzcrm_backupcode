<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Try creating the index with INPLACE algorithm to avoid locking the table.
        // If it fails, fallback to normal index creation.
        try {
            DB::statement('CREATE INDEX act_box_del_status_type_actdate_idx ON activities(box_id, deleted_at, status, type, activity_date) ALGORITHM=INPLACE, LOCK=NONE');
        } catch (\Exception $e) {
            Schema::table('activities', function (Blueprint $table) {
                $table->index(['box_id', 'deleted_at', 'status', 'type', 'activity_date'], 'act_box_del_status_type_actdate_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('act_box_del_status_type_actdate_idx');
        });
    }
};
