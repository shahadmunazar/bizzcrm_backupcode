<?php

namespace App\Console\Commands;

use App\Models\Box;
use App\Models\SystemField;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMissingSystemFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:system-fields {--box_id= : Specific box ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync missing system fields for all boxes or a specific box';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting system fields synchronization...');

        $boxId = $this->option('box_id');

        if ($boxId) {
            $boxes = Box::where('id', $boxId)->get();
            if ($boxes->isEmpty()) {
                $this->error("Box with ID {$boxId} not found!");
                return 1;
            }
        } else {
            $boxes = Box::all();
        }

        if ($boxes->isEmpty()) {
            $this->warn('No boxes found in the database.');
            return 0;
        }

        $this->info("Found {$boxes->count()} box(es) to process.");

        $totalAdded = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($boxes as $box) {
            $this->info("\nProcessing Box ID: {$box->id} - {$box->name}");
            $result = $this->syncFieldsForBox($box);
            $totalAdded += $result['added'];
            $totalUpdated += $result['updated'];
            $totalSkipped += $result['skipped'];
        }

        $this->info("\n✅ System fields synchronization completed!");
        $this->info("Grand Total: Added={$totalAdded}, Updated={$totalUpdated}, Skipped={$totalSkipped}");
        return 0;
    }
    
    /**
     * Sync fields for a specific box (static method for use in other classes)
     * 
     * @param int|Box $box Box ID or Box model instance
     * @return array Stats array with added, updated, skipped counts
     */
    public static function syncForBox($box): array
    {
        if (is_numeric($box)) {
            $box = Box::find($box);
        }
        
        if (!$box) {
            return ['added' => 0, 'updated' => 0, 'skipped' => 0, 'error' => 'Box not found'];
        }

        $command = new self();
        return $command->syncFieldsForBox($box, false);
    }

    /**
     * Sync fields for a specific box
     * 
     * @param Box $box The box to sync fields for
     * @param bool $verbose Whether to output detailed logs
     * @return array Stats array with added, updated, skipped counts
     */
    protected function syncFieldsForBox(Box $box, bool $verbose = true): array
    {
        // Define all required system fields
        $requiredFields = $this->getRequiredSystemFields();

        // Get existing fields for this box
        $existingFields = SystemField::where('box_id', $box->id)
            ->where('object', 'leads')
            ->pluck('field')
            ->toArray();

        $addedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        // If box already has all fields, skip completely
        $missingFields = array_diff(
            array_column($requiredFields, 'field'),
            $existingFields
        );

        if (empty($missingFields) && count($existingFields) >= count($requiredFields)) {
            if ($verbose) {
                $this->info("  ✓ All fields present, skipping...");
            }
            return [
                'added' => 0,
                'updated' => 0,
                'skipped' => count($requiredFields)
            ];
        }

        foreach ($requiredFields as $fieldData) {
            $fieldData['box_id'] = $box->id;
            
            // Check if field already exists
            $existingField = SystemField::where('box_id', $box->id)
                ->where('object', 'leads')
                ->where('field', $fieldData['field'])
                ->first();

            if ($existingField) {
                // Check if update is actually needed by comparing key attributes
                $needsUpdate = false;
                $compareFields = ['name', 'field_name', 'field_type', 'field_refernce', 'field_refernce_key', 'sequence', 'is_visible'];
                
                foreach ($compareFields as $compareField) {
                    if (isset($fieldData[$compareField]) && $existingField->$compareField != $fieldData[$compareField]) {
                        $needsUpdate = true;
                        break;
                    }
                }

                if ($needsUpdate) {
                    $existingField->update($fieldData);
                    if ($verbose) {
                        $this->line("  ↻ Updated: {$fieldData['name']} ({$fieldData['field']})");
                    }
                    $updatedCount++;
                } else {
                    $skippedCount++;
                }
            } else {
                // Create new field
                SystemField::create($fieldData);
                if ($verbose) {
                    $this->line("  + Added: {$fieldData['name']} ({$fieldData['field']})");
                }
                $addedCount++;
            }
        }

        if ($verbose) {
            $this->info("  Summary: Added={$addedCount}, Updated={$updatedCount}, Skipped={$skippedCount}");
        }

        return [
            'added' => $addedCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount
        ];
    }

    /**
     * Get all required system fields
     */
    protected function getRequiredSystemFields()
    {
        return \App\Helpers\LeadFieldsHelper::getFields();
    }
}
