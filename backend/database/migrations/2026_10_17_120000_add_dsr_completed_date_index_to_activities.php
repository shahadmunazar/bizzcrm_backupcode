<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $hasIndex = collect(DB::select("SHOW INDEXES FROM activities WHERE Key_name = 'idx_act_box_del_completed_actid'"))->isNotEmpty();
            if (!$hasIndex) {
                DB::statement('CREATE INDEX idx_act_box_del_completed_actid ON activities(box_id, deleted_at, completed_date, activity_id)');
            }
        } catch (\Throwable $e) {
            // Log or ignore if already created
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('DROP INDEX idx_act_box_del_completed_actid ON activities');
        } catch (\Throwable $e) {}
    }
};
