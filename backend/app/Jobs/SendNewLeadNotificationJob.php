<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadNotification;

class SendNewLeadNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $leadId;
    protected $assignedUserId;
    protected $status;
    protected $senderId;
    protected $sourceName;

    /**
     * Create a new job instance.
     */
    public function __construct($leadId, $assignedUserId, $status = 'assigned', $senderId = null, $sourceName = null)
    {
        $this->leadId = $leadId;
        $this->assignedUserId = $assignedUserId;
        $this->status = $status;
        $this->senderId = $senderId;
        $this->sourceName = $sourceName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info("SendNewLeadNotificationJob started for Lead ID: {$this->leadId}, User ID: {$this->assignedUserId}, Status: {$this->status}");

        $lead = Lead::withTrashed()->with('leadSource')->find($this->leadId);
        $user = User::find($this->assignedUserId);

        $senderName = null;
        if ($this->senderId) {
             $sender = User::find($this->senderId);
             if ($sender) {
                 $senderName = $sender->name;
             }
        }

        if ($lead && $user) {
            // Send notification
            $user->notify(new NewLeadNotification($lead, $this->status, $senderName, $this->sourceName));
            \Log::info("Notification sent to User ID: {$user->id}");
        } else {
            \Log::warning("SendNewLeadNotificationJob failed: Lead or User not found.", [
                'lead_found' => (bool)$lead,
                'user_found' => (bool)$user
            ]);
        }
    }
}
