<?php

namespace App\Jobs;

use App\Helpers\LeadFieldsHelper;
use App\Models\SystemField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncLeadFieldsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $boxId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($boxId)
    {
        $this->boxId = $boxId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("SyncLeadFieldsJob started for Box ID: {$this->boxId}");
        try {
            $standardFields = LeadFieldsHelper::getFields();
            $existingFields = SystemField::where('box_id', $this->boxId)
                ->where('object', 'leads')
                ->get()
                ->keyBy('field');
            $processedFields = [];
            foreach ($standardFields as $fieldConfig) {
                // Ensure box_id is set
                $fieldConfig['box_id'] = $this->boxId;
                $fieldName = $fieldConfig['field'];
                if ($existingFields->has($fieldName)) {
                    // Update existing
                    $existingField = $existingFields->get($fieldName);
                    $existingField->update($fieldConfig);
                    $processedFields[] = $fieldName;
                }
                else {
                    // Create new
                    SystemField::create($fieldConfig);
                    $processedFields[] = $fieldName;
                }
            }

            // Identify extra fields to delete
            // Any field in $existingFields that was NOT processed is an extra field
            $extraFields = $existingFields->except($processedFields);

            if ($extraFields->isNotEmpty()) {
                Log::info("Deleting extra fields for Box ID {$this->boxId}: " . $extraFields->keys()->implode(', '));
                // Delete them
                SystemField::whereIn('id', $extraFields->pluck('id'))->delete();
            }

            Log::info("SyncLeadFieldsJob completed for Box ID: {$this->boxId}");

        }
        catch (\Exception $e) {
            Log::error("SyncLeadFieldsJob failed for Box ID {$this->boxId}: " . $e->getMessage());
            throw $e;
        }
    }
}
