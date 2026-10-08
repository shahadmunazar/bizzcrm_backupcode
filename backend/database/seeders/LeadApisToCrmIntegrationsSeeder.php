<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadApisToCrmIntegrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Migration from lead_apis (bizzcrm_migrate) to crm_integrations...');

        // Fetch data from old database
        $oldApis = DB::connection('bizzcrm_migrate')->table('lead_apis')->get();

        if ($oldApis->isEmpty()) {
            $this->command->warn('No records found in lead_apis table.');
            return;
        }

        foreach ($oldApis as $oldApi) {
            // Ensure params is valid JSON or null (not empty string)
            $params = empty(trim($oldApi->params ?? '')) ? null : $oldApi->params;

            // Check if it exists in the new table by system_name
            $existing = DB::table('crm_integrations')->where('system_name', $oldApi->system_name)->first();

            if ($existing) {
                // Update params and status
                DB::table('crm_integrations')
                    ->where('id', $existing->id)
                    ->update([
                        'name' => $oldApi->name,
                        'status' => $oldApi->status,
                        'params' => $params,
                        'updated_at' => Carbon::now()
                    ]);
                $this->command->info("Updated existing integration: {$oldApi->system_name}");
            } else {
                // Insert new record
                DB::table('crm_integrations')->insert([
                    'name' => $oldApi->name,
                    'system_name' => $oldApi->system_name,
                    'status' => $oldApi->status,
                    'params' => $params,
                    'icon_type' => 'icon',
                    'icon' => 'fa-solid fa-plug', // Default icon for unknown integrations
                    'created_at' => Carbon::parse($oldApi->created_at ?? now()),
                    'updated_at' => Carbon::now()
                ]);
                $this->command->info("Inserted new integration: {$oldApi->system_name}");
            }
        }
        
        $this->command->info('Migration completed successfully.');
    }
}
