<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExportReadyNotification extends Notification
{
    use Queueable;

    public $filePath;
    public $type;
    public $boxSlug;

    /**
     * Create a new notification instance.
     */
    public function __construct($filePath, $type, $boxSlug)
    {
        $this->filePath = $filePath;
        $this->type = $type;
        $this->boxSlug = $boxSlug;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('biometric.export.download', ['box_slug' => $this->boxSlug, 'file' => basename($this->filePath)]);

        return (new MailMessage)
            ->subject('Biometric Data Export Ready')
            ->line("Your biometric machine data export ({$this->type}) is ready.")
            ->action('Download Export', $url)
            ->line('The link will expire in 24 hours.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Export Ready',
            'message' => "Your biometric data export ({$this->type}) is ready.",
            'file_path' => $this->filePath,
            'type' => 'export_ready',
            'icon' => 'ti-file-download',
            'bg_color' => 'bg-info',
            'link' => route('biometric.export.download', ['box_slug' => $this->boxSlug, 'file' => basename($this->filePath)])
        ];
    }
}
