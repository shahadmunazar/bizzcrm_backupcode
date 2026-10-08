<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\SendNewLeadNotificationJob;

class BulkAssignLeadsJob implements ShouldQueue
{
    use Queueable;

    // Timeout increased to 1 hour to handle very large datasets
    public int $timeout = 3600;

    public function __construct(
        protected array $leadIds,
        protected ?int $assignedTo,
        protected int $userId
    ) {}

    public function handle(): void
    {
        if (empty($this->leadIds)) {
            return;
        }

        $recipientUser = $this->assignedTo ? User::find($this->assignedTo) : null;
        $newUserName = $recipientUser ? $recipientUser->name : 'Unassigned';

        Lead::whereIn('id', $this->leadIds)
            ->chunkById(500, function ($leads) use ($newUserName) { 

                // Pre-fetch old assignees for this chunk to avoid N+1
                $oldAssigneeIds = $leads->pluck('assigned_to')->filter()->unique();
                $oldUsers = User::whereIn('id', $oldAssigneeIds)->pluck('name', 'id');

                DB::transaction(function () use ($leads, $oldUsers, $newUserName) {
                    $now = Carbon::now();

                    // Using native foreach for maximum performance
                    foreach ($leads as $lead) {
                        $oldAssignedTo = $lead->assigned_to;
                        $oldUserName = $oldUsers[$oldAssignedTo] ?? 'Unassigned';

                        if ($oldAssignedTo == $this->assignedTo) {
                            continue;
                        }

                        $lead->update([
                            'last_assigned_to' => $oldAssignedTo,
                            'assigned_to'      => $this->assignedTo,
                            'last_updated_by'  => $this->userId,
                            'updated_at'       => $now,
                            'change_assigned_to_time' => $now,
                        ]);

                        Activity::create([
                            'box_id'          => $lead->box_id,
                            'activity_id'     => $lead->id,
                            'activity_type'   => 'leads',
                            'type'            => 'history',
                            'subject'         => 'Lead Updated',
                            'description'     => "Assigned to {$newUserName}",
                            'added_by'        => $this->userId,
                            'last_updated_by' => $this->userId,
                            'status'          => 'Close',
                            'completed_date'  => $now,
                            'created_at'      => $now,
                            'updated_at'      => $now,
                            'params'          => json_encode([
                                'previous_assignee_name' => $oldUserName,
                                'current_assignee_name' => $newUserName
                            ]),
                        ]);

                        if ($this->assignedTo) {
                            SendNewLeadNotificationJob::dispatch($lead->id, $this->assignedTo, 'transfer', $this->userId);
                        }
                    }
                });
            });
    }
}
