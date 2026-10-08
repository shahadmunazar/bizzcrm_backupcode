<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\BoxUser;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\SendNewLeadNotificationJob;

class BulkRestoreLeadsJob implements ShouldQueue
{
    use Queueable;

    // Timeout increased to 1 hour to handle very large datasets
    public int $timeout = 3600;

    public function __construct(
        protected array $leadIds,
        protected int $userId
    ) {}

    public function handle(): void
    {
        if (empty($this->leadIds)) {
            \Log::warning("BulkRestoreLeadsJob: No Lead IDs provided.");
            return;
        }

        $ids = array_map('intval', $this->leadIds);
        $user = User::find($this->userId);
        
        $boxUsers = BoxUser::with('user')
            ->where('user_id', $this->userId)
            ->get()
            ->keyBy('box_id');

        Lead::withTrashed()
            ->whereIn('id', $ids)
            ->chunkById(500, function ($leads) use ($boxUsers, $user) {
                
                $oldAssigneeIds = $leads->pluck('assigned_to')->filter()->unique();
                $oldUsers = User::whereIn('id', $oldAssigneeIds)->pluck('name', 'id');

                DB::transaction(function () use ($leads, $boxUsers, $user, $oldUsers) {
                    $now = Carbon::now();

                    // Using native foreach for maximum performance
                    foreach ($leads as $lead) {
                        try {
                            $oldAssignedTo = $lead->assigned_to;
                            $oldUserName = $oldUsers[$oldAssignedTo] ?? 'Unassigned';

                            // Resolve Restorer Name
                            $newUserName = 'Unassigned';
                            if ($boxUsers->has($lead->box_id)) {
                                $boxUser = $boxUsers[$lead->box_id];
                                $newUserName = $boxUser->user->name
                                    ?? trim($boxUser->first_name . ' ' . $boxUser->last_name);
                            } elseif ($user) {
                                $newUserName = $user->name;
                            }

                            if ($lead->trashed()) {
                                $lead->restore();
                            }

                            $lead->update([
                                'last_assigned_to' => $oldAssignedTo,
                                'assigned_to'      => $this->userId,
                                'change_assigned_to_time' => $now,
                                'last_updated_by'  => $this->userId,
                                'updated_at'       => $now,
                                'duplicate_leads'  => 0,
                                'trashed_at'       => null
                            ]);

                            // Add to lead assignment pivot and history
                            $lead->assignees()->attach($this->userId, ['assigned_by' => $this->userId]);
                            
                            \App\Models\LeadAssignmentHistory::create([
                                'lead_id'     => $lead->id,
                                'user_id'     => $this->userId,
                                'assigned_by' => $this->userId,
                                'action'      => 'restored',
                                'remarks'     => "Lead restored from trash by {$newUserName}",
                                'created_at'  => $now,
                                'updated_at'  => $now,
                            ]);

                            Activity::create([
                                'box_id'          => $lead->box_id,
                                'activity_id'     => $lead->id,
                                'activity_type'   => 'leads',
                                'type'            => 'history',
                                'subject'         => "{$newUserName} Lead is restored",
                                'description'     => "Lead restored from trash from {$oldUserName} to {$newUserName}",
                                'added_by'        => $this->userId,
                                'last_updated_by' => $this->userId,
                                'status'          => 'Close',
                                'completed_date'  => $now,
                                'created_at'      => $now,
                                'updated_at'      => $now,
                                'params'          => json_encode([]),
                            ]);

                            SendNewLeadNotificationJob::dispatch($lead->id, $this->userId, 'restored');

                        } catch (\Exception $e) {
                            \Log::error("BulkRestoreLeadsJob: Error processing individual ID {$lead->id}: " . $e->getMessage());
                            throw $e;
                        }
                    }
                });
            });
    }
}
