<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RoleSeeder first to seed roles
        // 1. Core Reference & System Data (No Dependencies)
        // These are global tables needed by almost everything else.
        $this->call(Countries::class);
        $this->call(States::class);
        $this->call(Cities::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(ModulePermissionSeeder::class);
        $this->call(MasterIndustrySeeder::class);
        $this->call(IndustrySeeder::class);
        $this->call(PackageFeatureSeeder::class);
        $this->call(WhatsAppFeatureSeeder::class);
        $this->call(PlanSeeder::class);

        // 2. Base Structural Data (Users, Boxes)
        // DataMigrate imports old users/boxes, so it must run before dummy seeders
        $this->call(DataMigrateSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UserEmailVerifiedAtSeeder::class);
        $this->call(BoxSeeder::class);
        $this->call(BoxPermissionSeeder::class);

        // 3. Employee & Organizational Setup (Depends on Users & Boxes)
        // Seed employee types, work modes, shifts, departments, and assign employee data together
        $this->call(EmployeeTypeSeeder::class);
        $this->call(WorkModeSettingSeeder::class);
        $this->call(StandardShiftSeeder::class);
        $this->call(DepartmentCreateJobSeeder::class);
        $this->call(BackfillSystemEmployeeDataSeeder::class);
        $this->call(BackfillEmployeeTypeAndWorkModeSeeder::class);
        $this->call(UpdateBoxLeaveCreditSettingsSeeder::class);

        // 4. Statuses, Sources & Templates (Depends on Boxes/System)
        // Configurations that Leads and Activities rely on
        $this->call(LeadStatusDataSeeder::class);
        $this->call(LeadSourceDataSeeder::class);
        $this->call(LeadSourceTemplatesSeeder::class);
        $this->call(BackfillActivitySettingsSeeder::class);
        $this->call(ActivityTypeDataSeeder::class);

        $this->call(ActivityStatusDataSeeder::class);
        $this->call(BoxCustomFieldsSeeder::class);
        $this->call(BoxCustomDataSeeder::class);
        $this->call(CrmIntegrationSeeder::class);
        $this->call(BoxCrmIntegrationsSeeder::class);
        
        // 4. Organization Structure
        // Depends on Boxes
        $this->call(CompaniesDataSeeder::class);
        $this->call(DivisionSeeder::class);

        // 5. Core Operational Data (Depends on Users, Boxes, Locations, Statuses)
        $this->call(LeadsDataSeeder::class);
        $this->call(ActivitySeeder::class);
        
        // 6. External Data & Financials (Depends on Leads & Boxes)
        $this->call(ThirdPartyLeadsSeeder::class);
        $this->call(FacebookFormsSeeder::class);
        $this->call(AccountsDataSeeder::class);

        // 7. Email Data Migration (Must be last to ensure Users, Boxes, and Leads exist)
        $this->call(EmailInboxesSeeder::class);
        $this->call(GmailMessagesSeeder::class);

        // 8. Master Content & System Fields
        $this->call(SystemFieldsSeeder::class);
        $this->call(LeaveTypeSeeder::class);
        $this->call(IndianHolidaysSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(LeadApisToCrmIntegrationsSeeder::class);
        $this->call(LeadSourceTemplatesTableSeeder::class);
    }
}
