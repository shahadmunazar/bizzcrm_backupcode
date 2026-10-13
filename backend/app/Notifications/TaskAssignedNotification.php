<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TaskAssignedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $activity;
    protected $lead;

    /**
     * Create a new notification instance.
     */
    public function __construct($activity, $lead)
    {
        $this->activity = $activity;
        $this->lead = $lead;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Assigned')
            ->line('You have been assigned a new task.')
            ->action('View Task', url('/'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
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

    public function toArray(object $notifiable): array
    {
        $activityType = ucfirst($this->activity->type);
        $leadName = $this->lead ? ($this->lead->name . ' ' . $this->lead->last_name) : 'Unknown Lead';

        return [
            'title'       => 'New Task Assigned',
            'message'     => "You have been assigned a new {$activityType} task for {$leadName}.",
            'activity_id' => $this->activity->id,
            'lead_id'     => $this->lead->id,
            'type'        => 'task_assigned',
            'icon'        => 'ti-check', // Example icon class
            'bg_color'    => 'bg-primary'
        ];
    }
}
