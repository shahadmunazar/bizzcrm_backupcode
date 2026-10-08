<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Box,
    Lead,
    WhatsAppBusinessAccount,
    WhatsAppCampaign,
    WhatsAppChatbot,
    WhatsAppMessage,
    WhatsAppPhoneNumber,
    WhatsAppProject,
    WhatsAppQuickReply,
    WhatsAppTemplate
};
use App\Helpers\WhatsApp;
use Illuminate\Support\Facades\{Http, Log};
class WhatsAppApiController extends Controller
{
    /**
     * Authenticate the request using the Bearer token.
     */
    private function authenticate(Request $request)
    {
        // 1. Allow authenticated Mobile App / Web users (Sanctum)
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            if ($boxUser) {
                return \App\Models\Box::find($boxUser->box_id);
            }
        }

        // 2. Fallback to Developer API Token
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }
        return Box::where('whatsapp_api_token', $token)->first();
    }

    /**
     * Helper to get the active WhatsApp account for the box.
     */
    private function getActiveWhatsAppAccount($box)
    {
        return WhatsAppPhoneNumber::where('box_id', $box->id)
            ->where('is_active', true)
            ->first() ?? WhatsAppPhoneNumber::where('box_id', $box->id)->first();
    }

    public function dashboard(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $totalSent = WhatsAppMessage::where('box_id', $box->id)->where('type', 'OUTGOING')->count();
        $totalReceived = WhatsAppMessage::where('box_id', $box->id)->where('type', 'INCOMING')->count();
        $totalCampaigns = WhatsAppCampaign::where('box_id', $box->id)->count();
        
        $activeChats = Lead::whereHas('whatsappMessages', function($q) use ($box) {
            $q->where('box_id', $box->id)
              ->where('created_at', '>=', now()->subHours(24));
        })->count();

        // Calculate Daily Limit
        $dailyLimit = 'Unknown';
        $limitRemaining = 'Unknown';
        $sentToday = 0;
        
        $phoneNumbers = WhatsAppPhoneNumber::where('box_id', $box->id)->where('is_active', 1)->get();
        if ($phoneNumbers->count() > 0) {
            $activePhone = $phoneNumbers->first();
            $tier = 'TIER_UNKNOWN';
            
            if (!empty($activePhone->metadata) && is_array($activePhone->metadata) && isset($activePhone->metadata['status_data']['messaging_limit_tier'])) {
                $tier = $activePhone->metadata['status_data']['messaging_limit_tier'];
            }
            
            $limitMap = [
                'TIER_250' => 250,
                'TIER_1K' => 1000,
                'TIER_2K' => 2000,
                'TIER_10K' => 10000,
                'TIER_100K' => 100000,
                'TIER_UNLIMITED' => 'Unlimited',
            ];
            $dailyLimit = $limitMap[$tier] ?? 250;
            
            // Special override for known account
            $activeWaba = WhatsAppBusinessAccount::where('box_id', $box->id)->where('is_active', 1)->first();
            if ($activeWaba && $activeWaba->waba_id == '319518821236508' && $tier == 'TIER_UNKNOWN') {
                $dailyLimit = 2000;
            }
            
            $sentToday = WhatsAppMessage::where('sender_id', $activePhone->whatsapp_id)
                ->where('created_at', '>=', now()->subHours(24))
                ->count();
                
            $limitRemaining = is_numeric($dailyLimit) ? max(0, $dailyLimit - $sentToday) : 'Unlimited';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_sent_messages' => $totalSent,
                'incoming_messages' => $totalReceived,
                'total_campaigns' => $totalCampaigns,
                'active_chats' => $activeChats,
                'ai_response_credits' => $box->ai_credits ?? 0,
                'daily_limit' => $dailyLimit,
                'daily_limit_left' => $limitRemaining,
            ]
        ]);
    }

    public function accounts(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $accounts = WhatsAppPhoneNumber::where('box_id', $box->id)->get();

        return response()->json([
            'success' => true,
            'data' => $accounts
        ]);
    }

    public function connectAccount(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'business_id' => 'required|string',
            'waba_id' => 'required|string',
            'phone_number_id' => 'required|string',
            'phone_number' => 'required|string',
            'display_name' => 'nullable|string',
            'access_token' => 'required|string'
        ]);

        $account = WhatsAppPhoneNumber::updateOrCreate(
            ['box_id' => $box->id, 'whatsapp_id' => $request->phone_number_id],
            [
                'phone' => $request->phone_number,
                'name' => $request->display_name ?? 'WhatsApp Account',
                'is_active' => true
            ]
        );

        // Also update the box token
        $box->update([
            'whatsapp_token' => [
                'access_token' => $request->access_token,
                'business_id' => $request->business_id,
                'waba_id' => $request->waba_id
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp account connected successfully',
            'data' => $account
        ]);
    }

    /**
     * Send a WhatsApp message (Text or Template)
     */
    public function send(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        $request->validate([
            'phone' => 'required|string',
            'type' => 'required|in:text,template',
            'message' => 'required_if:type,text|string',
            'template_name' => 'required_if:type,template|string',
            'language' => 'nullable|string',
            'components' => 'nullable|array',
        ]);

        $activePhone = $this->getActiveWhatsAppAccount($box);
        if (!$activePhone) {
            return response()->json(['success' => false, 'message' => 'No active WhatsApp account found for this Box.'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
        if (strlen($cleanPhone) < 10) {
            return response()->json(['success' => false, 'message' => 'Invalid phone number format.'], 400);
        }

        // Find or create a Lead
        $lead = Lead::where('box_id', $box->id)
            ->where(function($q) use ($cleanPhone) {
                $q->where('phone', 'like', "%$cleanPhone%")
                  ->orWhere('sec_phone', 'like', "%$cleanPhone%");
            })->first();

        if (!$lead) {
            $lead = Lead::create([
                'box_id' => $box->id,
                'name' => 'API Contact ' . $cleanPhone,
                'phone' => $cleanPhone,
            ]);
        }

        $waToken = is_array($box->whatsapp_token) ? ($box->whatsapp_token['access_token'] ?? null) : null;
        if (!$waToken) {
            return response()->json(['success' => false, 'message' => 'WhatsApp is not properly configured for this Box.'], 400);
        }

        $url = "https://graph.facebook.com/v19.0/{$activePhone->whatsapp_id}/messages";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $cleanPhone,
        ];

        $whatsappTemplateId = null;

        if ($request->type === 'text') {
            $payload['type'] = 'text';
            $payload['text'] = ['body' => $request->message];
            $messageText = $request->message;
        } else {
            $payload['type'] = 'template';
            $payload['template'] = [
                'name' => $request->template_name,
                'language' => ['code' => $request->language ?? 'en_US'],
            ];
            
            $dbTemplate = WhatsAppTemplate::where('box_id', $box->id)->where('name', $request->template_name)->first();
            if ($dbTemplate) {
                $whatsappTemplateId = $dbTemplate->id;
            }

            if ($request->has('template_params') && is_array($request->template_params)) {
                // Construct components using the simplified template_params mapping
                $parameters = [];
                if ($dbTemplate && isset($dbTemplate->params['api_config']['aliases'])) {
                    $aliases = $dbTemplate->params['api_config']['aliases'];
                    // aliases format: "1" => "customer_name"
                    
                    // We need to build the parameters in sequential order
                    $maxVar = max(array_keys($aliases) ?: [0]);
                    for ($i = 1; $i <= $maxVar; $i++) {
                        $key = (string) $i;
                        if (isset($aliases[$key])) {
                            $aliasName = $aliases[$key];
                            $val = $request->template_params[$aliasName] ?? '';
                            $parameters[] = ['type' => 'text', 'text' => (string) $val];
                        } else {
                            $parameters[] = ['type' => 'text', 'text' => ''];
                        }
                    }
                } else {
                    // Fallback if no aliases defined, they can just pass {"1": "val1"}
                    foreach ($request->template_params as $key => $val) {
                        $parameters[] = ['type' => 'text', 'text' => (string) $val];
                    }
                }

                if (!empty($parameters)) {
                    $payload['template']['components'] = [
                        [
                            'type' => 'body',
                            'parameters' => $parameters
                        ]
                    ];
                }
            } elseif ($request->has('components') && is_array($request->components)) {
                $payload['template']['components'] = $request->components;
            }
            
            $messageText = "Template: " . $request->template_name;
        }

        $response = Http::withToken($waToken)->post($url, $payload);
        $resData = $response->json();

        // Log the message in BizzCRM
        $message = WhatsAppMessage::create([
            'box_id' => $box->id,
            'lead_id' => $lead->id,
            'sender_id' => $activePhone->whatsapp_id,
            'type' => 'OUTGOING',
            'status' => $response->successful() ? 'SENT' : 'FAILED',
            'text' => $messageText,
            'media' => [
                'type' => 'developer_api',
                'api_source' => 'developer_api',
                'payload' => $payload,
                'template_name' => $request->type === 'template' ? $request->template_name : null,
                'whatsapp_template_id' => $whatsappTemplateId
            ],
            'message_id' => $resData['messages'][0]['id'] ?? null,
            'error' => $response->successful() ? null : json_encode($resData)
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true, 
                'message' => 'Message sent successfully', 
                'data' => [
                    'message_id' => $resData['messages'][0]['id'] ?? null
                ]
            ]);
        } else {
            Log::error('API WhatsApp Send Failed', ['response' => $resData]);
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send message via Meta API', 
                'error' => $resData
            ], 500);
        }
    }

    /**
     * Fetch messages for the authenticated Box.
     */
    public function messages(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        $query = WhatsAppMessage::where('box_id', $box->id)->with('lead:id,name,phone');

        if ($request->has('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
            $query->whereHas('lead', function($q) use ($cleanPhone) {
                $q->where('phone', 'like', "%$cleanPhone%")
                  ->orWhere('sec_phone', 'like', "%$cleanPhone%");
            });
        }

        if ($request->has('type')) {
            $query->where('type', strtoupper($request->type));
        }

        $limit = (int) $request->input('limit', 20);
        if ($limit > 100) $limit = 100;

        $messages = $query->latest()->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function chats(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $tab = $request->get('tab', 'active');

        // Base query with extreme performance using correlated join
        $baseQuery = Lead::select('leads.*', 'wm.type as lm_type', 'wm.created_at as lm_date')
            ->join('whatsapp_messages as wm', function ($join) use ($box) {
                $join->on('wm.lead_id', '=', 'leads.id')
                     ->where('wm.box_id', '=', $box->id)
                     ->where('wm.id', '=', \Illuminate\Support\Facades\DB::raw("(SELECT MAX(id) FROM whatsapp_messages sub WHERE sub.lead_id = leads.id AND sub.box_id = {$box->id} AND sub.deleted_at IS NULL)"));
            })
            ->where('leads.deleted_at', null);

        // Calculate counts quickly in a single query
        $countsSql = "
            SELECT 
                SUM(CASE WHEN lm_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN lm_type = 'OUTGOING' AND lm_date < DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN lm_type = 'INCOMING' AND lm_date < DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as notContacted
            FROM (" . $baseQuery->toSql() . ") as sub
        ";
        $countsObj = \Illuminate\Support\Facades\DB::selectOne($countsSql, $baseQuery->getBindings());

        // Fast count for unread using EXISTS
        $unreadCount = Lead::whereHas('whatsappMessages', function($q) use ($box) {
            $q->where('box_id', $box->id)->where('is_read', false)->where('type', 'INCOMING');
        })->count();

        $counts = [
            'active' => (int) ($countsObj->active ?? 0),
            'unread' => $unreadCount,
            'inactive' => (int) ($countsObj->inactive ?? 0),
            'notContacted' => (int) ($countsObj->notContacted ?? 0),
        ];

        // Apply Tab Filter
        $query = clone $baseQuery;
        if ($tab === 'unread') {
            $query->whereHas('whatsappMessages', function($q) use ($box) {
                $q->where('box_id', $box->id)->where('is_read', false)->where('type', 'INCOMING');
            });
        } elseif ($tab === 'inactive') {
            $query->where('wm.type', 'OUTGOING')->where('wm.created_at', '<', now()->subHours(24));
        } elseif ($tab === 'notContacted') {
            $query->where('wm.type', 'INCOMING')->where('wm.created_at', '<', now()->subHours(24));
        } else {
            // Default to 'active'
            $query->where('wm.created_at', '>=', now()->subHours(24));
        }

        // Fetch Paginated Leads with counts
        $chats = $query->withCount(['whatsappMessages as unread_count' => function($q) use ($box) {
                $q->where('box_id', $box->id)->where('is_read', false)->where('type', 'INCOMING');
            }])
            ->orderByDesc('lm_date')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'counts' => $counts,
            'data' => $chats
        ]);
    }

    public function chatMessages(Request $request, $lead_id)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $messages = WhatsAppMessage::where('box_id', $box->id)
            ->where('lead_id', $lead_id)
            ->latest()
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function projects(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $projects = WhatsAppProject::where('box_id', $box->id)->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    public function templates(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $templates = WhatsAppTemplate::where('box_id', $box->id)->get();

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    public function chatbots(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $chatbots = WhatsAppChatbot::where('box_id', $box->id)->get();

        return response()->json([
            'success' => true,
            'data' => $chatbots
        ]);
    }

    public function quickReplies(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $quickReplies = WhatsAppQuickReply::where('box_id', $box->id)->get();

        return response()->json([
            'success' => true,
            'data' => $quickReplies
        ]);
    }

    public function campaigns(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $campaigns = WhatsAppCampaign::where('box_id', $box->id)->with('template')->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    public function storeCampaign(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'required|exists:whatsapp_templates,id',
            'audience_type' => 'required|string',
            'schedule_at' => 'nullable|date'
        ]);

        $campaign = WhatsAppCampaign::create([
            'box_id' => $box->id,
            'name' => $request->name,
            'whatsapp_template_id' => $request->template_id,
            'audience_type' => $request->audience_type,
            'status' => 'DRAFT',
            'scheduled_at' => $request->schedule_at
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully',
            'data' => $campaign
        ]);
    }

    public function storeProject(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $project = WhatsAppProject::create([
            'box_id' => $box->id,
            'name' => $request->name,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project
        ]);
    }

    public function reactMessage(Request $request, $id)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'emoji' => 'required|string'
        ]);

        $message = WhatsAppMessage::where('box_id', $box->id)->findOrFail($id);
        $activePhone = $this->getActiveWhatsAppAccount($box);
        $waToken = is_array($box->whatsapp_token) ? ($box->whatsapp_token['access_token'] ?? null) : null;

        if (!$activePhone || !$waToken) {
            return response()->json(['success' => false, 'message' => 'WhatsApp configuration missing.'], 400);
        }

        $url = "https://graph.facebook.com/v19.0/{$activePhone->whatsapp_id}/messages";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $message->lead->phone,
            'type' => 'reaction',
            'reaction' => [
                'message_id' => $message->message_id,
                'emoji' => $request->emoji
            ]
        ];

        $response = Http::withToken($waToken)->post($url, $payload);
        
        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Reaction sent']);
        }
        
        return response()->json(['success' => false, 'message' => 'Failed to send reaction', 'error' => $response->json()], 500);
    }

    public function sendMedia(Request $request)
    {
        $box = $this->authenticate($request);
        if (!$box) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'phone' => 'required|string',
            'media_type' => 'required|in:image,video,document,audio',
            'media_url' => 'required_without:media_id|url',
            'media_id' => 'required_without:media_url|string',
            'caption' => 'nullable|string'
        ]);

        $activePhone = $this->getActiveWhatsAppAccount($box);
        $waToken = is_array($box->whatsapp_token) ? ($box->whatsapp_token['access_token'] ?? null) : null;

        if (!$activePhone || !$waToken) {
            return response()->json(['success' => false, 'message' => 'WhatsApp configuration missing.'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
        $url = "https://graph.facebook.com/v19.0/{$activePhone->whatsapp_id}/messages";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanPhone,
            'type' => $request->media_type,
            $request->media_type => []
        ];

        if ($request->has('media_id')) {
            $payload[$request->media_type]['id'] = $request->media_id;
        } else {
            $payload[$request->media_type]['link'] = $request->media_url;
        }

        if ($request->caption && in_array($request->media_type, ['image', 'video', 'document'])) {
            $payload[$request->media_type]['caption'] = $request->caption;
        }

        $response = Http::withToken($waToken)->post($url, $payload);
        
        if ($response->successful()) {
            // Log message
            $resData = $response->json();
            $lead = Lead::firstOrCreate(['box_id' => $box->id, 'phone' => $cleanPhone], ['name' => 'API Contact ' . $cleanPhone]);
            
            WhatsAppMessage::create([
                'box_id' => $box->id,
                'lead_id' => $lead->id,
                'sender_id' => $activePhone->whatsapp_id,
                'type' => 'OUTGOING',
                'status' => 'SENT',
                'text' => $request->caption ?? ucfirst($request->media_type) . ' attached',
                'media' => $payload[$request->media_type],
                'message_id' => $resData['messages'][0]['id'] ?? null,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Media sent successfully',
                'data' => ['message_id' => $resData['messages'][0]['id'] ?? null]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Failed to send media', 'error' => $response->json()], 500);
    }
}
