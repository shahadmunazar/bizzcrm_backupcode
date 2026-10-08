<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $plan;
    public $daysLeft;

    public function __construct($user, $subscription, $plan, $daysLeft = 3)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->daysLeft = $daysLeft;
    }

    public function build()
    {
        return $this->subject('Reminder: Your Subscription for ' . $this->plan->name . ' Expires in ' . $this->daysLeft . ' Days')
            ->view('emails.subscription_expiry_reminder');
    }
}
