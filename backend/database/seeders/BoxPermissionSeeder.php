<?php

namespace Database\Seeders;

use App\Models\Box;
use App\Models\BoxUser;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BoxPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'Holidays',
            'Leaves',
            'Clients',
            'Projects',
            'Tasks',
            'Chats',
            'Assets',
            'Timing Sheets',
            'Accounts',
            'Activities Records',
            'General Settings',
            'Integrations',
        ];

        $actions = [
            'Read',
            'Write',
            'Create',
            'Delete',
            'Import',
            'Export',
        ];

        // Employee module permissions
        $employeeActions = ['read', 'write', 'create', 'delete', 'export', 'import'];
        foreach ($employeeActions as $action) {
            Permission::firstOrCreate(
                [
                    'name' => 'employees.' . $action,
                    'guard_name' => 'web',
                ]
            );
        }
        
        // Create employees.all permission
        Permission::firstOrCreate(
            [
                'name' => 'employees.all',
                'guard_name' => 'web',
            ]
        );

        // Create all module-specific permissions (shared across boxes)
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissionName = strtolower(str_replace(' ', '_', $module)) . '.' . strtolower($action);
                
                Permission::firstOrCreate(
                    [
                        'name' => $permissionName,
                        'guard_name' => 'web',
                    ]
                );
            }
        }

        // Create the "All Permissions" master permission
        // This permission grants access to all modules and actions
        Permission::firstOrCreate(
            [
                'name' => 'all.permissions',
                'guard_name' => 'web',
            ],
            [
                'name' => 'all.permissions',
                'guard_name' => 'web',
            ]
        );

        // Also create module-level "all" permissions for convenience
        foreach ($modules as $module) {
            $moduleKey = strtolower(str_replace(' ', '_', $module));
            Permission::firstOrCreate(
                [
                    'name' => $moduleKey . '.all',
                    'guard_name' => 'web',
                ]
            );
        }
    }
}

