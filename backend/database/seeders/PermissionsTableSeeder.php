<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'holidays',
            'leaves',
            'clients',
            'projects',
            'tasks',
            'chats',
            'assets',
            'timing_sheets',
            'accounts',
            'activities_records',
            'general_settings',
            'integrations',
            'finance_dashboard',
            'finance_settings_allowance_types',
            'finance_settings_expense_categories',
            'finance_settings_approval_rules',
            'finance_expenses',
            'finance_approvals',
            'finance_reimbursements',
        ];

        $actions = [
            'read',
            'write',
            'create',
            'delete',
            'import',
            'export',
            'approve',
            'apply',
            'process',
        ];

        $now = Carbon::now();

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissionName = $module . '.' . $action;
                DB::table('permissions')->updateOrInsert(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
