<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateBoxCustomTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:generate-box-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dynamic custom tables for each box based on box_custom_fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting dynamic table generation...");

        $fields = \Illuminate\Support\Facades\DB::table('box_custom_fields')
                    ->where('status', 'Enabled')
                    ->get();

        $groupedFields = $fields->groupBy('box_id');

        foreach ($groupedFields as $boxId => $boxFields) {
            $tableName = "data_{$boxId}";

            if (!\Illuminate\Support\Facades\Schema::hasTable($tableName)) {
                $this->info("Creating table: {$tableName}");
                \Illuminate\Support\Facades\Schema::create($tableName, function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('lead_id')->unique(); // Enforce unique lead_id to prevent duplicates
                    $table->timestamps();
                });
            }

            \Illuminate\Support\Facades\Schema::table($tableName, function (\Illuminate\Database\Schema\Blueprint $table) use ($tableName, $boxFields) {
                // Ensure timestamps exist
                if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                    $this->line(" - Added timestamps to {$tableName}");
                }

                foreach ($boxFields as $field) {
                    $colName = $field->col_name;

                    // Check if column already exists
                    if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, $colName)) {
                        
                        // Determine column type
                        if ($field->type === 'textarea') {
                            $table->text($colName)->nullable();
                        } elseif ($field->type === 'input' && strpos($colName, 'date') !== false) {
                            $table->date($colName)->nullable();
                        } else {
                            $table->string($colName)->nullable();
                        }
                        
                        $this->line(" - Added column: {$colName} to {$tableName}");
                    }
                }
            });
        }

        $this->info("✅ Finished generating dynamic tables for all boxes!");
    }
}
