<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\AiContentLog;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Call OpenAI API and log the request and response
     */
    public function generate(string $prompt, string $type = 'general', ?int $boxId = null, ?int $userId = null): ?string
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful CRM assistant. Output only the requested JSON or concise response without markdown formatting unless specified.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $result = $response->choices[0]->message->content;

            // Log the AI usage
            AiContentLog::create([
                'box_id' => $boxId,
                'user_id' => $userId ?? (auth()->check() ? auth()->id() : null),
                'prompt' => $prompt,
                'result' => $result,
                'type' => $type,
            ]);

            return trim($result);
        } catch (\Exception $e) {
            Log::error('AI Service Error: ' . $e->getMessage(), ['prompt' => $prompt, 'type' => $type]);
            return null;
        }
    }

    /**
     * Enhance lead data based on raw input
     */
    public function enhanceLeadData(string $rawQuery): ?array
    {
        $prompt = "Extract the following details from this text and return them as JSON: 'industry', 'city', 'amount' (as a number), 'intent' (hot, warm, cold). \n\nText: \"{$rawQuery}\" \n\nOnly output valid JSON.";
        
        $response = $this->generate($prompt, 'lead_enhancement');
        
        if (!$response) return null;

        // Try to decode JSON
        // Remove potential markdown code blocks if the AI ignored instructions
        $response = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $response);
        
        return json_decode($response, true);
    }

    /**
     * Summarize activity log
     */
    public function summarizeActivity(string $description): ?string
    {
        $prompt = "Summarize the following meeting or call note into a single concise sentence. \n\nNote: \"{$description}\"";
        
        return $this->generate($prompt, 'activity_summary');
    }

    /**
     * Predict next action from activity
     */
    public function predictNextAction(string $description): ?string
    {
        $prompt = "Based on this activity log, what is the single most logical next step or action item? Keep it under 10 words. \n\nLog: \"{$description}\"";
        
        return $this->generate($prompt, 'activity_next_action');
    }

    /**
     * Draft WhatsApp Reply
     */
    public function draftWhatsAppReply(string $incomingMessage, string $history = ''): ?string
    {
        $prompt = "Draft a professional, helpful, and concise response to the following customer WhatsApp message. \n\n";
        if ($history) {
            $prompt .= "Previous Conversation Context: \n{$history}\n\n";
        }
        $prompt .= "Customer Message: \"{$incomingMessage}\"";
        
        return $this->generate($prompt, 'whatsapp_reply');
    }
    
    /**
     * Detect WhatsApp Intent
     */
    public function detectWhatsAppIntent(string $incomingMessage): ?string
    {
        $prompt = "Categorize this message into one of these intents: Sales Inquiry, Support Request, General Question, Spam. Reply with JUST the category name. \n\nMessage: \"{$incomingMessage}\"";
        
        return $this->generate($prompt, 'whatsapp_intent');
    }

    /**
     * Classify an activity for the Smart Views engine
     */
    public function classifyActivity(string $activityText): ?array
    {
        $prompt = "Classify this CRM activity and return a JSON object with these exact keys: 'category' (string), 'intent' (string), 'sentiment' (Positive/Neutral/Negative), 'priority' (High/Medium/Low), 'action_required' (boolean), 'suggested_action' (string, max 5 words), 'confidence' (integer 0-100), 'explanation' (string, max 15 words explaining why). \n\nActivity Text: \"{$activityText}\" \n\nOnly output valid JSON.";
        
        $response = $this->generate($prompt, 'activity_classification');
        return $this->extractJson($response);
    }

    /**
     * Translate natural language into structured Smart View rules
     */
    public function generateSmartViewRules(string $naturalLanguage): ?array
    {
        $prompt = <<<PROMPT
A CRM user typed this to create a smart filter: "{$naturalLanguage}". 

Convert this into a structured JSON rule set. 

Return JSON with exactly these two keys:
1. 'filters': An array of standard, rigid database checks (e.g., [{"field": "status", "operator": "=", "value": "completed"}]). 
   CRITICAL: ONLY use 'filters' for explicitly clear facts (like a specific status, city, or date). Do NOT guess database columns like lead_status_id for abstract concepts!
2. 'ai_rules': An array of AI logic checks based on intent, sentiment, or category. 
   CRITICAL: ALWAYS put soft, human concepts (like 'angry', 'interested', 'wants refund', 'urgent') into 'ai_rules'. 
   (e.g., [{"field": "sentiment", "operator": "=", "value": "negative"}, {"field": "intent", "operator": "=", "value": "refund"}])

Example Input: "Show me completed activities where the customer was angry and wants a refund"
Example Output: 
{
    "filters": [{"field": "status", "operator": "=", "value": "completed"}],
    "ai_rules": [{"field": "sentiment", "operator": "=", "value": "negative"}, {"field": "intent", "operator": "=", "value": "refund"}]
}

Only output valid JSON.
PROMPT;
        
        $response = $this->generate($prompt, 'smart_view_rules');
        return $this->extractJson($response);
    }

    /**
     * Translate natural language search into a query structure
     */
    public function translateSearchToQuery(string $search): ?array
    {
        $prompt = "Convert this CRM natural language search query into structured JSON for our backend to execute: \"{$search}\". \n\nReturn JSON with keys: 'models' (array of models to search e.g., 'Lead', 'Activity', 'WhatsAppMessage') and 'conditions' (array of human-readable query filters). \n\nOnly output valid JSON.";
        
        $response = $this->generate($prompt, 'ask_crm_query');
        return $this->extractJson($response);
    }

    /**
     * Helper to safely extract JSON from LLM response
     */
    private function extractJson(?string $response): ?array
    {
        if (!$response) return null;

        // Try extracting from markdown block
        if (preg_match('/```json\s*(.*?)\s*```/is', $response, $matches)) {
            $response = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/is', $response, $matches)) {
            $response = $matches[1];
        }

        // Try to find curly braces if it's an object
        $start = strpos($response, '{');
        $end = strrpos($response, '}');
        if ($start !== false && $end !== false) {
            $clean = substr($response, $start, $end - $start + 1);
            $decoded = json_decode($clean, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        
        // Try to find square brackets if it's an array
        $start = strpos($response, '[');
        $end = strrpos($response, ']');
        if ($start !== false && $end !== false) {
            $clean = substr($response, $start, $end - $start + 1);
            $decoded = json_decode($clean, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Fallback to raw decode
        return json_decode($response, true);
    }

    /**
     * Translate natural language preference into structured JSON
     */
    public function parseNaturalLanguagePreference(string $naturalLanguage): ?array
    {
        $prompt = "A CRM user typed this preference: \"{$naturalLanguage}\". \n\nConvert this into a structured JSON preference object. It should map to standard filters, sorting, or notification settings. \nExample Output: {\"dashboard\": {\"widgets\": [\"pending_applications\"]}, \"applications\": {\"default_filter\": {\"status\": \"pending\"}, \"sort\": {\"field\": \"priority\", \"direction\": \"desc\"}}} \n\nOnly output valid JSON.";
        
        $response = $this->generate($prompt, 'parse_preference');
        if (!$response) return null;

        $response = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $response);
        return json_decode($response, true);
    }
}
