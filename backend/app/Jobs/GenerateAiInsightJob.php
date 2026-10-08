<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

use App\Models\SmartView;
use App\Models\AiInsight;
use App\Services\AIService;

class GenerateAiInsightJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Model $record, public string $moduleName)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService): void
    {
        $textToAnalyze = $this->extractTextToAnalyze();

        if (empty(trim($textToAnalyze))) {
            return;
        }

        $classification = $aiService->classifyActivity($textToAnalyze);

        if ($classification) {
            $insightData = [
                'analyzable_id' => $this->record->id,
                'analyzable_type' => get_class($this->record),
                'category' => $classification['category'] ?? null,
                'intent' => $classification['intent'] ?? null,
                'sentiment' => $classification['sentiment'] ?? null,
                'priority' => $classification['priority'] ?? null,
                'action_required' => $classification['action_required'] ?? false,
                'suggested_action' => $classification['suggested_action'] ?? null,
                'confidence' => $classification['confidence'] ?? null,
            ];
            
            // Match against Smart Views targeting this specific module or 'all'
            $userId = $this->extractUserId();
            if ($userId) {
                $matchedViewIds = SmartView::where('user_id', $userId)
                    ->whereIn('module', [$this->moduleName, 'all'])
                    ->pluck('id')->toArray();
                    
                if (!empty($matchedViewIds)) {
                    $insightData['matched_smart_views'] = $matchedViewIds; // cast handles json
                }
            }

            AiInsight::updateOrCreate(
                ['analyzable_id' => $this->record->id, 'analyzable_type' => get_class($this->record)],
                $insightData
            );
        }
    }

    private function extractTextToAnalyze(): string
    {
        return match ($this->moduleName) {
            'activity' => (string) $this->record->description,
            'whatsapp' => (string) $this->record->text,
            'lead' => (string) ($this->record->name . ' ' . $this->record->notes),
            'project' => (string) ($this->record->project_name . ' ' . $this->record->description),
            'deal' => (string) ($this->record->name . ' ' . $this->record->description),
            default => '',
        };
    }

    private function extractUserId(): ?int
    {
        return match ($this->moduleName) {
            'activity' => $this->record->added_by,
            'whatsapp' => $this->record->employee_id ?? $this->record->assigned_to,
            'lead' => $this->record->assigned_to ?? $this->record->created_by,
            'project' => $this->record->client_id ?? null,
            'deal' => $this->record->assigned_to ?? null,
            default => null,
        };
    }
}
