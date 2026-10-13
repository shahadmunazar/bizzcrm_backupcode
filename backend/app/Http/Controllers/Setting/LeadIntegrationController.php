<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\{Box, BoxCrmIntegration, CrmIntegration, CrmSource, User, ContactStage, WebHook, FacebookPage, FacebookAccount, Industry, LostReason, LeadSubStatus, Country, State, City, BoxCustomField};
use App\Http\Controllers\Leads\LeadsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Validator, Storage, Log};
use Carbon\Carbon;
use Illuminate\Support\Str;

class LeadIntegrationController extends Controller
{
    protected $box;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->box = $request->attributes->get('box');
            return $next($request);
        });
    }

    /**
     * Get list of all integrations (Connected and Available)
     */
    public function index(Request $request)
    {
        // Check if database count matches seeder count to auto-seed new integrations
        $seederCount = count(\Database\Seeders\CrmIntegrationSeeder::getSeedData());
        if (CrmIntegration::count() < $seederCount) {
            \App\Jobs\SeedCrmIntegrationsJob::dispatch();
        }

        if (!$this->box) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to view integrations.'], 403);
        }

        // 1. Fetch lookup resources using centralized method
        $data = $this->getBoxResources([
            'countries', 'states', 'cities', 'custom_fields', 'integration_apis'
        ]);

        // 2. Load and Transform Connected APIs
        $data['connected_apis'] = BoxCrmIntegration::where('box_id', $this->box->id)
            ->with(['users', 'api'])
            ->get()
            ->map(function ($integration) {
            // Specialized logic for Facebook pages/forms
            if ($integration->api->system_name === "facebook") {
                $integration->pages = $this->getFacebookPagesWithMappedForms();
                
                // Load all connected Facebook accounts
                $integration->facebook_accounts = FacebookAccount::where('box_id', $this->box->id)
                    ->where('status', 'Enabled')
                    ->get(['id', 'name', 'facebook_id', 'created_at']);

                // Add lead statistics
                $leadSourceId = $this->box->facebook_api_params['lead_source_id'] ?? null;
                
                $integration->lead_stats = [
                    'total' => \App\Models\FacebookLead::where('box_id', $this->box->id)->count(),
                    'processed' => \App\Models\FacebookLead::where('box_id', $this->box->id)->where('status', 'Processed')->count(),
                    'pending' => \App\Models\FacebookLead::where('box_id', $this->box->id)->where('status', 'Pending')->count(),
                ];

                if ($leadSourceId) {
                    $integration->crm_stats = [
                        'assigned' => \App\Models\Lead::where('box_id', $this->box->id)
                            ->where('lead_source_id', $leadSourceId)
                            ->whereNotNull('assigned_to')
                            ->with('assignedTo:id,name')
                            ->select('assigned_to', \DB::raw('count(*) as count'))
                            ->groupBy('assigned_to')
                            ->get(),
                        'statuses' => \App\Models\Lead::where('box_id', $this->box->id)
                            ->where('lead_source_id', $leadSourceId)
                            ->with('leadStatus:id,name,color')
                            ->select('lead_status_id', \DB::raw('count(*) as count'))
                            ->groupBy('lead_status_id')
                            ->get()
                    ];
                }
            }

            // Specialized logic for Gmail
            if ($integration->api->system_name === "gmail") {
                $integration->gmail_accounts = \App\Models\GmailInbox::where('box_id', $this->box->id)
                    ->where('status', 'Enabled')
                    ->get(['id', 'email', 'last_sync_at']);
            }
            return $integration;
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get all box-specific lookup resources (Sources, Statuses, Users, etc.)
     */
    public function getResources(Request $request)
    {
        if (!$this->box) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to access resources.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $this->getBoxResources()
        ]);
    }

    /**
     * Centralized method to fetch box-specific lookups.
     * Makes the controller extremely scalable and clean.
     */
    private function getBoxResources(array $includeExtras = [])
    {
        $boxId = $this->box->id;

        $resources = [
            'sources' => CrmSource::where('box_id', $boxId)->where('status', 1)->get(),
            'users' => User::whereHas('boxes', fn($q) => $q->where('boxes.id', $boxId))->get(),
            'contactStages' => ContactStage::where('box_id', $boxId)->where('status', 1)->get(),
            'industries' => Industry::where('box_id', $boxId)->where('status', 1)->get(),
            'lostReasons' => LostReason::where('box_id', $boxId)->where('status', 1)->get(),
            'subStatuses' => LeadSubStatus::where('box_id', $boxId)->where('status', 1)->get(),
            'system_fields' => \App\Helpers\LeadFieldsHelper::getFields(),
        ];

        // Add optional heavy resources only when needed
        if (in_array('custom_fields', $includeExtras)) {
            $resources['custom_fields'] = BoxCustomField::where('box_id', $boxId)
                ->where('object', 'leads')
                ->where('status', 'Enabled')
                ->get();
        }

        if (in_array('countries', $includeExtras)) {
            $resources['countries'] = Country::where('status', 'Enable')->get();
            $resources['states'] = $this->box->country_id ?State::where('country_id', $this->box->country_id)->where('status', 'Enable')->get() : [];
            $resources['cities'] = $this->box->state_id ?City::where('state_id', $this->box->state_id)->where('status', 'Enable')->get() : [];
        }

        if (in_array('integration_apis', $includeExtras)) {
            $resources['available_apis'] = CrmIntegration::where('status', 'Enabled')->get();
        }

        return $resources;
    }

    /**
     * Helper for Facebook dynamic relationship loading
     */
    private function getFacebookPagesWithMappedForms()
    {
        return FacebookPage::with(['forms' => fn($q) => $q->where('is_mapped', 'Yes')])
            ->where('box_id', $this->box->id)
            ->whereHas('forms', fn($q) => $q->where('is_mapped', 'Yes'))
            ->get();
    }

    /**
     * Add a link between an API and the Box (initial step)
     */
    public function addApi(Request $request)
    {
        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'create', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to add integrations.'], 403);
        }

        $apiId = $request->api_id ?? ($request->item['id'] ?? null);
        $api = CrmIntegration::where('status', 'Enabled')->find($apiId);

        if (!$api) {
            return response()->json(['success' => false, 'message' => 'Invalid API'], 400);
        }

        if ($api->system_name == "facebook") {
            if (empty($this->box->facebook_api_params)) {
                return response()->json(['success' => false, 'message' => 'Please configure with facebook first'], 400);
            }
        }

        $integration = BoxCrmIntegration::create([
            'lead_api_id' => $api->id,
            'box_id' => $this->box->id,
            'status' => 'Disabled',
            'connection_status' => 'Disconnected',
            'last_checked_date' => now()->diffForHumans(),
            'params' => ($api->system_name == 'webhook') ? ['webhook_slug' => Str::random(10)] : []
        ]);

        return response()->json(['success' => true, 'item' => $integration->load('api')]);
    }

    /**
     * Update integration status and parameters
     */
    public function toggleStatus(Request $request)
    {
        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to update integrations.'], 403);
        }

        $id = $request->id ?? ($request->item['id'] ?? ($request->api_id ?? null));

        $integration = BoxCrmIntegration::with('api')
            ->where('box_id', $this->box->id)
            ->find($id);

        if (!$integration) {
            // Fallback for when lead_api_id is passed instead of junction ID
            $integration = BoxCrmIntegration::with('api')
                ->where('box_id', $this->box->id)
                ->where('lead_api_id', $id)
                ->first();
        }

        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Invalid API Integration'], 400);
        }

        $status = $request->status ?? ($request->item['status'] ?? null);
        $params = $request->params ?? ($request->item['params'] ?? null);

        if ($status && $status != "Save") {
            if ($integration->api->system_name == "facebook" && $status == 'Enabled') {
                if (empty($this->box->facebook_api_params)) {
                    return response()->json(['success' => false, 'message' => 'Please configure with facebook first'], 400);
                }
            }
            $integration->status = $status;
            $integration->connection_status = ($status == 'Enabled') ? 'Connected' : 'Disconnected';
        }

        if ($params) {
            // Convert 'Has Words' comma-separated string to array
            if (isset($params['has_words']) && is_string($params['has_words'])) {
                $params['has_words'] = array_filter(array_map('trim', explode(',', $params['has_words'])));
            }

            // Preserve webhook_slug if it exists in DB but not in request
            if (isset($integration->params['webhook_slug']) && !isset($params['webhook_slug'])) {
                $params['webhook_slug'] = $integration->params['webhook_slug'];
            }
            $integration->params = $params;

            // Default to connected, but might be overridden by specific checks
            $integration->connection_status = "Connected";

            // Sync users if round_robin_users is passed
            $robinUsers = $params['round_robin_users'] ?? [];
            if (!empty($robinUsers)) {
                $integration->users()->sync($robinUsers);
            }

            // Connection Check for Specific APIs
            if ($integration->status == "Enabled") {
                if ($integration->api->system_name == "gmail") {
                    $googleConnected = \App\Models\GmailInbox::where('box_id', $this->box->id)
                        ->where('status', 'Enabled')
                        ->exists();
                    if (!$googleConnected) {
                        return response()->json(['success' => false, 'message' => 'Please connect your Google account first using the "Connect Gmail" button above.'], 400);
                    }
                    $integration->connection_status = "Connected";
                }
            }
        }

        if ($integration->status == "Enabled" && $integration->api->system_name == "webhook") {
            if (empty($integration->token)) {
                $integration->token = uniqid() . Str::random(20);
            }
            // Ensure slug exists
            $currentParams = $integration->params ?? [];
            if (empty($currentParams['webhook_slug'])) {
                $currentParams['webhook_slug'] = Str::random(10);
                $integration->params = $currentParams;
            }
        }

        $integration->last_checked_date = now()->diffForHumans();
        $integration->save();

        return response()->json([
            'success' => true,
            'message' => 'Integration updated successfully',
            'item' => $integration->load(['api', 'users'])
        ]);
    }

    /**
     * Regenerate unique token for Webhook
     */
    public function regenerateToken(Request $request)
    {
        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to regenerate tokens.'], 403);
        }

        $id = $request->id ?? ($request->item['id'] ?? null);
        $integration = BoxCrmIntegration::where('box_id', $this->box->id)->find($id);

        if (!$integration || $integration->api->system_name != 'webhook') {
            return response()->json(['success' => false, 'message' => 'Invalid API'], 400);
        }

        $integration->token = uniqid() . Str::random(20);
        $integration->save();

        return response()->json([
            'success' => true,
            'token' => $integration->token,
            'message' => 'Token regenerated',
            'item' => $integration->load('api')
        ]);
    }

    private function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]);
        }
        elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        else if (!empty(request()->input('auth'))) {
            return request()->input('auth');
        }
        return null;
    }

    /**
     * Main Webhook endpoint for receiving leads
     */
    public function webhook(Request $request, $id)
    {
        $requestData = $request->all();
        $accessToken = $this->getBearerToken();

        // 1. Log incoming request for debugging
        $this->logRawWebhook($id, $requestData);

        if (empty($accessToken)) {
            return response()->json(['success' => false, 'message' => 'Authorization token required'], 401);
        }

        // 2. Identify the integration (Slug or ID)
        $integration = $this->findIntegration($id);
        if (!$integration || $integration->api->system_name !== 'webhook') {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive Webhook integration'], 404);
        }

        // 3. Security Check
        if ($accessToken !== $integration->token || empty($integration->token)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Invalid token'], 401);
        }

        if (empty($requestData)) {
            return response()->json(['success' => false, 'message' => 'No data received'], 400);
        }

        // Health Check / Ping mode (allows verifying endpoint connectivity without creating leads)
        if ($request->boolean('ping') || $request->boolean('test') || $request->boolean('health_check')) {
            $integration->update([
                'last_checked_date' => now()->toDateTimeString()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Webhook connection verified successfully! Ready to receive leads.',
                'integration_id' => $integration->id,
                'source_name' => $integration->params['source_name'] ?? 'WebHook',
                'box' => $integration->box->name ?? null
            ]);
        }

        // 4. Normalize incoming field aliases (mobile -> phone, full_name -> name, etc.) and clean phone
        $requestData = $this->normalizeWebhookData($requestData);

        // 5. Validate Lead Data based on Box requirements
        $validator = $this->validateWebhookData($integration->box, $requestData);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 6. Build Log Record
        $webhookLog = $this->createWebhookLog($integration, $requestData);

        // 7. Process Lead
        try {
            $this->processWebhookLead($integration, $requestData, $webhookLog);

            $integration->update([
                'last_lead_date' => now()->toDateTimeString(),
                'last_checked_date' => now()->diffForHumans()
            ]);

            return response()->json(['success' => true, 'message' => 'Lead processed successfully', 'log_id' => $webhookLog->id]);
        }
        catch (\Exception $e) {
            Log::error("Webhook Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lead processing failed', 'error' => $e->getMessage()], 500);
        }
    }

    private function normalizeWebhookData(array $data): array
    {
        // Flatten nested wrappers if sent as { "data": { ... } } or { "lead": { ... } }
        if (isset($data['data']) && is_array($data['data'])) {
            $data = array_merge($data['data'], $data);
        } elseif (isset($data['lead']) && is_array($data['lead'])) {
            $data = array_merge($data['lead'], $data);
        } elseif (isset($data['payload']) && is_array($data['payload'])) {
            $data = array_merge($data['payload'], $data);
        }

        // 1. Phone / Mobile alias
        if (empty($data['phone'])) {
            $data['phone'] = $data['mobile'] ?? $data['contact'] ?? $data['phone_number'] ?? $data['contact_no'] ?? $data['cell'] ?? $data['mobile_number'] ?? $data['Phone'] ?? $data['Mobile'] ?? null;
        }

        if (!empty($data['phone'])) {
            $cleaned = preg_replace('/[^\d+]/', '', (string)$data['phone']);
            // Strip leading +91 or 91 if length is 12 (Indian country code)
            if (str_starts_with($cleaned, '+91')) {
                $cleaned = substr($cleaned, 3);
            } elseif (strlen($cleaned) === 12 && str_starts_with($cleaned, '91')) {
                $cleaned = substr($cleaned, 2);
            } elseif (strlen($cleaned) === 11 && str_starts_with($cleaned, '0')) {
                $cleaned = substr($cleaned, 1);
            }
            $data['phone'] = $cleaned;
        }

        // 2. Name alias
        if (empty($data['name'])) {
            $data['name'] = $data['full_name'] ?? $data['first_name'] ?? $data['sender_name'] ?? $data['caller_name'] ?? $data['lead_name'] ?? $data['Name'] ?? null;
            if (!empty($data['first_name']) && !empty($data['last_name'])) {
                $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);
            }
        }

        // 3. Email alias
        if (empty($data['email'])) {
            $data['email'] = $data['email_id'] ?? $data['email_address'] ?? $data['sender_email'] ?? $data['mail'] ?? $data['Email'] ?? null;
        }

        // 4. Message / Query alias
        if (empty($data['query'])) {
            $data['query'] = $data['message'] ?? $data['comments'] ?? $data['requirement'] ?? $data['inquiry'] ?? $data['description'] ?? $data['msg'] ?? null;
        }

        // 5. Company alias
        if (empty($data['company_name'])) {
            $data['company_name'] = $data['company'] ?? $data['organization'] ?? $data['firm_name'] ?? null;
        }

        // 6. City / State alias
        if (empty($data['city'])) {
            $data['city'] = $data['sender_city'] ?? $data['City'] ?? null;
        }
        if (empty($data['state'])) {
            $data['state'] = $data['sender_state'] ?? $data['State'] ?? null;
        }

        return $data;
    }

    private function findIntegration($id)
    {
        $query = BoxCrmIntegration::with(['api', 'box'])->where('status', 'Enabled');

        if (is_numeric($id)) {
            return $query->find($id);
        }

        return $query->where('params->webhook_slug', $id)->first();
    }

    private function logRawWebhook($id, $data)
    {
        $logDir = storage_path("app/webhook/raw");
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $file = $logDir . "/" . date('Y_m_d') . ".log";
        $content = "[" . date('Y-m-d H:i:s') . "] ID: $id | Data: " . json_encode($data) . "\n";
        file_put_contents($file, $content, FILE_APPEND);
    }

    private function validateWebhookData($box, $data)
    {
        $requiredField = $box->lead_required_field ?? 'email';

        $rules = [
            'email' => ($requiredField === 'email' ? ['required', 'email'] : ['nullable', 'email']),
            'phone' => ($requiredField === 'phone' ? ['required', 'regex:/^(\+?[1-9]\d{9,14}|[6-9]\d{9})$/'] : ['nullable', 'regex:/^(\+?[1-9]\d{9,14}|[6-9]\d{9})$/'])
        ];

        return Validator::make($data, $rules);
    }

    private function createWebhookLog($integration, $data)
    {
        $systemFields = \App\Helpers\LeadFieldsHelper::getFields();
        $fieldsToExclude = array_merge(
            array_column($systemFields, 'field'),
        ['last_name', 'mobile', 'sec_phone', 'gstin_number', 'pan_number']
        );

        $webhookLog = new WebHook();
        $webhookLog->lead_api_box_id = $integration->id;
        $webhookLog->box_id = $integration->box_id;
        $webhookLog->name = $data['name'] ?? null;
        $webhookLog->email = $data['email'] ?? null;
        $webhookLog->phone = $data['phone'] ?? null;
        $webhookLog->query = $data['query'] ?? null;
        $webhookLog->company_name = $data['company_name'] ?? null;
        $webhookLog->params = array_diff_key($data, array_flip($fieldsToExclude));
        $webhookLog->save();

        return $webhookLog;
    }

    private function processWebhookLead($integration, $data, $webhookLog)
    {
        $box = $integration->box;
        $sourceName = $integration->params['source_name'] ?? 'WebHook';
        $data['Submitted From'] = $sourceName . ' - WebHook';

        $filterData = [
            'id' => $integration->id,
            'filter_type' => 'api',
            'round_robin' => $integration->params['round_robin'] ?? 0,
            'round_robin_users' => $integration->params['round_robin_users'] ?? null,
            'assigned_to' => $integration->params['assigned_to'] ?? null,
            'lead_status_id' => $integration->params['lead_status_id'] ?? null,
            'lead_source_id' => $integration->params['lead_source_id'] ?? null,
            'auto_trash' => $integration->params['auto_trash'] ?? [],
            'has_words' => $integration->params['has_words'] ?? [],
            'auto_unassigned' => $integration->params['auto_unassigned'] ?? [],
            'enable_shifts' => $integration->params['enable_shifts'] ?? 'No',
            'shifts' => $integration->params['shifts'] ?? [],
        ];

        $mapper = [];
        $systemFields = \App\Helpers\LeadFieldsHelper::getFields();
        foreach ($systemFields as $f) {
            $mapper[$f['field']] = $f['field'];
        }
        $mapper['last_name'] = 'last_name';
        $mapper['mobile'] = 'mobile';
        $mapper['sec_phone'] = 'sec_phone';

        $leadsController = new LeadsController();
        $lead = $leadsController->saveLead($box, $data, $filterData, $mapper, $webhookLog->created_at, $box->superadmin_id, $sourceName . ' - WebHook');

        if ($lead) {
            $webhookLog->update([
                'status' => $lead->wasRecentlyCreated ? 'Imported' : 'Updated',
                'lead_id' => $lead->id
            ]);
        }
        else {
            $webhookLog->update(['status' => 'Skipped']);
        }
    }

    public function callerdesk()
    {
        $requestData = request()->all();
        Log::info("CallerDesk Webhook: " . json_encode($requestData));
        return response()->json(['success' => true, 'message' => 'logged']);
    }

    public function leadApis(Request $request)
    {

        $userId = Auth::id();
        $boxUser = \App\Models\BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'lead_apis'))) {
            abort(403, 'You do not have permission to access lead integrations settings.');
        }

        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }



        $integrations = CrmIntegration::all()->toArray();

        $integrations = array_map(function ($item) {
            if (is_string($item['params'])) {
                $item['params'] = json_decode($item['params'], true);
            }
            return $item;
        }, $integrations);

        $facebookConnected = false;
        if ($box) {
            $box->refresh();
            $facebookConnected = !empty($box->facebook_api_params)
                && isset($box->facebook_api_params['access_token'])
                && !empty($box->facebook_api_params['access_token']);
        }

        $whatsappConnected = false;
        if ($box) {
            $box->refresh();
            $whatsappConnected = !empty($box->whatsapp_token)
                && isset($box->whatsapp_token['access_token'])
                && !empty($box->whatsapp_token['access_token']);
        }

        return view('settings.lead-apis', compact('box', 'facebookConnected', 'whatsappConnected', 'integrations'));
    }

    public function getStatesByCountry($box_slug, Request $request)
    {
        $countryId = $request->country_id;
        $states = State::where('country_id', $countryId)->where('status', 'Enable')->get();
        return response()->json(['success' => true, 'data' => $states]);
    }

    public function getCitiesByState($box_slug, Request $request)
    {
        $stateId = $request->state_id;
        $cities = City::where('state_id', $stateId)->where('status', 'Enable')->get();
        return response()->json(['success' => true, 'data' => $cities]);
    }

    public function syncLegacyApis(Request $request)
    {
        try {
            $columnMapping = [
                'lead_api_id' => 'lead_api_id',
                'box_id'      => 'box_id',
                'status'      => 'status',
                'connection_status' => 'connection_status',
                'last_checked_date' => 'last_checked_date',
                'error_log'   => 'error_log',
                'params'      => 'params',
                'last_lead_date' => 'last_lead_date',
                'token'       => 'token',
                'created_at'  => 'created_at',
                'updated_at'  => 'updated_at',
                'deleted_at'  => 'deleted_at',
            ];

            // Mapping from legacy lead_apis IDs to new crm_integrations IDs
            $idMapping = [
                1 => 2, // India Mart
                2 => 7, // Pharma Khabar
                3 => 9, // Trade India
                4 => 8, // Trade XL
                5 => 5, // 99 Acers
                6 => 6, // Web Hook
                7 => 3, // Pharma Hopers
                8 => 4, // Pharma Vends
                9 => 1, // Facebook
            ];

            // Fetch data from lead_api_boxes which was imported into bizzcrm
            $leadApiBoxes = \Illuminate\Support\Facades\DB::connection('bizzcrm_migrate')->table('lead_api_boxes')->get();

            $syncedCount = 0;
            foreach ($leadApiBoxes as $item) {
                // Helper function to safely get mapped column data
                $getValue = function($targetCol, $default = null) use ($item, $columnMapping) {
                    $sourceCol = $columnMapping[$targetCol] ?? $targetCol;
                    return property_exists($item, $sourceCol) ? $item->{$sourceCol} : $default;
                };

                $oldApiId = $getValue('lead_api_id');
                $newApiId = $idMapping[$oldApiId] ?? $oldApiId; // fallback to original if not found in map

                \Illuminate\Support\Facades\DB::table('box_crm_integrations')->updateOrInsert(
                    [
                        // We use ID if it exists, otherwise we match by lead_api_id & box_id combination
                        'id' => property_exists($item, 'id') ? $item->id : null,
                    ],
                    [
                        'lead_api_id' => $newApiId,
                        'box_id' => $getValue('box_id'),
                        'status' => $getValue('status', 'active'),
                        'connection_status' => $getValue('connection_status'),
                        'last_checked_date' => $getValue('last_checked_date'),
                        'error_log' => $getValue('error_log'),
                        'params' => empty($getValue('params')) ? null : $getValue('params'),
                        'last_lead_date' => $getValue('last_lead_date'),
                        'token' => $getValue('token'),
                        'created_at' => $getValue('created_at', now()),
                        'updated_at' => $getValue('updated_at', now()),
                        'deleted_at' => $getValue('deleted_at'),
                    ]
                );
                $syncedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Legacy API integrations synced successfully! Processed {$syncedCount} records.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync legacy APIs: ' . $e->getMessage()
            ], 500);
        }
    }
}
