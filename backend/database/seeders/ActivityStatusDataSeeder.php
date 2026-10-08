<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;

class ActivityStatusDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $activityStatuses = DB::connection('bizzcrm_migrate')->table('activity_statuses')->get();
        $this->command->info('Starting Activity Statuses Seeder from DB...');
        $startTime = microtime(true);

        $chunk = [];
        $processed = 0;

        foreach ($activityStatuses as $item) {
            $row = (array) $item;
            $processed++;
            
            $mappedRow = [
                'id' => $row['id'] ?? null,
                'activity_setting_id' => null,
                'activity_type_id' => null,
                'box_id' => $row['box_id'] ?? null,
                'updated_by' => $row['last_updated_by'] ?? ($row['added_by'] ?? null),
                'activity_status' => $row['name'] ?? null,
                'status' => 1,
                'deleted_at' => $row['deleted_at'] ?? null,
                'created_at' => $row['created_at'] ?? null,
                'updated_at' => $row['updated_at'] ?? null,
            ];
            
            $chunk[] = $mappedRow;

            if (count($chunk) >= 500) {
                DB::transaction(function () use (&$chunk) {
                    DB::table('activity_statuses')->insertOrIgnore($chunk);
                });
                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            DB::transaction(function () use (&$chunk) {
                DB::table('activity_statuses')->insertOrIgnore($chunk);
            });
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $this->command->info("Processed {$processed} activity statuses in {$executionTime} seconds.");
        Schema::enableForeignKeyConstraints();
    }
}
