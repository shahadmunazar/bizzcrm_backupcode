<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModulePermissionSeeder extends Seeder
{
    private $modules = [];
    private $actions = ['read', 'create', 'update', 'delete', 'generate', 'assigned', 'approved', 'disapproved', 'import', 'export'];
    public function run()
    {
        $this->modules = \App\Helpers\PermissionHelper::allModules();
        $allPermissions = [];
        $allPermissionsPerm = Permission::firstOrCreate(
            ['name' => 'all.permissions', 'guard_name' => 'web']
        );
        $allPermissions[] = $allPermissionsPerm;
        foreach ($this->modules as $moduleKey => $moduleData) {
            $subModules = $moduleData['sub_modules'] ?? [];
            foreach ($subModules as $subModule) {
                foreach ($this->actions as $action) {
                    $permissionName = "{$moduleKey}.{$subModule}.{$action}";
                    $permission     = Permission::firstOrCreate(
                        ['name' => $permissionName, 'guard_name' => 'web']
                    );
                    $allPermissions[] = $permission;
                }
            }
            foreach ($this->actions as $action) {
                $permissionName = "{$moduleKey}.{$action}";
                $permission     = Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web']
                );
                $allPermissions[] = $permission;
            }
        }
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $adminRole      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole    = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $employeeRole   = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($allPermissions);
        $adminRole->syncPermissions($allPermissions);
        $managerPermissions = [];
        foreach ($this->modules as $moduleKey => $moduleData) {
            $subModules = $moduleData['sub_modules'] ?? [];
            $managerPermissions[] = "{$moduleKey}.read";
            $managerPermissions[] = "{$moduleKey}.create";
            $managerPermissions[] = "{$moduleKey}.update";
            foreach ($subModules as $subModule) {
                $managerPermissions[] = "{$moduleKey}.{$subModule}.read";
                $managerPermissions[] = "{$moduleKey}.{$subModule}.create";
                $managerPermissions[] = "{$moduleKey}.{$subModule}.update";
            }
        }
        $managerRole->syncPermissions($managerPermissions);
        $employeePermissions    = [];
        $employeeAllowedModules = ['attendance', 'hr_tools', 'payroll'];
        foreach ($employeeAllowedModules as $moduleKey) {
            if (isset($this->modules[$moduleKey])) {
                $moduleData = $this->modules[$moduleKey];
                $subModules = $moduleData['sub_modules'] ?? [];
                $employeePermissions[] = "{$moduleKey}.read";
                foreach ($subModules as $subModule) {
                    $employeePermissions[] = "{$moduleKey}.{$subModule}.read";
                }
            }
        }
        $employeeRole->syncPermissions($employeePermissions);
        $this->command->info('Module permissions created successfully!');
        $this->command->info('Total permissions created: ' . count($allPermissions));
        $this->command->info('Admin role: All permissions (' . count($allPermissions) . ' permissions)');
        $this->command->info('Manager role: Read, Create, Update permissions (' . count($managerPermissions) . ' permissions)');
        $this->command->info('Employee role: Read permissions for Attendance, HR Tools, and Payroll (' . count($employeePermissions) . ' permissions)');
    }
}
