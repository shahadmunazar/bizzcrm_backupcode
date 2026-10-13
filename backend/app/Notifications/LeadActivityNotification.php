<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Lead;

class LeadActivityNotification extends Notification
{
    use Queueable;

    public $lead;
    public $activityType;
    public $message;
    public $performedByUserId;
    public $performedByUserName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Lead $lead, $activityType, $message, $performedByUserId, $performedByUserName)
    {
        $this->lead = $lead;
        $this->activityType = $activityType;
        $this->message = $message;
        $this->performedByUserId = $performedByUserId;
        $this->performedByUserName = $performedByUserName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', \App\Channels\FcmChannel::class]; // Can add broadcast later if configured
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    
    public function toFcm($notifiable)
    {
        $dbData = $this->toArray($notifiable);
        
        $data = [];
        foreach ($dbData as $key => $val) {
            if (!in_array($key, ['title', 'message', 'icon', 'bg_color'])) {
                $data[$key] = is_string($val) || is_numeric($val) ? (string)$val : json_encode($val);
            }
        }
        
        return [
            'title' => $dbData['title'] ?? 'Notification',
            'body' => $dbData['message'] ?? '',
            'data' => $data
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'lead_id' => $this->lead->id,
            'lead_name' => trim(($this->lead->first_name ?? '') . ' ' . ($this->lead->last_name ?? '')),
            'activity_type' => $this->activityType,
            'title' => 'Starred Lead Update',
            'bg_color' => 'bg-warning',
            'source' => $this->lead->source->name ?? 'N/A',
            'message' => $this->message,
            'performed_by_user_id' => $this->performedByUserId,
            'performed_by_user_name' => $this->performedByUserName,
        ];
    }
}
