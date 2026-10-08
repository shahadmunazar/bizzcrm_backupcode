<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;
use Throwable;

class LeadsDataSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '-1'); // Prevent memory exhaustion for massive JSON files
        Schema::disableForeignKeyConstraints();
        DB::connection()->disableQueryLog();

        $this->command->newLine();
        $this->command->info("========================================================");
        $this->command->info("🚀 Starting Leads Data Import...");
        $this->command->info("📂 Source Table: bizzcrm_migrate.leads");
        $this->command->info("🗄️ Target Table: leads");
        $this->command->info("========================================================");

        $startTime = microtime(true);

        $this->command->info("📊 Streaming records from the database...");
        $totalRecords = 'Unknown';
        
        // Using cursor() to stream large datasets without hitting memory limits
        $stream = DB::connection('bizzcrm_migrate')->table('leads')->cursor();

        $chunk = [];
        $processed = 0;
        $batchSize = 5000;
        
        // Retrieve columns from the destination table
        $targetColumns = Schema::getColumnListing('leads');
        // Retrieve columns from the source table to ensure we only map what exists
        $sourceColumns = Schema::connection('bizzcrm_migrate')->getColumnListing('leads');
        
        // Build an automated mapping dynamically based on intersecting column names
        // You can manually override any mismatched fields here if needed.
        $mapping = [];
        foreach ($sourceColumns as $col) {
            if (in_array($col, $targetColumns)) {
                $mapping[$col] = $col; // Target => Source
            }
        }

        try {

            foreach ($stream as $item) {

                $row = (array) $item;
                $mappedRow = [];
                
                foreach ($mapping as $targetCol => $sourceCol) {
                    $mappedRow[$targetCol] = $row[$sourceCol] ?? null;
                }

                $chunk[] = $mappedRow;

                if (count($chunk) >= $batchSize) {

                    DB::transaction(function () use ($chunk) {
                        $safeChunkSize = 500;
                        
                        foreach (array_chunk($chunk, $safeChunkSize) as $subChunk) {
                            DB::table('leads')->insertOrIgnore($subChunk);
                        }
                    });

                    $processed += count($chunk);
                    $currentBatch = (int) ceil($processed / $batchSize);

                    $this->command->info(
                        "📦 Batch {$currentBatch} Imported | Records: "
                        . number_format($processed)
                        . " | Database Table: leads"
                    );

                    $chunk = [];

                    gc_collect_cycles();
                    
                    // Reconnect to prevent "MySQL server has gone away" timeout
                    DB::reconnect();
                }
            }

            if (! empty($chunk)) {

                DB::transaction(function () use ($chunk) {
                    $safeChunkSize = 500;
                    
                    foreach (array_chunk($chunk, $safeChunkSize) as $subChunk) {
                        DB::table('leads')->insertOrIgnore($subChunk);
                    }
                });

                $processed += count($chunk);
                $currentBatch = (int) ceil($processed / $batchSize);

                $this->command->info(
                    "📦 Batch {$currentBatch} Imported | Records: "
                    . number_format($processed)
                    . " | Database Table: leads"
                );
            }

            $time = round(microtime(true) - $startTime, 2);

            $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

            $this->command->newLine();
            $this->command->info("========================================================");
            $this->command->info("🎉 Leads Import Completed Successfully");
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