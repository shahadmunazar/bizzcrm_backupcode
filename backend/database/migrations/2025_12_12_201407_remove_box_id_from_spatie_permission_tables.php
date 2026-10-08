<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop box_id from roles table
        if (Schema::hasColumn('roles', 'box_id')) {
            Schema::table('roles', function (Blueprint $table) {
                try {
                    $table->dropIndex('roles_team_foreign_key_index');
                } catch (\Exception $e) {
                    // Index might not exist
                }
                $table->dropColumn('box_id');
            });
        }

        // Drop box_id from model_has_roles table
        if (Schema::hasColumn('model_has_roles', 'box_id')) {
            // Use raw SQL to drop primary key and recreate it
            DB::statement('ALTER TABLE model_has_roles DROP PRIMARY KEY');
            
            Schema::table('model_has_roles', function (Blueprint $table) {
                try {
                    $table->dropIndex('model_has_roles_team_foreign_key_index');
                } catch (\Exception $e) {
                    // Index might not exist
                }
                $table->dropColumn('box_id');
            });
            
            // Recreate primary key without box_id
            DB::statement('ALTER TABLE model_has_roles ADD PRIMARY KEY (role_id, model_id, model_type)');
        }

        // Drop box_id from model_has_permissions table
        if (Schema::hasColumn('model_has_permissions', 'box_id')) {
            // Use raw SQL to drop primary key and recreate it
            DB::statement('ALTER TABLE model_has_permissions DROP PRIMARY KEY');
            
            Schema::table('model_has_permissions', function (Blueprint $table) {
                try {
                    $table->dropIndex('model_has_permissions_team_foreign_key_index');
                } catch (\Exception $e) {
                    // Index might not exist
                }
                $table->dropColumn('box_id');
            });
            
            // Recreate primary key without box_id
            DB::statement('ALTER TABLE model_has_permissions ADD PRIMARY KEY (permission_id, model_id, model_type)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columnNames = config('permission.column_names');
        $teamForeignKey = $columnNames['team_foreign_key'] ?? 'box_id';

        // Add box_id back to roles table
        if (!Schema::hasColumn('roles', $teamForeignKey)) {
            Schema::table('roles', function (Blueprint $table) use ($teamForeignKey) {
                $table->unsignedBigInteger($teamForeignKey)->nullable()->after('id');
                $table->index($teamForeignKey, 'roles_team_foreign_key_index');
            });
        }

        // Add box_id back to model_has_roles table
        if (!Schema::hasColumn('model_has_roles', $teamForeignKey)) {
            Schema::table('model_has_roles', function (Blueprint $table) use ($teamForeignKey) {
                $table->dropPrimary('model_has_roles_role_model_type_primary');
                $table->unsignedBigInteger($teamForeignKey)->after('model_id');
                $table->index($teamForeignKey, 'model_has_roles_team_foreign_key_index');
                $table->primary([$teamForeignKey, 'role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            });
        }

        // Add box_id back to model_has_permissions table
        if (!Schema::hasColumn('model_has_permissions', $teamForeignKey)) {
            Schema::table('model_has_permissions', function (Blueprint $table) use ($teamForeignKey) {
                $table->dropPrimary('model_has_permissions_permission_model_type_primary');
                $table->unsignedBigInteger($teamForeignKey)->after('model_id');
                $table->index($teamForeignKey, 'model_has_permissions_team_foreign_key_index');
                $table->primary([$teamForeignKey, 'permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            });
        }
    }
};
