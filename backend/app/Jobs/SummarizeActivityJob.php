<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Activity;
use App\Services\AIService;

class SummarizeActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Activity $activity)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService): void
    {
        if (empty(trim($this->activity->description))) {
            return;
        }

        // Only summarize if it's long enough (e.g. > 100 chars)
        if (strlen($this->activity->description) > 100) {
            $summary = $aiService->summarizeActivity($this->activity->description);
            $nextAction = $aiService->predictNextAction($this->activity->description);

            if ($summary || $nextAction) {
                $updates = [];
                if ($summary) $updates['ai_summary'] = $summary;
                if ($nextAction) $updates['ai_next_action'] = $nextAction;
                
                $this->activity->updateQuietly($updates);
            }
        }
    }
}
