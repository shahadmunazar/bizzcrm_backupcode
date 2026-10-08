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
        Schema::table('box_users', function (Blueprint $table) {
            $table->unsignedBigInteger('reporting_manager_id')->nullable()->after('designation');
            $table->foreign('reporting_manager_id')
                  ->references('id')
                  ->on('box_users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('box_users', function (Blueprint $table) {
            $table->dropForeign(['reporting_manager_id']);
            $table->dropColumn('reporting_manager_id');
        });
    }
};
