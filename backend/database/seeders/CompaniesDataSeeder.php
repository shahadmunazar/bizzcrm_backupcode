<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompaniesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $oldCompaniesCount = DB::connection('bizzcrm_migrate')->table('companies')->count();
        $this->command->info("Found {$oldCompaniesCount} records to migrate.");
        
        $bar = $this->command->getOutput()->createProgressBar($oldCompaniesCount);
        $bar->start();
        
        $stream = DB::connection('bizzcrm_migrate')->table('companies')->orderBy('id')->cursor();
        $insertData = [];
        $batchSize = 500;
        
        foreach ($stream as $company) {
            $insertData[] = [
                'id' => $company->id,
                'box_id' => $company->box_id,
                'bill_name' => $company->bill_name,
                'bill_phone' => $company->bill_phone,
                'pan' => $company->pan,
                'gstin' => $company->gstin,
                'bill_address' => $company->bill_address,
                'bill_pin_code' => $company->bill_pin_code,
                'bill_state_id' => $company->bill_state_id,
                'bill_city_id' => $company->bill_city_id,
                'status' => $company->status,
                'added_by' => $company->added_by,
                'last_updated_by' => $company->last_updated_by,
                'created_at' => $company->created_at,
                'updated_at' => $company->updated_at,
                'deleted_at' => $company->deleted_at,
                'params' => $company->params,
            ];
            
            if (count($insertData) >= $batchSize) {
                DB::table('companies')->upsert($insertData, ['id'], [
                    'box_id', 'bill_name', 'bill_phone', 'pan', 'gstin',
                    'bill_address', 'bill_pin_code', 'bill_state_id', 'bill_city_id',
                    'status', 'added_by', 'last_updated_by', 'created_at', 'updated_at',
                    'deleted_at', 'params'
                ]);
                $bar->advance(count($insertData));
                $insertData = [];
            }
        }
        
        // Insert any remaining records
        if (count($insertData) > 0) {
            DB::table('companies')->upsert($insertData, ['id'], [
                'box_id', 'bill_name', 'bill_phone', 'pan', 'gstin',
                'bill_address', 'bill_pin_code', 'bill_state_id', 'bill_city_id',
                'status', 'added_by', 'last_updated_by', 'created_at', 'updated_at',
                'deleted_at', 'params'
            ]);
            $bar->advance(count($insertData));
        }
            
        $bar->finish();
        $this->command->info("\nCompanies migrated successfully!");

    }
}
