<?php

namespace App\Jobs;

use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignNumber;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppPhoneNumber;
use App\Helpers\WhatsApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $batchSize = 50; // Process in small batches to allow limit checks

    public $timeout = 3600; // 1 hour

    public function __construct(WhatsAppCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle()
    {
        $this->campaign->refresh();
        if (in_array($this->campaign->status, ['COMPLETED', 'FAILED', 'PAUSED', 'STOPPED'])) {
            return;
        }

        $this->campaign->update(['status' => 'SENDING']);
        
        $box = $this->campaign->box;
        $waHelper = new WhatsApp($box);
        
        // Get the phone number model for limits
        // If sender_id is not set, we might need to pick the first active number for the box
        $senderId = $this->campaign->sender_id;
        $whatsappNumber = null;
        if ($senderId) {
            $whatsappNumber = WhatsAppPhoneNumber::find($senderId);
        } else {
            $whatsappNumber = WhatsAppPhoneNumber::where('box_id', $box->id)->where('is_active', 1)->first();
            if ($whatsappNumber) {
                $this->campaign->update(['sender_id' => $whatsappNumber->id]);
            }
        }

        if (!$whatsappNumber) {
            Log::error("No active WhatsApp number found for campaign ID: {$this->campaign->id}");
            $this->campaign->update(['status' => 'FAILED']);
            return;
        }

        // Check daily limit
        $dailyLimit = $whatsappNumber->metadata['daily_limit'] ?? 1000; // Default 1000
        
        // Count sent today from this number across all campaigns
        $sentToday = WhatsAppMessage::where('box_id', $box->id)
            ->whereDate('created_at', now()->toDateString())
            ->where('status', '!=', 'Failed')
            ->count();

        if ($sentToday >= $dailyLimit) {
            Log::info("Daily limit reached for number {$whatsappNumber->display_phone_number}. Rescheduling.");
            // Reschedule for tomorrow morning
            self::dispatch($this->campaign)->delay(now()->addDay()->startOfDay()->addHours(9));
            return;
        }

        $remainingLimit = $dailyLimit - $sentToday;
        $batchToProcess = min($this->batchSize, $remainingLimit);

        $numbers = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $this->campaign->id)
            ->where('status', 'PENDING')
            ->limit($batchToProcess)
            ->get();

        if ($numbers->isEmpty()) {
            $this->campaign->update([
                'status' => 'COMPLETED',
                'completed_at' => now()
            ]);
            Log::info("Campaign {$this->campaign->id} completed.");
            return;
        }

        foreach ($numbers as $num) {
            try {
                // Create a WhatsAppMessage record first (as required by sendTemplateMessage)
                $message = WhatsAppMessage::create([
                    'box_id' => $box->id,
                    'whatsapp_campaign_id' => $this->campaign->id,
                    'lead_id' => $num->lead_id,
                    'type' => 'OUTGOING',
                    'status' => 'PENDING',
                    'added_by' => $this->campaign->added_by
                ]);

                /* 
                // ACTUAL SENDING - Commented out for testing
                $waHelper->sendTemplateMessage($message);
                */

                // FOR TESTING: Simulate successful send and log it
                $message->update(['status' => 'SENT']);
                Log::info("TEST MODE: WhatsApp message simulated to {$num->phone} for campaign ID {$this->campaign->id}");

                if ($message->status === 'SENT' || $message->status === 'DELIVERED') {
                    $num->update(['status' => 'SENT']);
                } else {
                    $num->update([
                        'status' => 'FAILED',
                        'error_message' => $message->error ?? 'Failed to send'
                    ]);
                }
                
                // Update processed rows count
                $this->campaign->increment('processed_rows');

                // Optional: small delay to prevent rate limit issues
                usleep(500000); // 0.5 seconds

            } catch (\Exception $e) {
                Log::error("Error sending message to {$num->phone}: " . $e->getMessage());
                $num->update(['status' => 'FAILED', 'error_message' => $e->getMessage()]);
            }
        }

        // If there are still pending numbers, dispatch the job again
        $hasMore = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $this->campaign->id)
            ->where('status', 'PENDING')
            ->exists();

        if ($hasMore) {
            self::dispatch($this->campaign)->delay(now()->addSeconds(5));
        } else {
            $this->campaign->update([
                'status' => 'COMPLETED',
                'completed_at' => now()
            ]);
        }
    }
}
