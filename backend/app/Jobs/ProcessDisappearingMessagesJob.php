<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;

class ProcessDisappearingMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $boxId;
    protected $leadId;
    protected $duration;

    /**
     * Create a new job instance.
     */
    public function __construct($boxId, $leadId, $duration)
    {
        $this->boxId = $boxId;
        $this->leadId = $leadId;
        $this->duration = $duration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cutoffDate = null;

        if ($this->duration === '24_hours') {
            $cutoffDate = Carbon::now()->subDay();
        } elseif ($this->duration === '7_days') {
            $cutoffDate = Carbon::now()->subDays(7);
        } elseif ($this->duration === '90_days') {
            $cutoffDate = Carbon::now()->subDays(90);
        }

        if ($cutoffDate) {
            WhatsAppMessage::where('box_id', $this->boxId)
                ->where('lead_id', $this->leadId)
                ->where('created_at', '<', $cutoffDate)
                ->delete();
        }
    }
}
