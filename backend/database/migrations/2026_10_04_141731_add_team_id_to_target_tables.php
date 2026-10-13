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
            $table->unsignedBigInteger('team_id')->nullable()->after('department_id');
        });
        
        Schema::table('performance_results', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_assignments', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
        
        Schema::table('performance_results', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
};
