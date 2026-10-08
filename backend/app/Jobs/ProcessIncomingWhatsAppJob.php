<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\WhatsAppMessage;
use App\Services\AIService;

class ProcessIncomingWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public WhatsAppMessage $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService): void
    {
        if (empty(trim($this->message->text))) {
            return;
        }

        $intent = $aiService->detectWhatsAppIntent($this->message->text);
        
        // Fetch last 5 messages for context
        $history = WhatsAppMessage::where('lead_id', $this->message->lead_id)
            ->where('id', '<', $this->message->id)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return ($msg->type === 'INCOMING' ? 'Customer: ' : 'Agent: ') . $msg->text;
            })
            ->implode("\n");

        $suggestedReply = $aiService->draftWhatsAppReply($this->message->text, $history);

        if ($intent || $suggestedReply) {
            $updates = [];
            if ($intent) $updates['ai_intent'] = $intent;
            if ($suggestedReply) $updates['ai_suggested_reply'] = $suggestedReply;
            
            $this->message->update($updates);
        }
    }
}
