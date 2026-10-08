<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ContactStage;
use App\Models\Lead;
use App\Jobs\SendTaskNotificationJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckNoActivityAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:check-no-activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check leads for inactivity based on status settings and create alert activities.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('[' . now()->toDateTimeString() . '] Starting inactivity check...');

        try {
            // 1. Get all contact stages that have an alert threshold set and notification enabled
            $stages = ContactStage::where('no_activity_alert', '>', 0)
                ->whereIn('notification', ['Yes', 'yes'])
                ->where('status', 1)
                ->get();

            if ($stages->isEmpty()) {
                $this->info('No lead statuses found with activity alert settings.');
                return Command::SUCCESS;
            }

            foreach ($stages as $stage) {
                $value = $stage->no_activity_alert;
                $unit = strtolower($stage->no_activity_alert_unit ?? 'Days');

                // 2. Calculate the threshold timestamp
                $threshold = match ($unit) {
                        'minutes' => now()->subMinutes($value),
                        'hours' => now()->subHours($value),
                        default => now()->subDays($value),
                    };

                // 3. Find leads with this status and box_id that haven't been touched since the threshold
                // We use withTrashed(false) or just query the main table
                $leads = Lead::where('lead_status_id', $stage->id)
                    ->where('box_id', $stage->box_id)
                    ->get();

                foreach ($leads as $lead) {
                    // 4. Get the last activity for this lead
                    $lastActivity = Activity::where('activity_id', $lead->id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $lastTime = $lastActivity ? $lastActivity->created_at : $lead->created_at;

                    // 5. If the last change was before our threshold, create an alert activity
                    if ($lastTime->lt($threshold)) {

                        // 6. Check if the VERY LAST activity for this lead is already an inactivity alert
                        // This prevents creating a new alert every time the cron runs if the lead is still inactive.
                        if ($lastActivity && $lastActivity->subject === 'NO_ACTIVITY_ALERT') {
                            continue;
                        }

                        // 7. Create the activity
                        $alertActivity = Activity::create([
                            'box_id' => $lead->box_id,
                            'activity_id' => $lead->id,
                            'type' => 'history',
                            'subject' => 'NO_ACTIVITY_ALERT',
                            'description' => "System Alert: No activity recorded for {$value} {$unit} while in status '{$stage->name}'.",
                            'activity_date' => now(),
                            'assigned_to' => $lead->assigned_to,
                            'added_by' => null,
                            'status' => 'Close',
                            'completed_date' => now(),
                            'params' => json_encode([
                                'alert_threshold' => $value,
                                'alert_unit' => $unit,
                                'stage_name' => $stage->name,
                                'triggered_at' => now()->toDateTimeString(),
                            ]),
                        ]);

                        // 8. If Notification setting is 'Yes' for this stage, notify the assigned user
                        if (strtolower($stage->notification) === 'yes' && $lead->assigned_to) {
                            SendTaskNotificationJob::dispatch($alertActivity->id, $lead->assigned_to);
                            $this->info("Dispatched notification for Lead #{$lead->id} to User #{$lead->assigned_to}");
                        }

                        $this->info("Created alert record for Lead #{$lead->id} in Box #{$lead->box_id}");
                        Log::info("Inactivity alert triggered", [
                            'lead_id' => $lead->id,
                            'box_id' => $lead->box_id,
                            'status' => $stage->name,
                            'last_activity' => $lastTime->toDateTimeString()
                        ]);
                    }
                }
            }

            $this->info('[' . now()->toDateTimeString() . '] Inactivity check completed successfully.');
            return Command::SUCCESS;

        }
        catch (\Exception $e) {
            $this->error('Error during inactivity check: ' . $e->getMessage());
            Log::error('Lead Inactivity Cron Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
