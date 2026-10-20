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
        // 1. Covering index for leads source stats (box_id, deleted_at, lead_source_id, amount)
        try {
            $hasLeadIndex = collect(DB::select("SHOW INDEXES FROM leads WHERE Key_name = 'idx_leads_box_del_source_amount'"))->isNotEmpty();
            if (!$hasLeadIndex) {
                DB::statement('CREATE INDEX idx_leads_box_del_source_amount ON leads(box_id, deleted_at, lead_source_id, amount)');
            }
        } catch (\Throwable $e) {
            // Already exists or error logged
        }

        // 2. Performance index for activities status and user reporting
        try {
            $hasActIndex = collect(DB::select("SHOW INDEXES FROM activities WHERE Key_name = 'idx_act_user_perf'"))->isNotEmpty();
            if (!$hasActIndex) {
                DB::statement('CREATE INDEX idx_act_user_perf ON activities(box_id, deleted_at, status, assigned_to, created_at)');
            }
        } catch (\Throwable $e) {
            // Already exists or error logged
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('DROP INDEX idx_leads_box_del_source_amount ON leads');
        } catch (\Throwable $e) {}

        try {
            DB::statement('DROP INDEX idx_act_user_perf ON activities');
        } catch (\Throwable $e) {}
    }
};
