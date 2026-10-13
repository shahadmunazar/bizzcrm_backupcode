<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewLeadNotification extends Notification
{
    use Queueable;

    protected $lead;
    protected $status;
    protected $senderName;
    protected $sourceName;

    public function __construct($lead, $status = 'assigned', $senderName = null, $sourceName = null)
    {
        $this->lead = $lead;
        $this->status = $status;
        $this->senderName = $senderName;
        $this->sourceName = $sourceName;
    }

    public function via($notifiable)
    {
        return ['database', \App\Channels\FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        $dbData = $this->toArray($notifiable);
        
        return [
            'title' => $dbData['title'],
            'body' => $dbData['message'],
            'data' => [
                'type' => $dbData['type'],
                'lead_id' => (string) $dbData['lead_id'],
                'lead_name' => $dbData['lead_name']
            ]
        ];
    }

    public function toArray($notifiable)
    {
        $leadName = $this->lead->name ?? 'Unknown Lead';
        $source = $this->sourceName ?: ($this->lead->leadSource ? $this->lead->leadSource->name : 'N/A');

        if ($this->status === 'duplicate') {
            return [
                'title' => 'Duplicate Lead Detected',
                'message' => "The lead {$leadName} is marked as a duplicate.",
                'lead_id' => $this->lead->id,
                'lead_name' => $leadName,
                'source' => $source,
                'type' => 'duplicate_lead',
                'icon' => 'ti-alert-circle',
                'bg_color' => 'bg-warning'
            ];
        }

        if ($this->status === 'restored') {
            return [
                'title' => 'Lead Restored',
                'message' => "The lead {$leadName} has been restored from trash.",
                'lead_id' => $this->lead->id,
                'lead_name' => $leadName,
                'source' => $source,
                'type' => 'lead_restored',
                'icon' => 'ti-recycle',
                'bg_color' => 'bg-info'
            ];
        }

        if ($this->status === 'trash') {
            return [
                'title' => 'Lead Moved to Trash',
                'message' => "The lead {$leadName} was automatically moved to trash.",
                'lead_id' => $this->lead->id,
                'lead_name' => $leadName,
                'source' => $source,
                'type' => 'lead_trash',
                'icon' => 'ti-trash',
                'bg_color' => 'bg-danger'
            ];
        }

        if ($this->status === 'transfer') {
            $msg = "The lead {$leadName} has been transferred to you";
            if ($this->senderName) {
                $msg .= " from {$this->senderName}.";
            } else {
                $msg .= ".";
            }

            return [
                'title' => 'Lead Transferred',
                'message' => $msg,
                'lead_id' => $this->lead->id,
                'lead_name' => $leadName,
                'source' => $source,
                'type' => 'lead_transfer',
                'icon' => 'ti-arrows-left-right',
                'bg_color' => 'bg-primary'
            ];
        }

        return [
            'title' => 'New Lead Assigned',
            'message' => "A new lead {$leadName} has been assigned to you.",
            'lead_id' => $this->lead->id,
            'lead_name' => $leadName,
            'source' => $source,
            'type' => 'lead_assigned',
            'icon' => 'ti-user-plus',
            'bg_color' => 'bg-success'
        ];
    }
}
