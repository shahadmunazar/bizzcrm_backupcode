<?php

namespace App\Jobs;

use App\Mail\SubscriptionCancellationMail;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionCancellationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscriptionId;
    protected $reason;

    public function __construct($subscriptionId, $reason = null)
    {
        $this->subscriptionId = $subscriptionId;
        $this->reason = $reason;
    }

    public function handle()
    {
        $subscription = UserSubscription::find($this->subscriptionId);
        if (!$subscription) {
            return;
        }

        $user = User::find($subscription->user_id);
        $plan = Plan::find($subscription->plan_id);

        if ($user && $plan) {
            Mail::to($user->email)->send(new SubscriptionCancellationMail($user, $subscription, $plan, $this->reason));
        }
    }
}
