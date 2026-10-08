<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiryReminderMail;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendSubscriptionExpiryReminders extends Command
{
    protected $signature = 'subscription:expiry-reminders';
    protected $description = 'Send email reminders to users whose subscriptions are expiring in 3 days';

    public function handle()
    {
        $this->info('Finding subscriptions expiring in 3 days...');

        // Targets: Subscriptions expiring in exactly 3 days from now
        $targetDate = Carbon::today()->addDays(3);

        $expiringSubscriptions = UserSubscription::whereIn('status', ['active', 'cancelled'])
            ->where(function ($query) use ($targetDate) {
                // Check both potential expiry date columns
                $query->whereDate('next_billing_at', $targetDate)
                      ->orWhereDate('ends_at', $targetDate);
            })
            ->with(['user', 'plan'])
            ->get();

        $count = 0;
        foreach ($expiringSubscriptions as $subscription) {
            if ($subscription->user && $subscription->plan) {
                try {
                    Mail::to($subscription->user->email)->send(
                        new SubscriptionExpiryReminderMail($subscription->user, $subscription, $subscription->plan, 3)
                    );
                    $count++;
                    $this->info("Reminder sent to: " . $subscription->user->email);
                } catch (\Exception $e) {
                    $this->error("Failed to send email to " . $subscription->user->email . ": " . $e->getMessage());
                    Log::error("Subscription Reminder Error: " . $e->getMessage());
                }
            }
        }

        $this->info("Successfully sent $count subscription expiry reminders.");
    }
}
