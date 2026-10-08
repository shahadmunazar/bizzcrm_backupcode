<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;

class ActivityTypeDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $activityTypes = DB::connection('bizzcrm_migrate')->table('activity_types')->get();
        $this->command->info('Starting Activity Types Seeder from DB...');
        $startTime = microtime(true);

        $chunk = [];
        $processed = 0;

        foreach ($activityTypes as $item) {
            $row = (array) $item;
            $processed++;
            
            $mappedRow = [
                'id' => $row['id'] ?? null,
                'activity_type' => $row['name'] ?? null,
                'target_type' => 'both',
                'status' => 1,
                'created_at' => $row['created_at'] ?? null,
                'updated_at' => $row['updated_at'] ?? null,
            ];
            
            $chunk[] = $mappedRow;

            if (count($chunk) >= 500) {
                DB::transaction(function () use (&$chunk) {
                    DB::table('activity_types')->insertOrIgnore($chunk);
                });
                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            DB::transaction(function () use (&$chunk) {
                DB::table('activity_types')->insertOrIgnore($chunk);
            });
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $this->command->info("Processed {$processed} activity types in {$executionTime} seconds.");
        Schema::enableForeignKeyConstraints();
    }
}
