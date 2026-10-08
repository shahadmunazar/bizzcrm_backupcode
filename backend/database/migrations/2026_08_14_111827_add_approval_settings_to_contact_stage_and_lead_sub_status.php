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
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false)->after('no_activity_alert_unit');
            $table->integer('approval_department_id')->nullable()->after('requires_approval');
            $table->integer('approval_designation_id')->nullable()->after('approval_department_id');
        });

        Schema::table('lead_sub_status', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false)->after('leads_status_id');
            $table->integer('approval_department_id')->nullable()->after('requires_approval');
            $table->integer('approval_designation_id')->nullable()->after('approval_department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->dropColumn(['requires_approval', 'approval_department_id', 'approval_designation_id']);
        });

        Schema::table('lead_sub_status', function (Blueprint $table) {
            $table->dropColumn(['requires_approval', 'approval_department_id', 'approval_designation_id']);
        });
    }
};
