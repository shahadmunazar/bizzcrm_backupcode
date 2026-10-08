<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForwardWhatsAppWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhookUrl;
    public $payload;
    public $boxId;

    /**
     * Create a new job instance.
     */
    public function __construct($webhookUrl, $payload, $boxId)
    {
        $this->webhookUrl = $webhookUrl;
        $this->payload = $payload;
        $this->boxId = $boxId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::timeout(10)->post($this->webhookUrl, $this->payload);
            
            if (!$response->successful()) {
                Log::warning("WhatsApp Webhook Forwarding failed for Box {$this->boxId}", [
                    'url' => $this->webhookUrl,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp Webhook Forwarding Exception for Box {$this->boxId}", [
                'url' => $this->webhookUrl,
                'error' => $e->getMessage()
            ]);
        }
    }
}
