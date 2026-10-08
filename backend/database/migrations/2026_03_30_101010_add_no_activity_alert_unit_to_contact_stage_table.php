<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->string('no_activity_alert_unit')->default('Days')->after('no_activity_alert')->comment('Unit for the inactivity alert (Days, Hours, Minutes, etc.)');
        });
    }

    public function down()
    {
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->dropColumn('no_activity_alert_unit');
        });
    }
};
