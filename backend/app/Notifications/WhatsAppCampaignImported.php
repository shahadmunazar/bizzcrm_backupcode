<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WhatsAppCampaignImported extends Notification
{
    use Queueable;

    protected $campaign;
    protected $totalRows;
    protected $createdLeadsCount;

    /**
     * Create a new notification instance.
     */
    public function __construct($campaign, $totalRows, $createdLeadsCount = 0)
    {
        $this->campaign = $campaign;
        $this->totalRows = $totalRows;
        $this->createdLeadsCount = $createdLeadsCount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
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
        $message = "WhatsApp Campaign '{$this->campaign->name}' processing is complete.";
        if ($this->createdLeadsCount > 0) {
            $message .= " {$this->createdLeadsCount} new leads were created and assigned to you.";
        }
        $message .= " Total recipients: {$this->totalRows}.";

        return [
            'title' => 'Campaign Processing Complete',
            'message' => $message,
            'campaign_id' => $this->campaign->id,
            'campaign_name' => $this->campaign->name,
            'total_rows' => $this->totalRows,
            'leads_created' => $this->createdLeadsCount,
            'type' => 'whatsapp_campaign_complete',
            'icon' => 'ti-brand-whatsapp',
            'bg_color' => 'bg-success'
        ];
    }
}
