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
        Schema::table('target_assignments', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'box_user_id');
        });
        // Assuming performance_results also needs renaming if it exists
        if (Schema::hasColumn('performance_results', 'employee_id')) {
            Schema::table('performance_results', function (Blueprint $table) {
                $table->renameColumn('employee_id', 'box_user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_assignments', function (Blueprint $table) {
            $table->renameColumn('box_user_id', 'employee_id');
        });
        if (Schema::hasColumn('performance_results', 'box_user_id')) {
            Schema::table('performance_results', function (Blueprint $table) {
                $table->renameColumn('box_user_id', 'employee_id');
            });
        }
    }
};
