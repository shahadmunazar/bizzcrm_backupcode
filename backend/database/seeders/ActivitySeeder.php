<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;
use Throwable;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '-1'); // Prevent memory exhaustion for massive JSON files
        Schema::disableForeignKeyConstraints();
        DB::connection()->disableQueryLog();
        $this->command->newLine();
        $this->command->info("========================================================");
        $this->command->info("🚀 Starting Activities Data Import...");
        $this->command->info("📂 Source Table: bizzcrm_migrate.activities");
        $this->command->info("🗄️ Target Table: activities");
        $this->command->info("========================================================");
        $startTime = microtime(true);
        $this->command->info("📊 Streaming records from the database...");
        $totalRecords = 'Unknown';
        
        $stream = DB::connection('bizzcrm_migrate')->table('activities')->cursor();
        $chunk = [];
        $processed = 0;
        $batchSize = 20000;
        try {

            foreach ($stream as $item) {

                $row = (array) $item;
                $chunk[] = $row;

                if (count($chunk) >= $batchSize) {

                    DB::transaction(function () use ($chunk) {
                        // Calculate dynamic chunk size to safely bypass MySQL's 65,535 placeholder limit
                        $columnCount = count($chunk[0]);
                        $safeChunkSize = min(50, (int) floor(65000 / $columnCount));
                        
                        foreach (array_chunk($chunk, $safeChunkSize) as $subChunk) {
                            DB::table('activities')->insertOrIgnore($subChunk);
                        }
                    });

                    $processed += count($chunk);
                    $currentBatch = (int) ceil($processed / $batchSize);

                    $this->command->info(
                        "📦 Batch {$currentBatch} Imported | Records: "
                        . number_format($processed)
                        . " | Database Table: activities"
                    );

                    $chunk = [];

                    gc_collect_cycles();
                }
            }

            if (! empty($chunk)) {

                DB::transaction(function () use ($chunk) {
                        // Calculate dynamic chunk size to safely bypass MySQL's 65,535 placeholder limit
                        $columnCount = count($chunk[0]);
                        $safeChunkSize = min(50, (int) floor(65000 / $columnCount));
                        
                        foreach (array_chunk($chunk, $safeChunkSize) as $subChunk) {
                            DB::table('activities')->insertOrIgnore($subChunk);
                        }
                });

                $processed += count($chunk);
                $currentBatch = (int) ceil($processed / $batchSize);

                $this->command->info(
                    "📦 Batch {$currentBatch} Imported | Records: "
                    . number_format($processed)
                    . " | Database Table: activities"
                );
            }

            $time = round(microtime(true) - $startTime, 2);

            $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

            $this->command->newLine();
            $this->command->info("========================================================");
            $this->command->info("🎉 Activities Import Completed Successfully");
            $this->command->info("📊 Total Records Imported : " . number_format($processed));
            $this->command->info("⏱ Execution Time         : {$time} seconds");
            $this->command->info("💾 Peak Memory Usage     : {$memory} MB");
            $this->command->info("========================================================");

        } catch (Throwable $e) {

            $this->command->error("❌ Import Failed");
            $this->command->error($e->getMessage());

        } finally {

            Schema::enableForeignKeyConstraints();
        }
    }
}
