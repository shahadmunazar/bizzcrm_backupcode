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
        $tables = ['divisions', 'division_areas', 'area_leads'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->index();
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                }
                if (!Schema::hasColumn($tableName, 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->index();
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                }
                if (!Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->index();
                    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                }
                
                // Ensure softDeletes is present if deleted_by is used
                if (!Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['divisions', 'division_areas', 'area_leads'];
        
        foreach ($tables as $tableName) {
             Schema::table($tableName, function (Blueprint $table) {
                // Drop FKs first
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropForeign(['deleted_by']);
                
                $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
             });
        }
    }
};
