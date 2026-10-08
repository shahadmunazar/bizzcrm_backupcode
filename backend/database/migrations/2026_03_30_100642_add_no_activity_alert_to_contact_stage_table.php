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
            $table->integer('no_activity_alert')->nullable()->after('display_amount_field')->comment('Days of inactivity after which to show an alert');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->dropColumn('no_activity_alert');
        });
    }
};
