<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Activity;
use App\Models\SmartView;
use App\Services\AIService;

class ClassifyActivityJob implements ShouldQueue
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

        $classification = $aiService->classifyActivity($this->activity->description);

        if ($classification) {
            $updates = [
                'ai_category' => $classification['category'] ?? null,
                'ai_intent' => $classification['intent'] ?? null,
                'ai_sentiment' => $classification['sentiment'] ?? null,
                'ai_priority' => $classification['priority'] ?? null,
                'ai_action_required' => $classification['action_required'] ?? false,
                'ai_suggested_action' => $classification['suggested_action'] ?? null,
                'ai_confidence' => $classification['confidence'] ?? null,
                'ai_explanation' => $classification['explanation'] ?? null,
            ];
            
            // Smart View Matching
            // (In a real scenario, this would evaluate the dynamic JSON filters.
            // For now, we simulate matching any active SmartViews for the user)
            $userId = $this->activity->added_by;
            if ($userId) {
                $matchedViewIds = SmartView::where('user_id', $userId)->pluck('id')->toArray();
                if (!empty($matchedViewIds)) {
                    $updates['matched_smart_views'] = json_encode($matchedViewIds);
                }
            }

            $this->activity->updateQuietly($updates);
        }
    }
}
