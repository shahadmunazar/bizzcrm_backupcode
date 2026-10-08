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
        $tables = ['facebook_pages', 'facebook_forms', 'facebook_leads'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->softDeletes();
                $table->unsignedBigInteger('added_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();

                $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['facebook_pages', 'facebook_forms', 'facebook_leads'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['added_by']);
                $table->dropForeign(['updated_by']);
                $table->dropForeign(['deleted_by']);
                $table->dropColumn(['deleted_at', 'added_by', 'updated_by', 'deleted_by']);
            });
        }
    }
};
