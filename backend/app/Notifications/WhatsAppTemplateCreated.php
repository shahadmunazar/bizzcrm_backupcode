<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppTemplateCreated extends Notification
{
    use Queueable;

    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    
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
        return [
            'template_id' => $this->template->id,
            'template_name' => $this->template->name,
            'status' => $this->template->whats_app_status ?? 'PENDING',
            'title' => "New WhatsApp Template Created",
            'message' => "A new WhatsApp template '{$this->template->name}' has been created and sent to Meta for approval.",
            'action_url' => box_route('whatsapp.create_template', ['box_slug' => $notifiable->box->slug ?? 'default'])
        ];
    }
}
