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
            $table->index(['activity_id', 'activity_date'], 'activities_id_date_idx');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->index(['lead_id', 'created_at', 'deleted_at'], 'receipts_lead_created_del_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_id_date_idx');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex('receipts_lead_created_del_idx');
        });
    }
};
