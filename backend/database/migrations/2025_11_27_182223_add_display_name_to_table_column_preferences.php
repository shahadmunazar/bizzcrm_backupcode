<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add display_name column to table_column_preferences table
     */
    public function up(): void
    {
        // Add display_name column if it doesn't exist
        if (! Schema::hasColumn('table_column_preferences', 'display_name')) {
            Schema::table('table_column_preferences', function (Blueprint $table) {
                $table->string('display_name', 100)->nullable()->after('column_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop display_name column if it exists
        if (Schema::hasColumn('table_column_preferences', 'display_name')) {
            Schema::table('table_column_preferences', function (Blueprint $table) {
                $table->dropColumn('display_name');
            });
        }
    }
};
