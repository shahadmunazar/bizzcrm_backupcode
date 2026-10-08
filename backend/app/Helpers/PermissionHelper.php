<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Get all user permissions as an array for JavaScript
     *
     * @return array
     */
    public static function getUserPermissionsForJs(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        // Global Admin Override
        if ($user->hasRole(['admin', 'superadmin'], 'web') || $user->id == 1) {
            return self::getAllPermissions();
        }

        // Box Admin Override
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        if ($boxUser && strtolower($boxUser->staff_role ?? '') === 'admin') {
            return self::getAllPermissions();
        }

        // Use same logic as hasModulePermission: Direct if exist, otherwise Inherited
        $permissions = $user->permissions()->pluck('name')->toArray();
        
        if (count($permissions) === 0) {
            // Fallback to inherited
            $permissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
            
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            if ($boxUser) {
                if ($boxUser->departmentRelation) {
                    $deptPerms = $boxUser->departmentRelation->permissions()->pluck('name')->toArray();
                    $permissions = array_merge($permissions, $deptPerms);
                }
                if ($boxUser->designationRelation) {
                    $teamPerms = $boxUser->designationRelation->permissions()->pluck('name')->toArray();
                    $permissions = array_merge($permissions, $teamPerms);
                }
            }
        }

        return array_values(array_unique($permissions));
    }

    /**
     * Get all available permissions (including sub-modules)
     *
     * @return array
     */
    private static function getAllPermissions(): array
    {
        // Get all permissions from database (includes sub-modules)
        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
            ->pluck('name')
            ->toArray();
        // dd($permissions);
        return $permissions;
    }

    public static function isPrivilegedUser(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // 1. Admin check (Spatie roles)
        if ($user->hasRole('superadmin', 'web') || $user->id == 1 || $user->hasRole('admin', 'web')) {
            return true;
        }

        // 2. Check for explicit overrides (Direct Permissions)
        $directPermissions = $user->permissions()->pluck('name')->toArray();
        if (count($directPermissions) > 0) {
            return in_array('all.permissions', $directPermissions);
        }

        // 3. Fallback to inherited permissions (Roles, Department, Team)
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        if (in_array('all.permissions', $rolePermissions)) return true;

        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        if ($boxUser) {
            if ($boxUser->departmentRelation) {
                $deptPermissions = $boxUser->departmentRelation->permissions()->pluck('name')->toArray();
                if (in_array('all.permissions', $deptPermissions)) return true;
            }
            if ($boxUser->designationRelation) {
                $teamPermissions = $boxUser->designationRelation->permissions()->pluck('name')->toArray();
                if (in_array('all.permissions', $teamPermissions)) return true;
            }
        }

        return false;
    }

    /**
     * Check if user has permission for a module action
     */
    
    public static function hasModulePermission(string $module, string $action, ?string $subModule = null): bool
{
    $user = Auth::user();

    if (! $user) {
        return false;
    }

    // Global Admin Override
    if (
        $user->id === 1 ||
        $user->hasRole(['admin', 'superadmin'], 'web')
    ) {
        return true;
    }

    //  Box Admin Override
    $boxSlug = request()->route('box_slug');
    $boxUserQuery = \App\Models\BoxUser::where('user_id', $user->id);
    if ($boxSlug) {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if ($box) {
            $boxUser = (clone $boxUserQuery)->where('box_id', $box->id)->first();
        }
    }
    if (!isset($boxUser) || !$boxUser) {
        $boxUser = $boxUserQuery->first();
    }

    if ($boxUser && strtolower($boxUser->staff_role ?? '') === 'admin') {
        return true;
    }

    //  Collect All Permissions Properly
    $permissions = $user->getAllPermissions()->pluck('name')->toArray();
    // dd($permissions);    
    //  Master override
    if (in_array('all.permissions', $permissions)) {
        return true;
    }

    // Strict submodule check
    if ($subModule !== null) {
        return in_array("{$module}.{$subModule}.{$action}", $permissions);
    }

    // Module-level check
    if (in_array("{$module}.{$action}", $permissions)) {
        return true;
    }

    // Optional: allow any submodule of module if no submodule specified
    foreach ($permissions as $perm) {
        if (
            str_starts_with($perm, "{$module}.") &&
            str_ends_with($perm, ".{$action}")
        ) {
            return true;
        }
    }

    return false;
}


    public static function getAccessibleModules(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        // Admin, superadmin, or ID 1 can access all modules
        if ($user->hasRole('admin', 'web') || $user->hasRole('superadmin', 'web') || $user->id == 1) {
            return array_keys(self::allModules());
        }

        // Aggregate permissions (Direct if exists, otherwise Roles + Dept + Team)
        $userPermissions = $user->permissions()->pluck('name')->toArray();
        if (count($userPermissions) === 0) {
            $userPermissions = array_merge($userPermissions, $user->getPermissionsViaRoles()->pluck('name')->toArray());
            
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            if ($boxUser) {
                if ($boxUser->departmentRelation) {
                    $userPermissions = array_merge($userPermissions, $boxUser->departmentRelation->permissions()->pluck('name')->toArray());
                }
                if ($boxUser->designationRelation) {
                    $userPermissions = array_merge($userPermissions, $boxUser->designationRelation->permissions()->pluck('name')->toArray());
                }
            }
        }
        $userPermissions = array_unique($userPermissions);

        // Check global override
        if (in_array('all.permissions', $userPermissions)) {
             return array_keys(self::allModules());
        }

        $accessible = [];
        foreach (self::allModules() as $module => $data) {

            // Check module-level read permission
            if (in_array("{$module}.read", $userPermissions)) {
                $accessible[] = $module;
                continue;
            }

            // Check if ANY sub-module has read permission
            foreach ($data['sub_modules'] as $sub) {
                if (in_array("{$module}.{$sub}.read", $userPermissions)) {
                    $accessible[] = $module;
                    break;
                }
            }
        }

        return $accessible;
    }

    public static function allModules(): array
    {
        return [
            'attendance'         => [
                'name'        => 'Attendance',
                'sub_modules' => ['admin', 'employee', 'biometric', 'calendar', 'list', 'grid', 'details', 'details_personal', 'details_emergency', 'details_about', 'details_bank', 'details_family', 'details_education', 'details_experience', 'details_location', 'details_biometric', 'details_work_mode', 'details_assets', 'details_loan', 'details_projects', 'details_shift', 'leaves', 'admin_leaves', 'leave_settings', 'leave_type', 'leave_transaction', 'shift', 'holiday_type', 'holiday', 'team_admin', 'team_leave_transaction', 'device_register', 'mobile_attendance'],
            ],
            'hr_tools'           => [
                'name'        => 'HR Tools',
                'sub_modules' => ['assets', 'documents', 'letter_generation', 'job_applicants'],
            ],
            'payroll'            => [
                'name'        => 'Payroll',
                'sub_modules' => ['salary', 'report', 'slip', 'items', 'setting', 'loan_settings', 'loan_tracker'],
            ],
            'leads'              => [
                'name'        => 'Leads',
                'sub_modules' => ['dashboard', 'list', 'listall', 'listtrash', 'listduplicate', 'working_location', 'details'],
            ],
            'integrations'       => [
                'name'        => 'Integration',
                'sub_modules' => ['integrations', 'facebook_forms', 'campaign_settings', 'campaign_details', 'project_create', 'bulk_upload', 'labels'],
            ],
            'general_settings'   => [
                'name'        => 'General Settings',
                'sub_modules' => ['companies', 'companies_grid', 'companies_details', 'hsns', 'packages', 'companys', 'activities', 'sources', 'lead_status', 'custom_fields', 'email_inboxes', 'email_messages', 'api_keys', 'roles_permissions'],
            ],
            'website_settings'   => [
                'name'        => 'Website Settings',
                'sub_modules' => ['company', 'branch', 'employee_type', 'attendance_module', 'companys_currency', 'work_mode', 'localization', 'prefixes', 'preference', 'appearance', 'language'],
            ],
            'app_settings'       => [
                'name'        => 'App Settings',
                'sub_modules' => ['invoice', 'printers', 'custom_fields', 'manage_form', 'source', 'lost_reason', 'contact_stage', 'industry', 'calls', 'leads_sub_status'],
            ],
            'system_settings'    => [
                'name'        => 'System Settings',
                'sub_modules' => ['email', 'sms_gateways', 'gdpr'],
            ],
            'financial_settings' => [
                'name'        => 'Financial Settings',
                'sub_modules' => ['payment_gateways', 'bank_accounts', 'tax_rates', 'currencies', 'payment_modes', 'divisions'],
            ],
            'other_settings'     => [
                'name'        => 'Other Settings',
                'sub_modules' => ['box_settings', 'storage', 'ban_ip'],
            ],
            'user_management'    => [
                'name'        => 'User Management',
                'sub_modules' => ['structure', 'manage', 'roles', 'permissions', 'departments', 'designations', 'todo', 'projects', 'deals', 'employee_list', 'employee_type', 'work_mode', 'roles_permissions', 'project_contacts', 'project_types', 'project_categories', 'deal_settings'],
            ],
            'accounts'           => [
                'name'        => 'Accounts',
                'sub_modules' => ['receipts', 'invoice', 'invoices', 'invoice_details', 'invoice_grid', 'invoice_settings', 'invoice_templates'],
            ],
            'activities_records' => [
                'name'        => 'Activities Records',
                'sub_modules' => ['reminder_list', 'today_dsr', 'this_month_dsr', 'all_dsr', 'all_activity_leads'],
            ],
            'reports'            => [
                'name'        => 'Reports',
                'sub_modules' => ['source_stats', 'status_stats', 'task_stats', 'date_task_stats', 'user_stats'],
            ],
            'whatsapp'           => [
                'name'        => 'WhatsApp',
                'sub_modules' => ['dashboard', 'chat', 'create_project', 'campaign', 'campaign_details', 'campaign_complete', 'campaign_archieve'],
            ],
            'facebook'           => [
                'name'        => 'Facebook',
                'sub_modules' => ['dashboard', 'leads', 'sync_leads', 'sync_forms', 'disconnect', 'disconnect_account'],
            ],
        ];
    }

    public static function getAccessibleRoutes(): array
    {
        $routes  = self::getRoutePermissionMap();
        $allowed = [];

        foreach ($routes as $route => $permission) {
            if (self::canAccessRoute($route)) {
                $allowed[] = $route;
            }
        }

        return $allowed;
    }

    public static function getBlockedRoutes(): array
    {
        $routes  = self::getRoutePermissionMap();
        $blocked = [];

        foreach ($routes as $route => $permission) {
            if (! self::canAccessRoute($route)) {
                $blocked[] = $route;
            }
        }

        return $blocked;
    }

    public static function getRouteAccessSummary(): array
    {
        return [
            'allowed_routes' => self::getAccessibleRoutes(),
            'blocked_routes' => self::getBlockedRoutes(),
            'total_routes'   => count(self::getRoutePermissionMap()),
        ];
    }

    /**
     * Get route to permission mapping with sub-module support
     *
     * @return array
     */
    public static function getRoutePermissionMap(): array
    {
        return [
            // Attendance module routes
            'employee.attendace.admin'                => 'attendance.admin.read',
            'employee.attendace.team'                 => 'attendance.team_admin.read',
            'employee.biometric.machine'              => 'attendance.biometric.read',
            'employee.attendace'                      => 'attendance.employee.read',
            'employee.dashboard'                      => 'attendance.employee.read',
            'employee.attendance.calendar'            => 'attendance.calendar.read',
            'manage_users.employee_list'                           => 'user_management.employee_list.read',
            'employee.grid'                           => 'attendance.admin.read',
            'employee.details'                        => 'attendance.admin.read',
            'employee.leaves'                         => 'attendance.leaves.read',
            'employee.leave.admin'                    => 'attendance.admin_leaves.read',
            'employee.leave_settings'                 => 'attendance.leave_settings.read',
            'employee.leads_type'                     => 'attendance.leave_type.read',
            'employee.shift'                          => 'attendance.shift.read',
            'get.shift.data'                          => 'attendance.shift.read',
            'get.shift.details'                       => 'attendance.shift.read',
            'shifts.store'                            => 'attendance.shift.create',
            'Update.shift'                            => 'attendance.shift.update',
            'delete.shift'                            => 'attendance.shift.delete',
            'generate.shift.employees'                => 'attendance.shift.generate',
            'employee.leave_transaction'              => 'attendance.leave_transaction.read',
            'employee.leave_transaction.all'          => 'attendance.leave_transaction.read',
            'employee.leave_transaction.team'         => 'attendance.team_leave_transaction.read',
            'employee.holiday_type'                   => 'attendance.holiday_type.read',
            'employee.holiday.page'                   => 'attendance.holiday.read',
            'assign.employee.shift'                   => 'attendance.details_shift.update',
            'delete.shift.emp'                        => 'attendance.details_shift.delete',

            // HR Tools module routes
            'hr.employess.assets'                     => 'hr_tools.assets.read',
            'hr.employess.hr.documents'               => 'hr_tools.documents.read',
            'hr.employeess.letter.generation'         => 'hr_tools.letter_generation.read',

            // Payroll module routes
            'payroll.employee.salary'                 => 'payroll.salary.read',
            'payroll.employee.report'                 => 'payroll.report.read',
            'payroll.salary.slip'                     => 'payroll.slip.read',
            'payroll.payroll.items'                   => 'payroll.items.read',
            'payroll.setting'                         => 'payroll.setting.update',
            'payroll.loan.settings'                   => 'payroll.loan_settings.update',
            'payroll.loan.tracker'                    => 'payroll.loan_tracker.read',
            'employe.salary.export'                   => 'payroll.salary.export',
            'generate.slip'                           => 'payroll.slip.generate',

            // Leads module routes
            'leads.details'                           => 'leads.list.read',
            'leads.details.all'                       => 'leads.listall.read',
            'leads.details.trash'                     => 'leads.listtrash.read',
            'leads.details.duplicate'                 => 'leads.listduplicate.read',
            'leads.details.working-location'          => 'leads.working_location.read',
            'leads.store'                             => 'leads.create',
            'leads.update'                            => 'leads.update',
            'leads.bulk.trash'                        => 'leads.delete',
            'leads.bulk.delete'                       => 'leads.delete',
            'get.leads.details'                       => 'leads.details.read',

            // Accounts module routes
            'accounts.receipts'                       => 'accounts.receipts.read',
            'accounts.invoice'                        => 'accounts.invoice.read',
            'accounts.invoices'                       => 'accounts.invoices.read',
            'accounts.invoice_details'                => 'accounts.invoice_details.read',
            'accounts.invoice_grid'                   => 'accounts.invoice_grid.read',
            'accounts.invoice_settings'               => 'accounts.invoice_settings.read',


            // Activities Records routes
            'reports.ReminderList'                    => 'activities_records.reminder_list.read',
            'reports.TodayDSR'                        => 'activities_records.today_dsr.read',
            'reports.ThisMonthDSR'                    => 'activities_records.this_month_dsr.read',
            'reports.AllDSR'                          => 'activities_records.all_dsr.read',
            'reports.AllActivityLeads'                => 'activities_records.all_activity_leads.read',
            'get.activity.leads'                      => 'activities_records.all_activity_leads.read',
            'reports.source_stats'                    => 'reports.source_stats.read',
            'reports.status_stats'                    => 'reports.status_stats.read',
            'reports.task_stats'                      => 'reports.task_stats.read',
            'reports.date_task_stats'                 => 'reports.date_task_stats.read',
            'reports.user_stats'                      => 'reports.user_stats.read',

            // Integrations module routes
            'settings.integrations.integrations'      => 'integrations.integrations.read',
            'settings.integrations.facebook.form'     => 'integrations.facebook_forms.read',
            'settings.integrations.compaign.settings' => 'integrations.campaign_settings.update',
            'settings.integrations.compaign.details'  => 'integrations.campaign_details.read',
            'settings.integrations.create.project'    => 'integrations.project_create.create',
            'settings.integrations.labels'            => 'integrations.labels.read',
            'settings.integrations.bulk.uploads'      => 'integrations.bulk_upload.create',

            // General Settings module routes
            'settings.general.companies'              => 'general_settings.companies.read',
            'settings.general.companies.data'         => 'general_settings.companies.read',
            'settings.general.companies.store'        => 'general_settings.companies.create',
            'settings.general.companies.update'       => 'general_settings.companies.update',
            'settings.general.companies.destroy'      => 'general_settings.companies.delete',
            'settings.general.companies.export'       => 'general_settings.companies.export',
            'settings.general.companies_grid'         => 'general_settings.companies_grid.read',
            'settings.general.companys_details'       => 'general_settings.companies_details.read',
            'settings.general.hsns'                   => 'general_settings.hsns.read',
            'settings.general.packages'               => 'general_settings.packages.read',
            'settings.general.companys'               => 'general_settings.companys.update',
            'settings.general.activities'             => 'general_settings.activities.read',
            'settings.general.sources'                => 'general_settings.sources.read',
            'settings.general.lead_status'            => 'general_settings.lead_status.read',
            'settings.general.custom_fields'          => 'general_settings.custom_fields.read',
            'settings.general.email_inboxes'          => 'general_settings.email_inboxes.read',
            'settings.general.email_messages'         => 'general_settings.email_messages.read',
            'settings.get.email_messages'             => 'general_settings.email_messages.read',
            'settings.get.email_message_detail'       => 'general_settings.email_messages.read',
            'settings.general.api_keys'               => 'general_settings.api_keys.read',
            'settings.get.crm_integrations'           => 'general_settings.api_keys.read',
            'settings.add.crm_integration'            => 'general_settings.api_keys.update',
            'settings.toggle.crm_integration'         => 'general_settings.api_keys.update',
            'settings.regenerate.crm_token'           => 'general_settings.api_keys.update',
            'google.connect.box'                      => 'general_settings.api_keys.update',
            'google.sync_emails'                      => 'general_settings.api_keys.read',
            'google.disconnect'                       => 'general_settings.api_keys.update',
            'facebook.checkConnection'                => 'general_settings.api_keys.read',
            'facebook.businessIntegration'            => 'general_settings.api_keys.read',
            'facebook.sync-forms'                     => 'general_settings.api_keys.update',
            'whatsapp.business.checkConnection'       => 'general_settings.api_keys.read',
            'whatsapp.business.registerWebhook'       => 'general_settings.api_keys.update',
            'manage_users.roles_permissions'      => 'user_management.roles_permissions.read',

            // Website Settings module routes
            'website.settings.company'                => 'website_settings.company.update',
            'website.settings.branch'                 => 'website_settings.branch.read',
            'manage_users.employee_type'          => 'user_management.employee_type.read',
            'website.settings.attendance.module'      => 'website_settings.attendance_module.update',
            'website.settings.companys_currency'      => 'website_settings.companys_currency.read',
            'manage_users.work_mode'              => 'user_management.work_mode.read',
            'website.settings.localization'           => 'website_settings.localization.update',
            'website.settings.prefixes'               => 'website_settings.prefixes.update',
            'website.settings.preference'             => 'website_settings.preference.update',
            'website.settings.appearance'             => 'website_settings.appearance.update',
            'website.settings.language'               => 'website_settings.language.read',

            // App Settings module routes
            'app.settings.invoice'                    => 'app_settings.invoice.update',
            'app.settings.printers'                   => 'app_settings.printers.read',
            'app.settings.custom_fields.index'        => 'app_settings.custom_fields.read',
            'app.settings.manage.form'                => 'app_settings.manage_form.read',
            'app.settings.source'                     => 'app_settings.source.read',
            'app.settings.lost.reason'                => 'app_settings.lost_reason.read',
            'app.settings.contact.stage'              => 'app_settings.contact_stage.read',
            'app.settings.industry'                   => 'app_settings.industry.read',
            'app.settings.calls'                      => 'app_settings.calls.read',
            'app.settings.leads.leads_sub_status'     => 'app_settings.leads_sub_status.read',
            'Leadsubstatus.store'                     => 'app_settings.leads_sub_status.create',
            'LeadSubStatus.update'                    => 'app_settings.leads_sub_status.update',
            'LeadSubStatys.delete'                    => 'app_settings.leads_sub_status.delete',

            // System Settings module routes
            'system.settings.email'                   => 'system_settings.email.update',
            'system.settings.sms_gateways'            => 'system_settings.sms_gateways.update',
            'system.settings.gdpr'                    => 'system_settings.gdpr.update',

            // Financial Settings module routes
            'financial.settings.payment_gateways'     => 'financial_settings.payment_gateways.read',
            'financial.settings.bank_accounts'        => 'financial_settings.bank_accounts.read',
            'financial.settings.bank_accounts.store'  => 'financial_settings.bank_accounts.create',
            'financial.settings.bank_accounts.update' => 'financial_settings.bank_accounts.update',
            'financial.settings.bank_accounts.destroy' => 'financial_settings.bank_accounts.delete',
            'financial.settings.payment_modes'        => 'financial_settings.payment_modes.read',
            'financial.settings.payment_modes.store'  => 'financial_settings.payment_modes.create',
            'financial.settings.payment_modes.update' => 'financial_settings.payment_modes.update',
            'financial.settings.payment_modes.destroy' => 'financial_settings.payment_modes.delete',
            'financial.settings.divisions'            => 'financial_settings.divisions.read',
            'financial.settings.divisions.store'      => 'financial_settings.divisions.create',
            'financial.settings.divisions.update'     => 'financial_settings.divisions.update',
            'financial.settings.divisions.destroy'    => 'financial_settings.divisions.delete',
            'financial.settings.tax_rates'            => 'financial_settings.tax_rates.read',
            'financial.settings.tax_rates.list'        => 'financial_settings.tax_rates.read',
            'financial.settings.tax_rates.store'       => 'financial_settings.tax_rates.create',
            'financial.settings.tax_rates.update'      => 'financial_settings.tax_rates.update',
            'financial.settings.tax_rates.delete'      => 'financial_settings.tax_rates.delete',
            'financial.settings.currencies'           => 'financial_settings.currencies.read',

            // Other Settings module routes
            'other.settings.box_settings'             => 'other_settings.box_settings.read',
            'settings.worktime.get'                   => 'other_settings.box_settings.read',
            'settings.worktime.save'                  => 'other_settings.box_settings.update',
            'settings.sync.lead_fields'                => 'general_settings.custom_fields.update',
            'other.settings.storage'                  => 'other_settings.storage.read',
            'other.settings.ban_ip'                   => 'other_settings.ban_ip.read',

            // User Management module routes
            'manage_users.organizational_structure'     => 'user_management.structure.read',
            'roles-permissions'                       => 'user_management.roles.read',
            'manage_users.permissions'                  => 'user_management.permissions.read',

            // Employee Management AJAX routes
            'fetch.employee'                          => 'attendance.list.read',
            'gridfetch.employee'                      => 'attendance.grid.read',
            'employee.get.box.users'                  => 'attendance.list.read',
            'employee.get.types'                      => 'attendance.list.read',
            'store.employee'                          => 'attendance.list.create',
            'employee.update'                         => 'attendance.list.update',
            'employee.update.admin'                   => 'attendance.list.update',
            'delete.employee'                         => 'attendance.list.delete',
            'employee.toggle'                         => 'attendance.list.update',
            'department.manage'                       => 'attendance.list.read',
            'employee.details.admin'                  => 'attendance.details.read',

            // WhatsApp module routes
            'whatsapp.dashboard'                      => 'whatsapp.dashboard.read',
            'whatsapp.index'                          => 'whatsapp.chat.read',
            'whatsapp.create_project'                 => 'whatsapp.create_project.read',
            'whatsapp.campaign'                       => 'whatsapp.campaign.read',
            'whatsapp.campaign_details'               => 'whatsapp.campaign_details.read',
            'whatsapp.campaign_complete'              => 'whatsapp.campaign_complete.read',
            'whatsapp.campaign_archieve'              => 'whatsapp.campaign_archieve.read',

            // Facebook module routes
            'facebook.dashboard'                      => 'facebook.dashboard.read',
            'facebook.leads'                          => 'facebook.leads.read',
            'facebook.leads.list'                     => 'facebook.leads.read',
            'facebook.sync-forms'                     => 'facebook.sync_forms.update',
            'facebook.sync-leads'                     => 'facebook.sync_leads.update',
            'facebook.disconnect'                     => 'facebook.disconnect.update',
            'facebook.disconnect_account'             => 'facebook.disconnect_account.update',

            // Newly added mapped routes
            'accounts.invoice_templates'              => 'accounts.invoice_templates.read',
            'device-register.index'                   => 'attendance.device_register.read',
            'hr.job.applicants.index'                 => 'hr_tools.job_applicants.read',
            'leads.dashboard'                         => 'leads.dashboard.read',
            'manage.users'                            => 'user_management.manage.read',
            'mobile-attendance.fingerprints'          => 'attendance.mobile_attendance.read',
            'mobile-attendance.punches'               => 'attendance.mobile_attendance.read',
            
            // Manage users sub-items
            'manage_users.departments'                => 'user_management.departments.read',
            'manage_users.designations'               => 'user_management.designations.read',
            'manage_users.todo'                       => 'user_management.todo.read',
            'manage_users.projects'                   => 'user_management.projects.read',
            'manage_users.projects.contacts'          => 'user_management.project_contacts.read',
            'settings.project_types'                  => 'user_management.project_types.read',
            'settings.project_categories'             => 'user_management.project_categories.read',
            'manage_users.deals'                      => 'user_management.deals.read',
            'manage_users.deal_settings.index'        => 'user_management.deal_settings.read',
        ];
    }

    /**
     * Get permission required for a route
     *
     * @param string $routeName
     * @return string|null
     */
    public static function getPermissionForRoute(string $routeName): ?string
    {
        $map = self::getRoutePermissionMap();
        return $map[$routeName] ?? null;
    }

    public static function canAccessAny(array $routes): bool
    {
        foreach ($routes as $route) {
            if (self::canAccessRoute($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access a route
     *
     * Two-level permission check:
     * 1. First check if route has specific permission mapping - check that specific permission
     * 2. If user has module-level read permission, grant access to all routes in that module
     * 3. If no mapping exists, deny access by default
     *
     * @param string $routeName
     * @return bool
     */
    public static function canAccessRoute(string $routeName): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        // Global Admin check (Changed to Superadmin only to enforce granular permissions for Admins)
        if ($user->hasRole(['superadmin'], 'web') || $user->id == 1) {
            return true;
        }

        // Box Admin check - box-level admins can access all routes
        /*
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        if ($boxUser && strtolower($boxUser->staff_role ?? '') === 'admin') {
            return true;
        }
        */

        $permission = self::getPermissionForRoute($routeName);
        if (!$permission) {
            return false;
        }

        // Aggregate all permissions (Direct + Roles + Dept + Team)
        $allPermissions = $user->permissions()->pluck('name')->toArray();
        // dd($allPermissions);
        $allPermissions = array_merge($allPermissions, $user->getPermissionsViaRoles()->pluck('name')->toArray());
        
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
        if ($boxUser) {
            if ($boxUser->departmentRelation) {
                $allPermissions = array_merge($allPermissions, $boxUser->departmentRelation->permissions()->pluck('name')->toArray());
            }
            if ($boxUser->designationRelation) {
                $allPermissions = array_merge($allPermissions, $boxUser->designationRelation->permissions()->pluck('name')->toArray());
            }
        }
        $allPermissions = array_unique($allPermissions);

        // Check for master permission
        if (in_array('all.permissions', $allPermissions)) {
            return true;
        }

        // Check specific route permission
        if (in_array($permission, $allPermissions)) {
            return true;
        }

        // Check module-level read permission as fallback for any specific permission in that module
        /*
        $parts = explode('.', $permission);
        if (count($parts) >= 2) {
            $module = $parts[0];
            if (in_array("{$module}.read", $allPermissions)) {
                return true;
            }
        }
        */

        return false;
    }
}
