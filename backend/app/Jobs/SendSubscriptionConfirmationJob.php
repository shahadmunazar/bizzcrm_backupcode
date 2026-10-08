<?php

namespace App\Jobs;

use App\Mail\SubscriptionConfirmationMail;
use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscriptionId;

    public function __construct($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    public function handle()
    {
        $subscription = UserSubscription::find($this->subscriptionId);
        if (!$subscription) {
            return;
        }

        $user = User::find($subscription->user_id);
        $plan = Plan::find($subscription->plan_id);
        $company = Company::where('box_id', $subscription->box_id)->first();

        if ($user && $plan) {
            Mail::to($user->email)->send(new SubscriptionConfirmationMail($user, $subscription, $plan, $company));
        }
    }
}
