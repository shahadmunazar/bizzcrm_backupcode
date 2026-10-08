<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignNumber;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppPhoneNumber;
use App\Helpers\WhatsApp;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:process-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending WhatsApp campaigns and send messages with daily limits';

    protected $batchSize = 1000; // Total messages to send in one cron execution

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting WhatsApp campaign processing...');

        // Find campaigns that are in SENDING status or newly IMPORTED
        // We include SENDING to continue partially sent campaigns
        $campaigns = WhatsAppCampaign::whereIn('status', ['SENDING', 'IMPORTED'])
            ->orderBy('updated_at', 'asc')
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No active campaigns found.');
            return 0;
        }

        $totalSentInThisRun = 0;

        foreach ($campaigns as $campaign) {
            if ($totalSentInThisRun >= $this->batchSize) break;

            $this->info("Processing campaign: {$campaign->name} (ID: {$campaign->id})");
            
            $box = $campaign->box;
            $waHelper = new WhatsApp($box);
            
            // Get sender number
            $senderId = $campaign->sender_id;
            $whatsappNumber = null;
            if ($senderId) {
                $whatsappNumber = WhatsAppPhoneNumber::find($senderId);
            } else {
                $whatsappNumber = WhatsAppPhoneNumber::where('box_id', $box->id)->where('is_active', 1)->first();
                if ($whatsappNumber) {
                    $campaign->update(['sender_id' => $whatsappNumber->id]);
                }
            }

            if (!$whatsappNumber) {
                $this->error("No active WhatsApp number found for campaign ID: {$campaign->id}");
                $campaign->update(['status' => 'FAILED']);
                continue;
            }

            // Time check: Only send between 9 AM and 9 PM (inclusive 9:00:00 to 20:59:59)
            $currentHour = (int) now()->format('G');
            if ($currentHour < 9 || $currentHour >= 21) {
                $this->warn("Outside of sending hours (9 AM - 9 PM). Skipping campaign execution for now.");
                continue;
            }

            // Check daily limit
            $dailyLimit = $whatsappNumber->metadata['daily_limit'] ?? 10000;
            $sentToday = WhatsAppMessage::where('sender_id', $whatsappNumber->id)
                ->whereDate('created_at', now()->toDateString())
                ->where('status', '!=', 'Failed')
                ->count();

            if ($sentToday >= $dailyLimit) {
                $this->warn("Daily limit reached for number {$whatsappNumber->display_phone_number}. Skipping this campaign for today.");
                continue;
            }

            $remainingLimit = $dailyLimit - $sentToday;
            // Process at most 500 messages per campaign per minute run to stay within batch size
            $limitForThisCampaign = min(500, $remainingLimit, ($this->batchSize - $totalSentInThisRun));

            $numbers = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $campaign->id)
                ->where('status', 'PENDING')
                ->limit($limitForThisCampaign)
                ->get();

            if ($numbers->isEmpty()) {
                // If there are no more pending numbers, mark as completed
                $campaign->update([
                    'status' => 'COMPLETED',
                    'completed_at' => now()
                ]);
                $this->info("Campaign {$campaign->id} marked as COMPLETED.");
                continue;
            }

            // Update status to SENDING if it was IMPORTED
            if ($campaign->status === 'IMPORTED') {
                $campaign->update(['status' => 'SENDING']);
            }

            foreach ($numbers as $num) {
                try {
                    $message = WhatsAppMessage::create([
                        'box_id' => $box->id,
                        'whatsapp_campaign_id' => $campaign->id,
                        'lead_id' => $num->lead_id,
                        'phone' => $num->phone,
                        'type' => 'OUTGOING',
                        'status' => 'Pending',
                        'added_by' => $campaign->added_by
                    ]);

                    /* 
                    // ACTUAL SENDING - Commented out for testing
                    $waHelper->sendTemplateMessage($message);
                    */

                    // FOR TESTING: Simulate successful send and log it
                    $message->update(['status' => 'Sent']);
                    Log::info("CRON TEST: WhatsApp message simulated to {$num->phone} for campaign ID {$campaign->id}");

                    if ($message->status === 'Sent' || $message->status === 'Delivered') {
                        $num->update(['status' => 'SENT']);
                    } else {
                        $num->update([
                            'status' => 'FAILED',
                            'error' => $message->error ?? 'Failed to send'
                        ]);
                    }
                    
                    $campaign->increment('processed_rows');
                    $totalSentInThisRun++;
                    
                    // Small delay
                    usleep(300000); 

                } catch (\Exception $e) {
                    $this->error("Error sending to {$num->phone}: " . $e->getMessage());
                    $num->update(['status' => 'FAILED', 'error' => $e->getMessage()]);
                }
            }
        }

        $this->info("Cron execution finished. Total sent in this run: $totalSentInThisRun");
        return 0;
    }
}
