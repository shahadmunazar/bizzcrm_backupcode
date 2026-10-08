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
        Schema::table('boxes', function (Blueprint $table) {
            $table->string('bulk_lead_assignment')->nullable()->after('notification');
            $table->string('lead_assignment')->default(0)->after('bulk_lead_assignment');
            $table->boolean('change_lead_assigned_if_admin')->default(0)->after('lead_assignment');
            $table->boolean('change_lead_status_if_admin')->default(0)->after('change_lead_assigned_if_admin');
            $table->boolean('change_lead_source_if_admin')->default(0)->after('change_lead_status_if_admin');
            $table->string('activity_reminder')->nullable()->after('change_lead_source_if_admin');
            $table->boolean('allow_duplicate_leads')->default(0)->after('activity_reminder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn([
                'bulk_lead_assignment',
                'lead_assignment',
                'change_lead_assigned_if_admin',
                'change_lead_status_if_admin',
                'change_lead_source_if_admin',
                'activity_reminder',
                'allow_duplicate_leads',
            ]);
        });
    }
};
