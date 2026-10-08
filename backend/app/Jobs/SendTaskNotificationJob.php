<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable; // Added
use Illuminate\Queue\InteractsWithQueue;    // Added
use Illuminate\Queue\SerializesModels;      // Added
use App\Models\Activity;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;

class SendTaskNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; 

    protected $activityId;
    protected $assignedUserId;

    /**
     * Create a new job instance.
     */
    public function __construct($activityId, $assignedUserId)
    {
        $this->activityId = $activityId;
        $this->assignedUserId = $assignedUserId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $activity = Activity::find($this->activityId);
        $user = User::find($this->assignedUserId);

        if ($activity && $user && $activity->activity_id) {
            $lead = Lead::find($activity->activity_id);
            if ($lead) {
                // Send notification
                $user->notify(new TaskAssignedNotification($activity, $lead));
            }
        }
    }
}
