<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Activity;
use App\Models\ContactStage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BulkUpdateLeadStatusJob implements ShouldQueue
{
    use Queueable;

    // Timeout increased to 1 hour to handle very large datasets
    public int $timeout = 3600;

    public function __construct(
        protected array $leadIds,
        protected int $statusId,
        protected int $updatedBy
    ) {}

    public function handle(): void
    {
        if (empty($this->leadIds)) {
            return;
        }

        $newStatus = ContactStage::find($this->statusId);
        $newStatusName = $newStatus ? $newStatus->name : 'Unknown';

        Lead::whereIn('id', $this->leadIds)
            ->chunkById(500, function ($leads) use ($newStatusName) {

                // Pre-fetch old status names
                $oldStatusIds = $leads->pluck('lead_status_id')->filter()->unique();
                $oldStatuses = ContactStage::whereIn('id', $oldStatusIds)->pluck('name', 'id');

                DB::transaction(function () use ($leads, $oldStatuses, $newStatusName) {
                    $now = Carbon::now();

                    // Using native foreach for maximum performance
                    foreach ($leads as $lead) {
                        try {
                            $oldStatusId = $lead->lead_status_id;
                            $oldStatusName = $oldStatuses[$oldStatusId] ?? 'Unknown';

                            if ($oldStatusId == $this->statusId) {
                                continue;
                            }

                            // Use withoutEvents to prevent double history recording from model events
                            $lead->withoutEvents(function () use ($lead, $oldStatusId, $now) {
                                $lead->update([
                                    'last_lead_status' => $oldStatusId,
                                    'lead_status_id'   => $this->statusId,
                                    'last_updated_by'  => $this->updatedBy,
                                    'updated_at'       => $now,
                                    'change_lead_status_time' => $now,
                                ]);
                            });

                            // Record manual status history (Explicit and reliable in queue context)
                            DB::table('lead_status_histories')->insert([
                                'box_id'        => $lead->box_id,
                                'lead_id'       => $lead->id,
                                'old_status_id' => $oldStatusId,
                                'new_status_id' => $this->statusId,
                                'changed_by'    => $this->updatedBy,
                                'created_at'    => $now,
                                'updated_at'    => $now,
                            ]);

                            Activity::create([
                                'box_id'          => $lead->box_id,
                                'activity_id'     => $lead->id,
                                'activity_type'   => 'leads',
                                'type'            => 'history',
                                'subject'         => 'Lead Status Updated',
                                'description'     => "Status changed from {$oldStatusName} to {$newStatusName}",
                                'added_by'        => $this->updatedBy,
                                'last_updated_by' => $this->updatedBy,
                                'status'          => 'Close',
                                'completed_date'  => $now,
                                'created_at'      => $now,
                                'updated_at'      => $now,
                                'params'          => json_encode([
                                    'previous_status_name' => $oldStatusName,
                                    'current_status_name' => $newStatusName
                                ]),
                            ]);

                        } catch (\Exception $e) {
                            \Log::error("BulkUpdateLeadStatusJob: Error processing Lead ID {$lead->id}: " . $e->getMessage());
                            throw $e;
                        }
                    }
                });
            });
    }
}
