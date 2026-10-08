<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Activity;
use App\Models\CrmSource;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BulkUpdateLeadSourceJob implements ShouldQueue
{
    use Queueable;

    // Timeout increased to 1 hour to handle very large datasets
    public int $timeout = 3600;

    public function __construct(
        protected array $leadIds,
        protected int $sourceId,
        protected int $updatedBy
    ) {}

    public function handle(): void
    {
        if (empty($this->leadIds)) {
            return;
        }

        $newSource = CrmSource::find($this->sourceId);
        $newSourceName = $newSource ? $newSource->name : 'Unknown';

        Lead::whereIn('id', $this->leadIds)
            ->chunkById(500, function ($leads) use ($newSourceName) {

                // Pre-fetch old source names
                $oldSourceIds = $leads->pluck('lead_source_id')->filter()->unique();
                $oldSources = CrmSource::whereIn('id', $oldSourceIds)->pluck('name', 'id');

                DB::transaction(function () use ($leads, $oldSources, $newSourceName) {
                    $now = Carbon::now();

                    // Using native foreach for maximum performance
                    foreach ($leads as $lead) {
                        try {
                            $oldSourceId = $lead->lead_source_id;
                            $oldSourceName = $oldSources[$oldSourceId] ?? 'Unknown';

                            if ($oldSourceId == $this->sourceId) {
                                continue;
                            }

                            $lead->update([
                                'lead_source_id'  => $this->sourceId,
                                'last_updated_by' => $this->updatedBy,
                                'updated_at'      => $now,
                            ]);

                            Activity::create([
                                'box_id'          => $lead->box_id,
                                'activity_id'     => $lead->id,
                                'activity_type'   => 'leads',
                                'type'            => 'history',
                                'subject'         => 'Lead Source Updated',
                                'description'     => "Source changed from {$oldSourceName} to {$newSourceName}",
                                'added_by'        => $this->updatedBy,
                                'last_updated_by' => $this->updatedBy,
                                'status'          => 'Close',
                                'completed_date'  => $now,
                                'created_at'      => $now,
                                'updated_at'      => $now,
                                'params'          => json_encode([
                                    'previous_source_name' => $oldSourceName,
                                    'current_source_name' => $newSourceName
                                ]),
                            ]);

                        } catch (\Exception $e) {
                            \Log::error("BulkUpdateLeadSourceJob: Error processing Lead ID {$lead->id}: " . $e->getMessage());
                            throw $e;
                        }
                    }
                });
            });
    }
}
