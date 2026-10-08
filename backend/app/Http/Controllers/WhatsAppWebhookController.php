<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\WhatsAppBusinessAccount;
use App\Models\WhatsAppPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * WhatsApp Webhook Verification (GET)
     * Meta sends a GET request to verify the webhook endpoint
     */
    public function webhookValidate(Request $request)
    {
        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'method' => 'GET',
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'query_params' => $request->all(),
            'headers' => $request->headers->all(),
        ];



        // Log to Laravel log
        Log::info('🔔 WhatsApp Business Webhook Validation Request', $logData);
        $hubMode = $request->query('hub_mode') ?? $request->query('hub.mode') ?? $request->input('hub_mode') ?? $request->input('hub.mode') ?? $_GET['hub_mode'] ?? $_GET['hub.mode'] ?? null;
        $verifyToken = $request->query('hub_verify_token') ?? $request->query('hub.verify_token') ?? $request->input('hub_verify_token') ?? $request->input('hub.verify_token') ?? $_GET['hub_verify_token'] ?? $_GET['hub.verify_token'] ?? null;
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge') ?? $request->input('hub_challenge') ?? $request->input('hub.challenge') ?? $_GET['hub_challenge'] ?? $_GET['hub.challenge'] ?? null;
        
        $expectedToken = config('services.whatsapp.webhook_verify_token') ?: 'BizzWhatsAppWebhook2026';
        $facebookToken = config('services.facebook.webhook_verify_token') ?: 'BizzTest5161';
        
        $allowedTokens = array_filter(array_unique([
            $expectedToken,
            'BizzWhatsAppWebhook2026',
            'BizzTest5161',
            $facebookToken,
            'BizzWhatsAppWebhook',
            'BizzWhatsAppWebhook2025'
        ]));

        Log::info('WhatsApp Webhook Verification Check', [
            'hub_mode_received' => $hubMode,
            'verify_token_received' => $verifyToken,
            'challenge_received' => $challenge ? 'yes' : 'no',
            'expected_token' => $expectedToken,
            'allowed_tokens' => $allowedTokens,
            'all_input' => $request->all(),
        ]);

        if ($hubMode && $hubMode == "subscribe") {
            if ($verifyToken && in_array($verifyToken, $allowedTokens)) {
                if ($challenge) {
                    Log::info('✅ WhatsApp Webhook Verification SUCCESS', [
                        'challenge' => $challenge,
                        'hub_mode' => $hubMode,
                        'matched_token' => $verifyToken,
                    ]);

                    // Return ONLY the challenge as plain text (Meta strictly expects this)
                    return response($challenge, 200)->header('Content-Type', 'text/plain');
                } else {
                    Log::warning('❌ WhatsApp Webhook Verification - Challenge Missing', [
                        'hub_mode' => $hubMode,
                        'has_verify_token' => !empty($verifyToken),
                    ]);
                }
            } else {
                Log::warning('❌ WhatsApp Webhook Verification FAILED - Token Mismatch', [
                    'received_token' => $verifyToken,
                    'allowed_tokens' => $allowedTokens,
                ]);
            }
        } else {
            Log::warning('❌ WhatsApp Webhook Validation Failed - Invalid Request', [
                'hub_mode' => $hubMode ?? 'not set',
                'has_verify_token' => !empty($verifyToken),
                'all_query_params' => $request->query(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid request',
        ], 403);
    }

    /**
     * WhatsApp Webhook Handler (POST)
     * Meta sends POST requests with webhook events
     */
    public function webhook(Request $request)
    {
        Log::debug('!!! WEBHOOK HIT !!!');
        $rawInput = file_get_contents("php://input");
        $data = json_decode($rawInput, true);

        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'method' => 'POST',
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'raw_input_length' => strlen($rawInput),
            'object_type' => $data['object'] ?? 'unknown',
            'has_entry' => isset($data['entry']),
            'entry_count' => isset($data['entry']) ? count($data['entry']) : 0,
        ];



        // Log to Laravel log
        Log::info('🔔 WhatsApp Business Webhook POST Request', $logData);
        Log::debug('📦 RAW WEBHOOK DATA: ' . $rawInput);

        // Verify webhook signature (optional but recommended for security)
        $signature = $request->header('X-Hub-Signature-256');
        if ($signature) {
            $appSecret = config('services.whatsapp.app_secret') ?: config('services.facebook.app_secret');
            if ($appSecret) {
                $expectedSignature = 'sha256=' . hash_hmac('sha256', $rawInput, $appSecret);
                if ($signature !== $expectedSignature) {
                    Log::warning('❌ WhatsApp Webhook Signature Mismatch', [
                        'received' => $signature,
                        'expected' => $expectedSignature,
                    ]);
                    // Only reject if we are in production environment
                    if (config('app.env') === 'production') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid signature',
                        ], 403);
                    } else {
                        Log::info('⚠️ Signature verification failed but allowing request since APP_ENV is ' . config('app.env'));
                    }
                }
            }
        }

        if (isset($data['object']) && $data['object'] == "whatsapp_business_account") {
            $messagesProcessed = 0;
            $statusUpdatesProcessed = 0;
            $errors = [];

            foreach ($data['entry'] as $entryIndex => $entry) {
                Log::info("Processing WhatsApp webhook entry #{$entryIndex}", [
                    'entry_id' => $entry['id'] ?? 'unknown',
                    'time' => $entry['time'] ?? null,
                    'has_changes' => isset($entry['changes']),
                    'changes_count' => isset($entry['changes']) ? count($entry['changes']) : 0,
                ]);

                // Developer API Webhook Forwarding
                if (isset($entry['id'])) {
                    $waba = \App\Models\WhatsAppBusinessAccount::where('waba_id', $entry['id'])->first();
                    if ($waba && $waba->box && !empty($waba->box->whatsapp_webhook_url)) {
                        \App\Jobs\ForwardWhatsAppWebhookJob::dispatch($waba->box->whatsapp_webhook_url, $data, $waba->box->id);
                    }
                }

                if (isset($entry['changes'])) {
                    foreach ($entry['changes'] as $changeIndex => $change) {
                        Log::info("Processing WhatsApp change #{$changeIndex}", [
                            'field' => $change['field'] ?? 'unknown',
                            'value_keys' => isset($change['value']) ? array_keys($change['value']) : [],
                        ]);

                        // Handle messages and statuses (Cloud API puts statuses inside messages field)
                        if ($change['field'] == "messages") {
                            if (isset($change['value']['messages'])) {
                                foreach ($change['value']['messages'] as $message) {
                                    Log::info('📥 WhatsApp Message Incoming', [
                                        'from' => $message['from'],
                                        'phone_id_receiving' => $change['value']['metadata']['phone_number_id'] ?? 'unknown',
                                        'waba_id' => $entry['id']
                                    ]);
                                }
                                $messagesProcessed += count($change['value']['messages']);
                                $this->handleMessage($change['value'], $entry['id']);
                            }
                            if (isset($change['value']['statuses'])) {
                                foreach ($change['value']['statuses'] as $statusUpdate) {
                                    $statusUpdatesProcessed++;
                                    $this->handleMessageStatus($statusUpdate, $entry['id']);
                                }
                            }
                        }

                        // Handle message status updates (legacy or other API versions)
                        if ($change['field'] == "message_status") {
                            $statusUpdatesProcessed++;
                            $this->handleMessageStatus($change['value'], $entry['id']);
                        }

                        // Handle account status updates
                        if ($change['field'] == "account_status") {
                            $this->handleAccountStatus($change['value'], $entry['id']);
                        }

                        // Handle phone number status updates
                        if ($change['field'] == "phone_number_status") {
                            $this->handlePhoneNumberStatus($change['value'], $entry['id']);
                        }

                        // Handle message template status updates
                        if ($change['field'] == "message_template_status_update") {
                            $this->handleTemplateStatus($change['value'], $entry['id']);
                        }

                        // Handle account update (Embedded Signup flow)
                        if ($change['field'] == "account_update") {
                            $this->handleAccountUpdate($change['value'], $entry['id']);
                        }
                    }
                }
            }

            $summary = [
                'messages_processed' => $messagesProcessed,
                'status_updates_processed' => $statusUpdatesProcessed,
                'errors' => $errors,
            ];

            Log::info('✅ WhatsApp Webhook Processing Complete', $summary);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'data' => $summary,
            ]);
        } else {
            Log::warning('⚠️ WhatsApp Webhook object is not "whatsapp_business_account" or missing entry', [
                'object' => $data['object'] ?? 'unknown',
                'has_entry' => isset($data['entry']),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid request',
        ], 400);
    }

    /**
     * Handle incoming WhatsApp messages
     */
    private function handleMessage($value, $wabaId)
    {
        try {
            // WhatsApp webhook structure: value contains messages array
            $messages = $value['messages'] ?? [];
            
            if (empty($messages)) {
                Log::warning('⚠️ WhatsApp message webhook received but no messages in payload', [
                    'waba_id' => $wabaId,
                    'value_keys' => array_keys($value),
                ]);
                return;
            }

            foreach ($messages as $message) {
                Log::info('📥 WhatsApp Message Received', [
                    'waba_id' => $wabaId,
                    'message_id' => $message['id'] ?? 'unknown',
                    'from' => $message['from'] ?? 'unknown',
                    'type' => $message['type'] ?? 'unknown',
                    'timestamp' => $message['timestamp'] ?? null,
                ]);

                // Find the WABA Account - Try by WABA ID first, then fallback to Phone Number ID
                $waba = WhatsAppBusinessAccount::where('waba_id', $wabaId)->first();
                
                if (!$waba && isset($value['metadata']['phone_number_id'])) {
                    $phoneId = $value['metadata']['phone_number_id'];
                    Log::info("🔍 WABA ID {$wabaId} not found, trying Phone ID: {$phoneId}");
                    $phone = WhatsAppPhoneNumber::where('whatsapp_id', $phoneId)->first();
                    if ($phone) {
                        $waba = $phone->whatsappBusinessAccount;
                        Log::info("✅ Found account via Phone ID lookup", ['waba_id' => $waba->waba_id ?? 'unknown']);
                    }
                }

                if (!$waba && isset($value['metadata']['display_phone_number'])) {
                    $displayPhone = $value['metadata']['display_phone_number'];
                    // Strip non-digits to match display_phone_number database formats
                    $cleanDisplayPhone = preg_replace('/\D/', '', $displayPhone);
                    Log::info("🔍 WABA not found, trying display phone lookup: {$cleanDisplayPhone}");
                    $phone = WhatsAppPhoneNumber::where('display_phone_number', 'LIKE', "%{$cleanDisplayPhone}%")->first();
                    if ($phone) {
                        $waba = $phone->whatsappBusinessAccount;
                        Log::info("✅ Found account via Display Phone lookup", ['waba_id' => $waba->waba_id ?? 'unknown']);
                    }
                }

                if ($waba) {
                    $from = $message['from'] ?? null;
                    $text = '';
                    $msgTypeField = $message['type'] ?? 'unknown';

                    // Handle incoming message reactions
                    if ($msgTypeField === 'reaction') {
                        $targetMsgId = $message['reaction']['message_id'] ?? null;
                        $emoji = $message['reaction']['emoji'] ?? '';

                        if ($targetMsgId) {
                            $originalMessage = \App\Models\WhatsAppMessage::where('message_id', $targetMsgId)->first();
                            if ($originalMessage) {
                                $searchNumber = substr($from, -10);
                                $isOurNumber = \App\Models\WhatsAppPhoneNumber::where(function($q) use ($searchNumber) {
                                    $q->where('display_phone_number', 'LIKE', "%$searchNumber%");
                                })->exists();

                                if ($isOurNumber) {
                                    $originalMessage->user_reaction = $emoji ?: null;
                                } else {
                                    $originalMessage->contact_reaction = $emoji ?: null;
                                }
                                $originalMessage->save();
                                Log::info("💖 WhatsApp Message Reaction updated via Webhook", [
                                    'original_message_id' => $targetMsgId,
                                    'emoji' => $emoji,
                                    'is_our_reaction' => $isOurNumber
                                ]);
                            } else {
                                Log::warning("⚠️ Original message to react not found in database", ['target_message_id' => $targetMsgId]);
                            }
                        }
                        continue;
                    }

                    $payload = null;

                    if ($msgTypeField === 'text') {
                        $text = $message['text']['body'] ?? '';
                    } elseif ($msgTypeField === 'interactive') {
                        $interactiveType = $message['interactive']['type'] ?? '';
                        if ($interactiveType === 'button_reply') {
                            $text = $message['interactive']['button_reply']['title'] ?? '';
                            $payload = $message['interactive']['button_reply']['id'] ?? null;
                        } elseif ($interactiveType === 'list_reply') {
                            $text = $message['interactive']['list_reply']['title'] ?? '';
                            $payload = $message['interactive']['list_reply']['id'] ?? null;
                        }
                    } elseif ($msgTypeField === 'button') {
                        $text = $message['button']['text'] ?? '';
                        $payload = $message['button']['payload'] ?? null;
                    } elseif ($msgTypeField === 'image') {
                        $text = $message['image']['caption'] ?? '[Image]';
                    } elseif ($msgTypeField === 'document') {
                        $text = $message['document']['caption'] ?? $message['document']['filename'] ?? '[Document]';
                    } elseif ($msgTypeField === 'audio') {
                        $text = '[Voice Message]';
                    } elseif ($msgTypeField === 'video') {
                        $text = $message['video']['caption'] ?? '[Video]';
                    } elseif ($msgTypeField === 'sticker') {
                        $text = '[Sticker]';
                    } elseif ($msgTypeField === 'location') {
                        $text = '[Location]';
                    } else {
                        $text = "[$msgTypeField message received]";
                    }
                    $messageId = $message['id'] ?? null;
                    $timestamp = isset($message['timestamp']) ? date('Y-m-d H:i:s', $message['timestamp']) : now();

                    if (!$from || !$messageId) continue;

                    // MEDIA HANDLING: Download and store media if present
                    $mediaData = null;
                    if (in_array($msgTypeField, ['image', 'video', 'audio', 'document', 'sticker'])) {
                        $mediaId = $message[$msgTypeField]['id'] ?? null;
                        if ($mediaId) {
                            Log::info("📁 Processing incoming WhatsApp media", ['type' => $msgTypeField, 'id' => $mediaId]);
                            $whatsappHelper = new \App\Helpers\WhatsApp($waba->box);
                            $mediaInfo = $whatsappHelper->getMedia($mediaId);
                            
                            if ($mediaInfo['success'] && isset($mediaInfo['data']['url'])) {
                                $download = $whatsappHelper->downloadMedia(
                                    $mediaInfo['data']['url'], 
                                    $messageId, 
                                    $mediaInfo['data']['mime_type'] ?? 'application/octet-stream'
                                );
                                
                                if ($download['success']) {
                                    $mediaData = [
                                        'path' => $download['path'],
                                        'filename' => $download['name'],
                                        'mime_type' => $mediaInfo['data']['mime_type'] ?? 'application/octet-stream',
                                        'size' => $mediaInfo['data']['file_size'] ?? 0,
                                        'type' => $msgTypeField
                                    ];
                                }
                            }
                        }
                    }

                    // LOG THE RAW MESSAGE FOR DEBUGGING
                    Log::info('📥 WhatsApp Webhook Message Details:', [
                        'from' => $from,
                        'waba_id' => $wabaId,
                        'text' => $text,
                        'message_id' => $messageId,
                        'has_media' => !is_null($mediaData)
                    ]);

                    // Find the Lead by phone number (last 10 digits)
                    $searchNumber = substr($from, -10);
                    
                    // Check if this message is from our own business number (Outgoing from Phone App)
                    $isOurNumber = \App\Models\WhatsAppPhoneNumber::where(function($q) use ($searchNumber) {
                        $q->where('display_phone_number', 'LIKE', "%$searchNumber%");
                    })->exists();

                    $msgType = $isOurNumber ? 'OUTGOING' : 'INCOMING';
                    $lead = null;

                    if (!$isOurNumber) {
                        // CUSTOMER -> BUSINESS
                        $lead = \App\Models\Lead::where('box_id', $waba->box_id)
                            ->where(function($query) use ($searchNumber) {
                                $query->where('phone', 'LIKE', "%$searchNumber%")
                                    ->orWhere('mobile', 'LIKE', "%$searchNumber%")
                                    ->orWhere('sec_phone', 'LIKE', "%$searchNumber%");
                            })->first();

                        if (!$lead) {
                            Log::info('🆕 Creating new lead for WhatsApp contact', ['from' => $from]);
                            $lead = \App\Models\Lead::create([
                                'box_id' => $waba->box_id,
                                'name' => 'New WhatsApp Contact (' . $from . ')',
                                'phone' => $from,
                                'status' => 'ACTIVE',
                                'description' => 'Automatically created from WhatsApp Incoming Message',
                                'assignment_type' => 'whatsapp_webhook',
                            ]);
                        } else {
                            Log::info('👤 Existing lead found for WhatsApp contact', [
                                'lead_id' => $lead->id,
                                'lead_name' => $lead->name
                            ]);
                        }
                    } else {
                        // BUSINESS -> CUSTOMER (Sent from Phone App)
                        $lead = \App\Models\Lead::where('box_id', $waba->box_id)
                            ->whereHas('whatsappMessages', function($q) {
                                $q->latest();
                            })->first();
                    }

                    if ($lead) {
                        // Check if message already exists
                        $existing = \App\Models\WhatsAppMessage::where('message_id', $messageId)->first();
                        if (!$existing) {
                            $replyToMessageId = null;
                            if (isset($message['context']['id'])) {
                                $parentMsg = \App\Models\WhatsAppMessage::where('message_id', $message['context']['id'])->first();
                                if ($parentMsg) {
                                    $replyToMessageId = $parentMsg->id;
                                }
                            }

                            \App\Models\WhatsAppMessage::create([
                                'box_id' => $lead->box_id,
                                'lead_id' => $lead->id,
                                'type' => $msgType,
                                'message_id' => $messageId,
                                'reply_to_message_id' => $replyToMessageId,
                                'text' => $text,
                                'media' => $mediaData,
                                'is_sent' => true,
                                'sent_at' => $timestamp,
                                'status' => 'RECEIVED',
                                'is_read' => $msgType === 'INCOMING' ? false : true,
                            ]);

                            // Log to daily Activity
                            try {
                                $today = now()->format('Y-m-d');
                                $existingActivity = \App\Models\Activity::where('box_id', $lead->box_id)
                                    ->where('activity_id', $lead->id)
                                    ->where('type', 'whatsapp')
                                    ->whereDate('activity_date', $today)
                                    ->first();

                                $timePrefix = "[" . now()->format('h:i A') . "]";
                                $title = $msgType === 'INCOMING' ? "Incoming Message" : "Outgoing Message";
                                $messageEntry = $timePrefix . " " . $title;
                                if ($text) {
                                    $messageEntry .= ": " . $text;
                                }

                                if ($existingActivity) {
                                    $existingActivity->update([
                                        'description' => $existingActivity->description . "\n" . $messageEntry
                                    ]);
                                } else {
                                    \App\Models\Activity::create([
                                        'box_id'          => $lead->box_id,
                                        'activity_id'     => $lead->id, 
                                        'type'            => 'whatsapp',
                                        'subject'         => "WhatsApp Conversation (" . now()->format('M d, Y') . ")",
                                        'description'     => $messageEntry,
                                        'activity_date'   => now(),
                                        'added_by'        => 1,
                                        'last_updated_by' => 1,
                                        'assigned_to'     => $lead->assigned_to ?? 1,
                                        'status'          => 'Close',
                                        'completed_date'  => now(),
                                    ]);
                                }
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to log WhatsApp webhook activity: " . $e->getMessage());
                            }
                            
                            // IF THIS IS AN INCOMING MESSAGE, PROCESS CHATBOT
                            if ($msgType === 'INCOMING') {
                                $phoneId = $value['metadata']['phone_number_id'] ?? null;
                                $this->processChatbot($lead, $waba, $text, $payload, $phoneId);
                            }
                        }
                    }
                } else {
                    Log::warning('⚠️ WhatsApp Business Account not found in database for incoming message', [
                        'waba_id' => $wabaId,
                        'phone_number_id' => $value['metadata']['phone_number_id'] ?? 'unknown',
                        'from' => $message['from'] ?? 'unknown'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('❌ Error processing WhatsApp message', [
                'waba_id' => $wabaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Execute Chatbot Logic
     */
    private function processChatbot($lead, $waba, $text, $payload, $phoneId)
    {
        try {
            Log::info("🤖 Evaluating Chatbot Triggers", [
                'lead_id' => $lead->id,
                'text' => $text,
                'payload' => $payload
            ]);

            // 1. Is this a button payload traversal? format: cb_{chatbot_id}_{node_id}
            if ($payload && str_starts_with($payload, 'cb_')) {
                preg_match('/^cb_(\d+)_(.*)$/', $payload, $matches);
                if (count($matches) === 3) {
                    $chatbotId = $matches[1];
                    $nodeId = $matches[2];

                    $chatbot = \App\Models\WhatsAppChatbot::find($chatbotId);
                    if ($chatbot && $chatbot->is_active && $chatbot->box_id === $waba->box_id) {
                        Log::info("🤖 Resuming Chatbot {$chatbotId} at node: {$nodeId}");
                        $node = $this->getChatbotNodeById($chatbot->flow_data, $nodeId);
                        if ($node) {
                            $this->sendChatbotNode($lead, $waba, $node, $chatbotId, $chatbot->flow_data, $phoneId);
                            return; // Stop processing further
                        }
                    }
                }
            } else {
                // Check if lead is currently in an active chatbot session waiting for a reply
                $session = \App\Models\WhatsAppChatbotSession::where('lead_id', $lead->id)
                    ->where('box_id', $waba->box_id)
                    ->whereNotNull('current_node_id')
                    ->first();
                
                if ($session) {
                    $this->processChatbotSessionReply($lead, $waba, $text, $phoneId, $session);
                    return; // Stop processing further triggers
                }

                // 1.5 Is there an attached chatbot to the last campaign message sent to this lead within the last 24 hours?
                $lastOutgoingMsg = \App\Models\WhatsAppMessage::where('lead_id', $lead->id)
                    ->where('type', 'OUTGOING')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($lastOutgoingMsg) {
                    if ($lastOutgoingMsg->whatsapp_campaign_id) {
                        $campaign = \App\Models\WhatsAppCampaign::find($lastOutgoingMsg->whatsapp_campaign_id);
                        if ($campaign && !empty($campaign->params['chatbot_id'])) {
                            $chatbotId = $campaign->params['chatbot_id'];
                            $chatbot = \App\Models\WhatsAppChatbot::find($chatbotId);
                            if ($chatbot && $chatbot->is_active && $chatbot->box_id === $waba->box_id) {
                                Log::info("🤖 Initiating Attached Chatbot {$chatbotId} for Campaign Reply");
                                $node = $this->getChatbotNodeById($chatbot->flow_data, 'startNode');
                                if ($node) {
                                    $this->sendChatbotNode($lead, $waba, $node, $chatbotId, $chatbot->flow_data, $phoneId);
                                    return; // Stop processing further
                                }
                            }
                        }
                    } elseif (is_array($lastOutgoingMsg->media) && !empty($lastOutgoingMsg->media['whatsapp_template_id'])) {
                        $templateId = $lastOutgoingMsg->media['whatsapp_template_id'];
                        $template = \App\Models\WhatsAppTemplate::find($templateId);
                        if ($template && !empty($template->params['api_config']['chatbot_id'])) {
                            $chatbotId = $template->params['api_config']['chatbot_id'];
                            $chatbot = \App\Models\WhatsAppChatbot::find($chatbotId);
                            if ($chatbot && $chatbot->is_active && $chatbot->box_id === $waba->box_id) {
                                Log::info("🤖 Initiating Attached Chatbot {$chatbotId} for API Template Reply");
                                $node = $this->getChatbotNodeById($chatbot->flow_data, 'startNode');
                                if ($node) {
                                    $this->sendChatbotNode($lead, $waba, $node, $chatbotId, $chatbot->flow_data, $phoneId);
                                    return; // Stop processing further
                                }
                            }
                        }
                    }
                }

            // 2. Initial trigger matching
            // We search for chatbots where trigger_type = exact match or contains
            $chatbots = \App\Models\WhatsAppChatbot::where('box_id', $waba->box_id)
                ->where('is_active', true)
                ->get();

            foreach ($chatbots as $chatbot) {
                $triggerValue = strtolower(trim($chatbot->trigger_value));
                $incomingText = strtolower(trim($text));
                $incomingPayload = strtolower(trim($payload ?? ''));

                $isMatch = false;

                if (empty($triggerValue)) continue;

                if (in_array($chatbot->trigger_type, ['exact_match', 'keyword'])) {
                    if ($incomingText === $triggerValue || $incomingPayload === $triggerValue) {
                        $isMatch = true;
                    }
                } elseif ($chatbot->trigger_type === 'contains') {
                    if (str_contains($incomingText, $triggerValue) || str_contains($incomingPayload, $triggerValue)) {
                        $isMatch = true;
                    }
                }

                if ($isMatch) {
                    Log::info("🤖 Trigger Matched Chatbot {$chatbot->id} via type: {$chatbot->trigger_type}");
                    $node = $this->getChatbotNodeById($chatbot->flow_data, 'startNode');
                    if ($node) {
                        $this->sendChatbotNode($lead, $waba, $node, $chatbot->id, $chatbot->flow_data, $phoneId);
                        return; // Stop after first match
                    }
                }
            }
        }
    } catch (\Exception $e) {
            Log::error('❌ Error in Chatbot processing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Process user's free-text reply to a Question node
     */
    private function processChatbotSessionReply($lead, $waba, $text, $phoneId, $session)
    {
        $chatbot = $session->chatbot;
        if (!$chatbot || !$chatbot->is_active) {
            $session->delete();
            return;
        }
        
        $flowData = $chatbot->flow_data;
        if (is_string($flowData)) $flowData = json_decode($flowData, true);
        
        $currentNode = $this->getChatbotNodeById($flowData, $session->current_node_id);
        if (!$currentNode || $currentNode['type'] !== 'question') {
            $session->current_node_id = null;
            $session->save();
            return;
        }
        
        // Save the answer
        $variable = $currentNode['config']['variable'] ?? 'answer';
        $data = $session->data ?? [];
        $data[$variable] = $text;
        $session->data = $data;
        $session->current_node_id = null; // Clear it before advancing
        $session->save();
        
        // Advance to next node
        if (isset($flowData['edges']) && is_array($flowData['edges'])) {
            foreach ($flowData['edges'] as $edge) {
                if ($edge['sourceId'] === $currentNode['id']) {
                    $nextNodeId = $edge['targetId'];
                    $nextNode = $this->getChatbotNodeById($flowData, $nextNodeId);
                    if ($nextNode) {
                        $this->sendChatbotNode($lead, $waba, $nextNode, $chatbot->id, $flowData, $phoneId);
                    }
                    break;
                }
            }
        }
    }

    /**
     * Retrieve a specific node from the nested JSON flow data using a dot-notation path
     */
    private function getChatbotNodeById($flowData, $nodeId)
    {
        if (is_string($flowData)) {
            $flowData = json_decode($flowData, true);
        }

        if (empty($flowData) || !isset($flowData['nodes'])) return null;

        foreach ($flowData['nodes'] as $node) {
            if ($node['id'] === $nodeId) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Send a Chatbot Node to the User (Translates Node UI to Meta API logic)
     */
    private function sendChatbotNode($lead, $waba, $node, $chatbotId, $flowData, $phoneId)
    {
        if (!$node) return;

        $whatsappHelper = new \App\Helpers\WhatsApp($waba->box);

        $messageText = $node['config']['text'] ?? '';

        // Dynamic Variable Replacement (e.g. {{name}}, {{email}})
        $session = \App\Models\WhatsAppChatbotSession::where('lead_id', $lead->id)->where('box_id', $waba->box_id)->first();
        if (!empty($messageText) && preg_match_all('/\{\{([^}]+)\}\}/', $messageText, $matches)) {
            foreach ($matches[1] as $index => $varName) {
                $varName = trim($varName);
                $replacement = '';
                if ($session && is_array($session->data) && isset($session->data[$varName])) {
                    $replacement = $session->data[$varName];
                } else if (isset($lead->{$varName})) {
                    $replacement = $lead->{$varName};
                }
                $messageText = str_replace($matches[0][$index], $replacement, $messageText);
            }
        }

        if (is_string($flowData)) {
            $flowData = json_decode($flowData, true);
        }

        // Find outgoing connections
        $outgoingEdges = [];
        if (isset($flowData['edges'])) {
            foreach ($flowData['edges'] as $edge) {
                if ($edge['sourceId'] === $node['id']) {
                    $outgoingEdges[] = $edge;
                }
            }
        }

        if ($node['type'] === 'condition') {
            // Evaluate condition
            $session = \App\Models\WhatsAppChatbotSession::where('lead_id', $lead->id)->where('box_id', $waba->box_id)->first();
            $variable = $node['config']['variable'] ?? '';
            $operator = $node['config']['operator'] ?? '==';
            $value = $node['config']['value'] ?? '';
            
            $actualValue = '';
            // Check session data first, then lead properties
            if ($session && is_array($session->data) && isset($session->data[$variable])) {
                $actualValue = $session->data[$variable];
            } else {
                $actualValue = $lead->{$variable} ?? '';
            }
            
            $isTrue = false;
            switch ($operator) {
                case '==': $isTrue = (strcasecmp((string)$actualValue, (string)$value) == 0); break;
                case '!=': $isTrue = (strcasecmp((string)$actualValue, (string)$value) != 0); break;
                case '>': $isTrue = ($actualValue > $value); break;
                case '<': $isTrue = ($actualValue < $value); break;
                case 'contains': $isTrue = (stripos((string)$actualValue, (string)$value) !== false); break;
            }
            
            // Find the edge for the true or false branch
            $branch = $isTrue ? 'true' : 'false';
            foreach ($outgoingEdges as $edge) {
                if (isset($edge['branch']) && $edge['branch'] === $branch) {
                    $nextNodeId = $edge['targetId'];
                    $nextNode = $this->getChatbotNodeById($flowData, $nextNodeId);
                    if ($nextNode) {
                        $this->sendChatbotNode($lead, $waba, $nextNode, $chatbotId, $flowData, $phoneId);
                    }
                    return;
                }
            }
            return; // Stop if no edge found
        }

        // Interactive Buttons
        if ($node['type'] === 'button' && !empty($node['config']['buttons'])) {
            $buttonsList = $node['config']['buttons'];
            $responseCount = count($buttonsList);
            
            if ($responseCount <= 3) {
                $buttons = [];
                foreach ($buttonsList as $index => $btnText) {
                    $edge = $outgoingEdges[$index] ?? $outgoingEdges[0] ?? null;
                    $targetNodeId = $edge ? $edge['targetId'] : 'end';
                    
                    $payload = "cb_{$chatbotId}_{$targetNodeId}";
                    
                    $buttons[] = [
                        'type' => 'reply',
                        'reply' => [
                            'id' => substr($payload, 0, 256), // Max 256 chars
                            'title' => substr($btnText, 0, 20) // Max 20 chars
                        ]
                    ];
                }

                $interactivePayload = [
                    'type' => 'button',
                    'body' => ['text' => $messageText ?: 'Please select an option:'],
                    'action' => [
                        'buttons' => $buttons
                    ]
                ];

                Log::info("📤 Sending Chatbot Interactive Button Node", ['lead' => $lead->id, 'interactive' => $interactivePayload]);
                
                $msg = \App\Models\WhatsAppMessage::create([
                    'box_id' => $lead->box_id,
                    'lead_id' => $lead->id,
                    'type' => 'OUTGOING',
                    'text' => $messageText,
                    'media' => ['type' => 'interactive', 'interactive' => $interactivePayload],
                    'is_sent' => false,
                    'status' => 'PENDING',
                ]);
                $whatsappHelper->sendDirectMessage($msg);
                return;
            } else {
                // Formatting as Interactive List (if > 3 options)
                $rows = [];
                foreach ($buttonsList as $index => $btnText) {
                    $edge = $outgoingEdges[$index] ?? $outgoingEdges[0] ?? null;
                    $targetNodeId = $edge ? $edge['targetId'] : 'end';
                    $payload = "cb_{$chatbotId}_{$targetNodeId}";
                    
                    $rows[] = [
                        'id' => substr($payload, 0, 200),
                        'title' => substr($btnText, 0, 24)
                    ];
                }

                $interactivePayload = [
                    'type' => 'list',
                    'body' => ['text' => $messageText ?: 'Please choose from the menu:'],
                    'action' => [
                        'button' => 'Select Option',
                        'sections' => [
                            [
                                'title' => 'Options',
                                'rows' => $rows
                            ]
                        ]
                    ]
                ];

                Log::info("📤 Sending Chatbot Interactive List Node", ['lead' => $lead->id]);
                
                $msg = \App\Models\WhatsAppMessage::create([
                    'box_id' => $lead->box_id,
                    'lead_id' => $lead->id,
                    'type' => 'OUTGOING',
                    'text' => $messageText,
                    'media' => ['type' => 'interactive', 'interactive' => $interactivePayload],
                    'is_sent' => false,
                    'status' => 'PENDING',
                ]);
                $whatsappHelper->sendDirectMessage($msg);
                return;
            }
        }

        // If no buttons, just send regular text message
        if (!empty($messageText)) {
            Log::info("📤 Sending Chatbot Text Node", ['lead' => $lead->id, 'text' => $messageText]);
            $msg = \App\Models\WhatsAppMessage::create([
                'box_id' => $lead->box_id,
                'lead_id' => $lead->id,
                'type' => 'OUTGOING',
                'text' => $messageText,
                'media' => null,
                'is_sent' => false,
                'status' => 'PENDING',
            ]);
            $whatsappHelper->sendDirectMessage($msg);
        }

        if ($node['type'] === 'question') {
            // Save session and stop to wait for user reply
            $session = \App\Models\WhatsAppChatbotSession::firstOrNew([
                'lead_id' => $lead->id,
                'box_id' => $waba->box_id
            ]);
            $session->whatsapp_chatbot_id = $chatbotId;
            $session->current_node_id = $node['id'];
            if (!$session->exists) {
                $session->data = [];
            }
            $session->save();
            return;
        }

        // Auto-advance to next node if it's a simple message/start
        if (in_array($node['type'], ['message', 'start']) && count($outgoingEdges) > 0) {
            $nextNodeId = $outgoingEdges[0]['targetId'];
            $nextNode = $this->getChatbotNodeById($flowData, $nextNodeId);
            if ($nextNode) {
                // Sleep for a short time to ensure messages are received in order on WhatsApp
                usleep(500000); // 0.5s
                $this->sendChatbotNode($lead, $waba, $nextNode, $chatbotId, $flowData, $phoneId);
            }
        }
    }

    /**
     * Handle message status updates (sent, delivered, read, etc.)
     */
    private function handleMessageStatus($value, $wabaId)
    {
        try {
            Log::info('📊 WhatsApp Message Status Update', [
                'waba_id' => $wabaId,
                'message_id' => $value['id'] ?? 'unknown',
                'status' => $value['status'] ?? 'unknown',
                'recipient_id' => $value['recipient_id'] ?? 'unknown',
            ]);

            // Update message status in database
            $msg = \App\Models\WhatsAppMessage::where('message_id', $value['id'])->first();
            if ($msg) {
                $status = $value['status'] ?? null;
                $msg->status = strtoupper($status);

                if ($status == 'delivered') {
                    $msg->is_delivered = true;
                    $msg->delivered_at = now();
                } elseif ($status == 'read') {
                    $msg->is_read = true;
                    $msg->read_at = now();
                    $msg->is_delivered = true; // If read, it must have been delivered
                } elseif ($status == 'failed') {
                    $msg->is_sent = false;
                    $errors = $value['errors'] ?? [];
                    if (!empty($errors)) {
                        $msg->error = json_encode($errors[0]);
                    }
                }
                $msg->save();
            }

        } catch (\Exception $e) {
            Log::error('❌ Error processing WhatsApp message status', [
                'waba_id' => $wabaId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle account status updates
     */
    private function handleAccountStatus($value, $wabaId)
    {
        try {
            Log::info('📊 WhatsApp Account Status Update', [
                'waba_id' => $wabaId,
                'status' => $value['status'] ?? 'unknown',
            ]);

            $waba = WhatsAppBusinessAccount::where('waba_id', $wabaId)->first();
            if ($waba && isset($value['status'])) {
                $waba->account_status = $value['status'];
                $waba->save();
            }

        } catch (\Exception $e) {
            Log::error('❌ Error processing WhatsApp account status', [
                'waba_id' => $wabaId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle phone number status updates
     */
    private function handlePhoneNumberStatus($value, $wabaId)
    {
        try {
            Log::info('📊 WhatsApp Phone Number Status Update', [
                'waba_id' => $wabaId,
                'phone_number_id' => $value['phone_number_id'] ?? 'unknown',
                'status' => $value['status'] ?? 'unknown',
            ]);

            $phoneNumber = WhatsAppPhoneNumber::where('whatsapp_id', $value['phone_number_id'])->first();
            if ($phoneNumber && isset($value['status'])) {
                $phoneNumber->status = $value['status'];
                $phoneNumber->save();
            }

        } catch (\Exception $e) {
            Log::error('❌ Error processing WhatsApp phone number status', [
                'waba_id' => $wabaId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle message template status updates
     */
    private function handleTemplateStatus($value, $wabaId)
    {
        try {
            $event = $value['event'] ?? 'unknown';
            $templateId = $value['message_template_id'] ?? null;
            $templateName = $value['message_template_name'] ?? null;

            Log::info('📊 WhatsApp Template Status Update', [
                'waba_id' => $wabaId,
                'event' => $event,
                'message_template_id' => $templateId,
                'message_template_name' => $templateName,
            ]);

            if ($templateId) {
                // Map Meta events to our statuses
                $statusMap = [
                    'APPROVED' => 'APPROVED',
                    'REJECTED' => 'REJECTED',
                    'PENDING' => 'PENDING',
                    'PAUSED' => 'PAUSED',
                    'DISABLED' => 'DISABLED',
                    'IN_APPEAL' => 'PENDING'
                ];
                
                $newStatus = $statusMap[$event] ?? null;

                $template = \App\Models\WhatsAppTemplate::where('whats_app_id', $templateId)->first();
                if ($template) {
                    if ($newStatus) {
                        $template->whats_app_status = $newStatus;
                        if ($newStatus === 'APPROVED' && !$template->approved_at) {
                            $template->approved_at = now();
                        }
                        $template->save();
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('❌ Error processing WhatsApp template status', [
                'waba_id' => $wabaId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle account update (e.g. Embedded Signup)
     */
    private function handleAccountUpdate($value, $entry_id)
    {
        try {
            $event = $value['event'] ?? 'unknown';
            $wabaInfo = $value['waba_info'] ?? null;
            
            if (!$wabaInfo || !isset($wabaInfo['waba_id'])) {
                Log::warning('⚠️ WhatsApp account_update received but missing WABA info', ['value' => $value]);
                return;
            }

            $wabaId = (string)$wabaInfo['waba_id'];
            $businessId = (string)($wabaInfo['owner_business_id'] ?? $entry_id);

            Log::info("📊 WhatsApp Account Update: {$event}", [
                'waba_id' => $wabaId,
                'business_id' => $businessId,
                'event' => $event
            ]);

            // Use a fresh query to avoid race conditions
            $existing = WhatsAppBusinessAccount::withTrashed()->where('waba_id', $wabaId)->first();
            
            if ($existing && $existing->trashed()) {
                // Archive the old ID to keep history but free up the unique waba_id
                $existing->update(['waba_id' => $wabaId . '_archived_' . now()->timestamp]);
                Log::info("📦 Archived trashed WABA from webhook for fresh start", ['waba_id' => $wabaId]);
                $existing = null;
            }
            
            // Re-check after potential archival
            if (!$existing) {
                $existing = WhatsAppBusinessAccount::where('waba_id', $wabaId)->first();
            }

            if (!$existing) {
                try {
                    WhatsAppBusinessAccount::create([
                        'waba_id' => $wabaId,
                        'whatsapp_business_portfolio_id' => $businessId,
                        'name' => 'Pending Discovery',
                        'account_status' => 'PENDING',
                        'is_active' => 0,
                        'box_id' => null,
                        'added_by' => 1 // Default admin
                    ]);
                    Log::info("🆕 Pre-registered fresh WABA from webhook", ['waba_id' => $wabaId]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // If another request created it first, just update it
                    if ($e->getCode() == 23000) {
                        Log::info("🔄 WABA created by simultaneous request, updating instead", ['waba_id' => $wabaId]);
                        $existing = WhatsAppBusinessAccount::where('waba_id', $wabaId)->first();
                        if ($existing) {
                            $existing->update([
                                'whatsapp_business_portfolio_id' => $businessId,
                                'account_status' => 'PENDING' 
                            ]);
                        }
                    } else {
                        throw $e;
                    }
                }
            } else {
                // Update business ID if changed
                $existing->update([
                    'whatsapp_business_portfolio_id' => $businessId,
                    'account_status' => 'PENDING' 
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Error processing WhatsApp account update', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the Local WhatsApp Tester Page
     */
    public function testerPage()
    {
        return view('whatsapp.tester');
    }

    /**
     * Simulate an incoming WhatsApp Message for Local Testing
     */
    public function simulateIncoming(Request $request)
    {
        $phone = $request->input('phone', '9536263009'); // Default or user input
        $message = $request->input('message', 'Hello from Local Tester!');
        $wabaId = $request->input('waba_id', '319518821236508');
        
        // Convert to 91-prefixed if needed (assumes India)
        $waId = strlen($phone) == 10 ? '91' . $phone : $phone;

        $payload = [
            "object" => "whatsapp_business_account",
            "entry" => [
                [
                    "id" => $wabaId,
                    "changes" => [
                        [
                            "value" => [
                                "messaging_product" => "whatsapp",
                                "metadata" => [
                                    "display_phone_number" => $waId,
                                    "phone_number_id" => "TEST_NUMBER_ID"
                                ],
                                "contacts" => [
                                    [
                                        "profile" => [
                                            "name" => "Local Test User"
                                        ],
                                        "wa_id" => $waId
                                    ]
                                ],
                                "messages" => [
                                    [
                                        "from" => $waId,
                                        "id" => "wamid.test_" . time(),
                                        "timestamp" => time(),
                                        "type" => "text",
                                        "text" => [
                                            "body" => $message
                                        ]
                                    ]
                                ]
                            ],
                            "field" => "messages"
                        ]
                    ]
                ]
            ]
        ];

        // Send a direct HTTP POST request to the local webhook endpoint
        $webhookUrl = url('/webhook/whatsapp');
        
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post($webhookUrl, $payload);
            return back()->with('success', 'Simulated incoming message sent! Response: ' . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Error simulating message: ' . $e->getMessage());
        }
    }
}
