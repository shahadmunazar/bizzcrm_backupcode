<?php

namespace App\Console\Commands;

use App\Models\Box;
use App\Models\SystemField;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncSystemFieldsForAllBoxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:system-fields-all {--box_id= : Specific box ID to sync} {--force : Force sync even if fields exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync system fields for all boxes or a specific box';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting System Fields Sync...');
        $this->newLine();

        $boxId = $this->option('box_id');
        $force = $this->option('force');

        if ($boxId) {
            // Sync specific box
            $box = Box::find($boxId);
            if (!$box) {
                $this->error("❌ Box with ID {$boxId} not found!");
                return 1;
            }

            $this->info("📦 Syncing fields for Box: {$box->name} (ID: {$box->id})");
            $result = $this->syncFieldsForBox($box, $force);
            $this->displayResults($result, $box->name);
        }
        else {
            // Sync all boxes
            $boxes = Box::all();
            $totalBoxes = $boxes->count();

            if ($totalBoxes === 0) {
                $this->warn('⚠️  No boxes found in the system.');
                return 0;
            }

            $this->info("📦 Found {$totalBoxes} box(es) to sync");
            $this->newLine();

            $totalAdded = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;

            $progressBar = $this->output->createProgressBar($totalBoxes);
            $progressBar->start();

            foreach ($boxes as $box) {
                $result = $this->syncFieldsForBox($box, $force);
                $totalAdded += $result['added'];
                $totalUpdated += $result['updated'];
                $totalSkipped += $result['skipped'];
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Display summary
            $this->info('═══════════════════════════════════════');
            $this->info('           SYNC SUMMARY');
            $this->info('═══════════════════════════════════════');
            $this->line("📦 Total Boxes Processed: <fg=cyan>{$totalBoxes}</>");
            $this->line("➕ Fields Added: <fg=green>{$totalAdded}</>");
            $this->line("🔄 Fields Updated: <fg=yellow>{$totalUpdated}</>");
            $this->line("⏭️  Fields Skipped: <fg=blue>{$totalSkipped}</>");
            $this->info('═══════════════════════════════════════');
        }

        $this->newLine();
        $this->info('✅ System Fields Sync Completed!');

        Log::info('System fields sync completed', [
            'boxes_processed' => $boxId ? 1 : $totalBoxes,
            'fields_added' => $totalAdded ?? 0,
            'fields_updated' => $totalUpdated ?? 0,
            'fields_skipped' => $totalSkipped ?? 0,
        ]);

        return 0;
    }

    /**
     * Sync fields for a specific box
     */
    protected function syncFieldsForBox(Box $box, bool $force = false): array
    {
        $standardFields = $this->getStandardSystemFields();
        $existingFields = SystemField::where('box_id', $box->id)
            ->where('object', 'leads')
            ->get()
            ->keyBy('field');

        $added = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($standardFields as $standardField) {
                $fieldKey = $standardField['field'];

                if ($existingFields->has($fieldKey)) {
                    // Field exists - check if update needed
                    $existingField = $existingFields->get($fieldKey);

                    if ($force || $this->shouldUpdateField($existingField, $standardField)) {
                        // Update only specific attributes, preserving sequence
                        $updateData = [
                            'name' => $standardField['name'],
                            'field_type' => $standardField['field_type'],
                            'validation_rules' => $standardField['validation_rules'],
                            'is_required' => $standardField['is_required'],
                            'placeholder' => $standardField['placeholder'] ?? null,
                            'is_visible' => $standardField['is_visible'],
                            'is_editable' => $standardField['is_editable'],
                        ];

                        // Only include options if present
                        if (isset($standardField['options'])) {
                            $updateData['options'] = $standardField['options'];
                        }

                        $existingField->update($updateData);
                        $updated++;
                    }
                    else {
                        $skipped++;
                    }
                }
                else {
                    // Field doesn't exist - add it with default sequence at the end
                    // Get max sequence for this box to append new field
                    $maxSequence = SystemField::where('box_id', $box->id)
                        ->where('object', 'leads')
                        ->max('sequence') ?? 0;

                    $fieldData = array_merge($standardField, [
                        'box_id' => $box->id,
                        'sequence' => $maxSequence + 1, // Add at the end
                    ]);

                    SystemField::create($fieldData);
                    $added++;
                }
            }

            DB::commit();

            return [
                'added' => $added,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to sync fields for box {$box->id}: " . $e->getMessage());

            return [
                'added' => 0,
                'updated' => 0,
                'skipped' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if field should be updated
     * Note: Sequence is NOT checked - boxes can have their own field ordering
     */
    protected function shouldUpdateField($existingField, $standardField): bool
    {
        // Compare key attributes to determine if update is needed
        // Deliberately excluding 'sequence' to allow boxes to customize field order
        $compareKeys = ['name', 'field_type', 'validation_rules', 'is_required', 'placeholder'];

        foreach ($compareKeys as $key) {
            $standardValue = $standardField[$key] ?? null;
            if ($existingField->$key !== $standardValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * Display results for a single box
     */
    protected function displayResults(array $result, string $boxName): void
    {
        $this->newLine();
        $this->info("Results for '{$boxName}':");
        $this->line("  ➕ Added: <fg=green>{$result['added']}</>");
        $this->line("  🔄 Updated: <fg=yellow>{$result['updated']}</>");
        $this->line("  ⏭️  Skipped: <fg=blue>{$result['skipped']}</>");

        if (isset($result['error'])) {
            $this->error("  ❌ Error: {$result['error']}");
        }
    }

    protected function getStandardSystemFields(): array
    {
        return \App\Helpers\LeadFieldsHelper::getFields();
    }
}

