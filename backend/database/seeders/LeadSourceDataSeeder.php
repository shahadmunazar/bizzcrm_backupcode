<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;

class LeadSourceDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $leadSources = DB::connection('bizzcrm_migrate')->table('lead_sources')->get();
        $this->command->info('Starting Lead Source Seeder from DB...');
        $startTime = microtime(true);

        $chunk = [];
        $processed = 0;
        
        foreach ($leadSources as $item) {
            $row = (array) $item;
            $processed++;
            
            // Map old SQL column names to current V3 DB column names
            if (array_key_exists('added_by', $row)) {
                $row['created_by'] = $row['added_by'];
                unset($row['added_by']);
            }

            $chunk[] = $row;

            if (count($chunk) >= 500) {
                DB::transaction(function () use (&$chunk) {
                    DB::table('crm_sources')->insertOrIgnore($chunk);
                });
                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            DB::transaction(function () use (&$chunk) {
                DB::table('crm_sources')->insertOrIgnore($chunk);
            });
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $this->command->info("Processed {$processed} lead sources in {$executionTime} seconds.");
        Schema::enableForeignKeyConstraints();
    }
}
