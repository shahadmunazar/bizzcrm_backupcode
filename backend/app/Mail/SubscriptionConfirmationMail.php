<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $plan;
    public $company;

    public function __construct($user, $subscription, $plan, $company)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->company = $company;
    }

    public function build()
    {
        return $this->subject('Payment Received: Subscription Confirmed - ' . $this->plan->name)
            ->view('emails.subscription_confirmation');
    }
}
