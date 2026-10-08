<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            if (Schema::hasColumn('salary_settings', 'salary_structure_name')) {
                // Change column type from decimal to string
                DB::statement('ALTER TABLE `salary_settings` MODIFY `salary_structure_name` VARCHAR(255) NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            if (Schema::hasColumn('salary_settings', 'salary_structure_name')) {
                // Revert back to decimal (though this shouldn't normally be needed)
                DB::statement('ALTER TABLE `salary_settings` MODIFY `salary_structure_name` DECIMAL(12,2) DEFAULT 0');
            }
        });
    }
};
