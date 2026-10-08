<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to prevent issues during mass inserts
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        $usersData = DB::connection('bizzcrm_migrate')->table('users')->get();
        $boxes = DB::connection('bizzcrm_migrate')->table('boxes')->get();
        $box_users = DB::connection('bizzcrm_migrate')->table('box_users')->get();
      
        $tables = [
            'users' => $usersData,
            'boxes' => $boxes,
            'box_users' => $box_users,
        ];

        foreach ($tables as $table => $dataStream) {
            $chunk = [];

            foreach ($dataStream as $item) {
                $row = (array) $item;
                
                // Map old SQL column names to current V3 DB column names
                if (array_key_exists('added_by', $row) && in_array($table, ['crm_sources', 'contact_stage'])) {
                    $row['created_by'] = $row['added_by'];
                    unset($row['added_by']);
                }

                if ($table === 'box_users' && isset($row['box_id'])) {
                    // Cache the department and teams list per box to avoid excessive queries
                    static $boxDeptTeamsCache = [];
                    $boxId = $row['box_id'];
                    
                    if (!isset($boxDeptTeamsCache[$boxId])) {
                        $dept = \Illuminate\Support\Facades\DB::table('departments')
                            ->where('box_id', $boxId)
                            ->where('name', 'Marketing')
                            ->first();
                            
                        $teams = [];
                        if ($dept) {
                            $teams = \Illuminate\Support\Facades\DB::table('teams')
                                ->where('box_id', $boxId)
                                ->where('department_id', $dept->id)
                                ->pluck('id')
                                ->toArray();
                        }
                        
                        $boxDeptTeamsCache[$boxId] = [
                            'department' => $dept ? $dept->id : null,
                            'teams' => $teams,
                        ];
                    }

                    $row['department'] = $boxDeptTeamsCache[$boxId]['department'];
                    
                    $availableTeams = $boxDeptTeamsCache[$boxId]['teams'];
                    $row['designation'] = !empty($availableTeams) 
                        ? $availableTeams[array_rand($availableTeams)] 
                        : null;
                }

                $chunk[] = $row;

                if (count($chunk) >= 500) {
                    \Illuminate\Support\Facades\DB::table($table)->insertOrIgnore($chunk);
                    $chunk = [];
                }
            }

            if (count($chunk) > 0) {
                \Illuminate\Support\Facades\DB::table($table)->insertOrIgnore($chunk);
            }
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Update all users so their email_verified_at matches their created_at time
        \Illuminate\Support\Facades\DB::table('users')->update([
            'email_verified_at' => \Illuminate\Support\Facades\DB::raw('created_at')
        ]);

        // Setup Spatie roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $employeeRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Clear existing roles assigned to users to prevent duplicates if seeding multiple times
        \Illuminate\Support\Facades\DB::table('model_has_roles')->where('model_type', \App\Models\User::class)->delete();

        // Find users who are admins (superadmin_id in boxes table)
        $adminUserIds = \Illuminate\Support\Facades\DB::table('boxes')
            ->whereNotNull('superadmin_id')
            ->pluck('superadmin_id')
            ->unique()
            ->toArray();

        $adminInserts = [];
        foreach ($adminUserIds as $userId) {
            $adminInserts[] = [
                'role_id' => $adminRole->id,
                'model_type' => \App\Models\User::class,
                'model_id' => $userId
            ];
        }

        // Find remaining users (employees)
        $employeeUserIds = \Illuminate\Support\Facades\DB::table('users')
            ->whereNotIn('id', $adminUserIds)
            ->pluck('id')
            ->toArray();

        $employeeInserts = [];
        foreach ($employeeUserIds as $userId) {
            $employeeInserts[] = [
                'role_id' => $employeeRole->id,
                'model_type' => \App\Models\User::class,
                'model_id' => $userId
            ];
        }

        // Bulk insert roles to model_has_roles table
        foreach (array_chunk($adminInserts, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('model_has_roles')->insertOrIgnore($chunk);
        }
        foreach (array_chunk($employeeInserts, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('model_has_roles')->insertOrIgnore($chunk);
        }

        // Generate system fields for the newly migrated boxes using LeadFieldsHelper
        $boxes = \App\Models\Box::all();
        $systemFields = \App\Helpers\LeadFieldsHelper::getFields();
        foreach ($boxes as $box) {
            foreach ($systemFields as $field) {
                // Check if it already exists to avoid duplicates on box_id basis
                $existingField = \App\Models\SystemField::where('box_id', $box->id)
                    ->where('object', $field['object'])
                    ->where(function($q) use ($field) {
                        $q->where('field', $field['field'])
                          ->orWhere('field_name', $field['field_name']);
                    })
                    ->first();

                if (!$existingField) {
                    $field['box_id'] = $box->id;
                    \App\Models\SystemField::create($field);
                } else {
                    // Update existing field to ensure settings are fresh
                    $existingField->update($field);
                }
            }
        }
    }
}
