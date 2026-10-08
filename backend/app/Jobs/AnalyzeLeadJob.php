<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Lead;
use App\Services\AIService;

class AnalyzeLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Lead $lead)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService): void
    {
        $textToAnalyze = collect([
            $this->lead->query,
            $this->lead->description,
            $this->lead->company_name
        ])->filter()->implode(' | ');

        if (empty(trim($textToAnalyze))) {
            return;
        }

        $enhancedData = $aiService->enhanceLeadData($textToAnalyze);

        if ($enhancedData) {
            $updates = [];
            
            if (empty($this->lead->industry) && isset($enhancedData['industry'])) {
                $updates['industry'] = $enhancedData['industry'];
            }
            if (empty($this->lead->amount) && isset($enhancedData['amount']) && is_numeric($enhancedData['amount'])) {
                $updates['amount'] = $enhancedData['amount'];
            }
            if (isset($enhancedData['intent'])) {
                $updates['ai_score'] = $enhancedData['intent'];
            }

            if (!empty($updates)) {
                $updates['ai_summary'] = 'Data automatically enriched by AI based on initial query.';
                
                // Temporarily bypass approval to save AI data
                $this->lead->bypass_approval = true;
                $this->lead->update($updates);
            }
        }
    }
}
