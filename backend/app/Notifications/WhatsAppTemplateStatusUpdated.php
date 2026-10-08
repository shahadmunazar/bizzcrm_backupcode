<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WhatsAppTemplateStatusUpdated extends Notification
{
    use Queueable;

    protected $template;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct($template, $oldStatus, $newStatus)
    {
        $this->template = $template;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Keeping it simple as requested, using internal notifications
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusText = strtolower($this->newStatus);
        return [
            'template_id' => $this->template->id,
            'template_name' => $this->template->name,
            'status' => $this->newStatus,
            'title' => "WhatsApp Template $statusText",
            'message' => "Your WhatsApp template '{$this->template->name}' has been {$statusText} by Meta.",
            'action_url' => box_route('whatsapp.create_template', ['box_slug' => $notifiable->box->slug ?? 'default'])
        ];
    }
}
