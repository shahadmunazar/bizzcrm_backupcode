<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FcmService;

class FcmChannel
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function send($notifiable, Notification $notification)
    {
        // Check if the notification supports FCM
        if (! method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if (!$message) {
            return;
        }

        // We assume the user has an 'id' that maps to created_by in device_registers
        $userId = $notifiable->id;

        $title = $message['title'] ?? 'New Notification';
        $body = $message['body'] ?? '';
        $data = $message['data'] ?? [];

        $this->fcmService->sendPush($userId, $title, $body, $data);
    }
}
