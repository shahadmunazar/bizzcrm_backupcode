<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $plan;
    public $reason;

    public function __construct($user, $subscription, $plan, $reason = null)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Subscription Cancellation Confirmed - ' . $this->plan->name)
            ->view('emails.subscription_cancellation');
    }
}
