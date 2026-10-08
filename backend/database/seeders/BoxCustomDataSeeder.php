<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonMachine\Items;
use Throwable;

class BoxCustomDataSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '-1');
        Schema::disableForeignKeyConstraints();
        DB::connection()->disableQueryLog();

        $this->command->info("========================================================");
        $this->command->info("🚀 Starting Box Custom Data Import from DB...");
        $this->command->info("========================================================");

        // Fetch all tables starting with 'data_' from the source database
        $tables = DB::connection('bizzcrm_migrate')->select("SHOW TABLES LIKE 'data\_%'");
        $databaseName = config('database.connections.bizzcrm_migrate.database');
        $keyName = "Tables_in_{$databaseName} (data\_%)";

        if (empty($tables)) {
            $this->command->warn("⚠️ No data_* tables found in source database!");
            return;
        }

        foreach ($tables as $tableObj) {
            $tableName = $tableObj->$keyName ?? current((array)$tableObj);

            if (!Schema::hasTable($tableName)) {
                $this->command->warn("⚠️ Target table {$tableName} does not exist. Skipping...");
                continue;
            }

            $this->command->info("📂 Processing Table: {$tableName}");
            $startTime = microtime(true);

            try {
                $targetColumns = Schema::getColumnListing($tableName);
                $stream = DB::connection('bizzcrm_migrate')->table($tableName)->cursor();
                $chunk = [];
                $processed = 0;
                $batchSize = 20000;
                $columnMap = []; // cache the mappings for the table

                foreach ($stream as $item) {
                    $row = (array) $item;

                    // Determine column mapping on the first row
                    if (empty($columnMap) && !empty($row)) {
                        $sourceColumns = array_keys($row);
                        foreach ($sourceColumns as $sc) {
                            if (in_array($sc, $targetColumns)) {
                                $columnMap[$sc] = $sc;
                            } else {
                                // Find closest match
                                $bestMatch = null;
                                $bestScore = -1;
                                foreach ($targetColumns as $tc) {
                                    if (in_array($tc, ['id', 'lead_id', 'created_at', 'updated_at'])) continue;
                                    
                                    similar_text($sc, $tc, $percent);
                                    // if it's highly similar (e.g., poperty_type vs property_type)
                                    if ($percent > 85 && $percent > $bestScore) {
                                        $bestScore = $percent;
                                        $bestMatch = $tc;
                                    }
                                }
                                if ($bestMatch) {
                                    $columnMap[$sc] = $bestMatch;
                                }
                            }
                        }
                    }

                    $mappedRow = [];
                    foreach ($row as $key => $value) {
                        if (isset($columnMap[$key])) {
                            $mappedRow[$columnMap[$key]] = $value;
                        }
                    }

                    // Only add if it has data
                    if (!empty($mappedRow)) {
                        $chunk[] = $mappedRow;
                    }

                    if (count($chunk) >= $batchSize) {
                        DB::transaction(function () use ($chunk, $tableName) {
                            $columnCount = count($chunk[0]);
                            $safeChunkSize = $columnCount > 0 ? (int) floor(65000 / $columnCount) : 500;
                            
                            foreach (array_chunk($chunk, $safeChunkSize) as $subChunk) {
                                DB::table($tableName)->insertOrIgnore($subChunk);
                            }
                        });

                        $processed += count($chunk);
                        $currentBatch = (int) ceil($processed / $batchSize);
                        $this->command->info("📦 Batch {$currentBatch} Imported | Records: " . number_format($processed));
                        $chunk = [];
                        gc_collect_cycles();
                    }
                }

                if (!empty($chunk)) {
                    DB::transaction(function () use ($chunk, $tableName) {
                        $columnCount = count($chunk[0]);
                        $safeChunkSize = $columnCount > 0 ? (int) floor(65000 / $columnCount) : 500;
                        
                        foreach (array_chunk($chunk, $safeChunkSize) as $subChunk) {
                            DB::table($tableName)->insertOrIgnore($subChunk);
                        }
                    });
                    $processed += count($chunk);
                }

                $time = round(microtime(true) - $startTime, 2);
                $this->command->info("✅ Finished {$tableName} | Total Records: " . number_format($processed) . " | Time: {$time}s");
            } catch (Throwable $e) {
                $this->command->error("❌ Import Failed for {$tableName}: " . $e->getMessage());
            }
        }

        Schema::enableForeignKeyConstraints();
        $this->command->info("========================================================");
        $this->command->info("🎉 All Box Custom Data Imported Successfully");
        $this->command->info("========================================================");
    }
}
 