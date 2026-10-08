<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddLeadSourceToWebHooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_hooks', function (Blueprint $table) {
            if (!Schema::hasColumn('web_hooks', 'lead_source_id')) {
                $table->unsignedBigInteger('lead_source_id')->nullable();
                $table->foreign('lead_source_id')->references('id')->on('crm_sources')->onDelete('set null');
            }
            $table->unsignedBigInteger('added_by')->nullable(); // created_by
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_hooks', function (Blueprint $table) {
            if (Schema::hasColumn('web_hooks', 'lead_source_id')) {
                $table->dropForeign(['lead_source_id']);
                $table->dropColumn('lead_source_id');
            }
            $table->dropSoftDeletes();
            $table->dropColumn(['added_by', 'updated_by', 'created_by']);
        });
    }
}
