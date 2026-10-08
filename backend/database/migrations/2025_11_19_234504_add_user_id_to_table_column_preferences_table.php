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
        // First add the user_id column if it doesn't exist
        if (!Schema::hasColumn('table_column_preferences', 'user_id')) {
            Schema::table('table_column_preferences', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('box_id');
            });
        }

        // Get the foreign key constraint name for box_id
        $fkName = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_column_preferences' AND COLUMN_NAME = 'box_id' AND REFERENCED_TABLE_NAME = 'boxes'");
        
        // Drop foreign key on box_id if it exists (needed to drop the unique index)
        if (!empty($fkName)) {
            $constraintName = $fkName[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE `table_column_preferences` DROP FOREIGN KEY `{$constraintName}`");
        }

        // Drop old unique constraint if it exists
        $indexes = DB::select("SHOW INDEX FROM `table_column_preferences` WHERE Key_name = 'box_table_column_unique'");
        if (!empty($indexes)) {
            DB::statement('ALTER TABLE `table_column_preferences` DROP INDEX `box_table_column_unique`');
        }

        // Re-add foreign key on box_id
        if (!empty($fkName)) {
            Schema::table('table_column_preferences', function (Blueprint $table) {
                $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            });
        }

        // Add foreign key for user_id if it doesn't exist
        $userFk = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_column_preferences' AND COLUMN_NAME = 'user_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
        if (empty($userFk)) {
            Schema::table('table_column_preferences', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Add new unique constraint with user_id if it doesn't exist
        $newIndexes = DB::select("SHOW INDEX FROM `table_column_preferences` WHERE Key_name = 'box_user_table_column_unique'");
        if (empty($newIndexes)) {
            Schema::table('table_column_preferences', function (Blueprint $table) {
                $table->unique(['box_id', 'user_id', 'table_name', 'column_name'], 'box_user_table_column_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new unique constraint first
        DB::statement('ALTER TABLE `table_column_preferences` DROP INDEX `box_user_table_column_unique`');

        // Restore old unique constraint
        Schema::table('table_column_preferences', function (Blueprint $table) {
            $table->unique(['box_id', 'table_name', 'column_name'], 'box_table_column_unique');
        });

        // Drop foreign key
        Schema::table('table_column_preferences', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Drop column
        Schema::table('table_column_preferences', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
