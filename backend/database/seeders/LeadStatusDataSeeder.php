<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;

class LeadStatusDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $leadStatuses = DB::connection('bizzcrm_migrate')->table('lead_statuses')->get();
        $this->command->info('Starting Lead Status Seeder from DB...');
        $startTime = microtime(true);

        $chunk = [];
        $processed = 0;
        
        foreach ($leadStatuses as $item) {
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
                    DB::table('contact_stage')->insertOrIgnore($chunk);
                });
                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            DB::transaction(function () use (&$chunk) {
                DB::table('contact_stage')->insertOrIgnore($chunk);
            });
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $this->command->info("Processed {$processed} lead statuses in {$executionTime} seconds.");
        Schema::enableForeignKeyConstraints();
    }
}
