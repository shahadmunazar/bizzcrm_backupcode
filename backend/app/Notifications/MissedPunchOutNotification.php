<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MissedPunchOutNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $attendanceDate;
    protected $expectedPunchOut;
    protected $boxUserName;

    public function __construct($attendanceDate, $expectedPunchOut, $boxUserName = null)
    {
        $this->attendanceDate = $attendanceDate;
        $this->expectedPunchOut = $expectedPunchOut;
        $this->boxUserName = $boxUserName;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', \App\Channels\FcmChannel::class];
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

    public function toArray($notifiable)
    {
        $formattedDate = \Carbon\Carbon::parse($this->attendanceDate)->format('D, M d, Y');
        $formattedTime = \Carbon\Carbon::parse($this->expectedPunchOut)->format('h:i A');

        return [
            'title' => 'Missed Punch Out',
            'message' => "You missed your punch out on {$formattedDate}. Auto punch out triggered at {$formattedTime}.",
            'type' => 'missed_punch_out',
            'icon' => 'ti-clock-exclamation',
            'bg_color' => 'bg-danger'
        ];
    }
    
    public function toBroadcast(object $notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
