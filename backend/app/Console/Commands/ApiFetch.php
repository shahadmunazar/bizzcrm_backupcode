<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\{BoxCrmIntegration as LeadApiBox, BoxCustomField, FacebookForm, FacebookLead, IndiaMartLead, PharmaKhabarLead, TradeIndiaLead, TradeXlLead, NintyNineAcersLead, PharmaHoperLead, PharmaVendLead, WebHook};
use App\Http\Controllers\Leads\LeadsController;
use App\Helpers\WhatsApp;

class ApiFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:fetch {box_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch leads from configured APIs';
    protected $apilist;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $LeadController = new LeadsController;

        $id = $this->argument('box_id');
        if ($id) {
            /** @var LeadApiBox[] $apilists */
            $apilists = LeadApiBox::with('api', 'box', 'users')->where('box_id', $id)->where('status', 'Enabled')->get();
        } else {
            /** @var LeadApiBox[] $apilists */
            $apilists = LeadApiBox::with(['api', 'box', 'users'])
                ->where('status', 'Enabled')
                ->where(function ($query) {
                    $now = new Carbon;
                    $now->subMinutes(1);
                    $query->whereNull('last_checked_date')->orWhere('last_checked_date', '<', $now);
                })
                ->where('lead_api_id', '<>', '6')
                ->orderBy('last_checked_date', 'asc')
                ->get();
        }

        foreach ($apilists as $apilist) {
            $this->apilist = $apilist;
            $params = $apilist->params;
            $key = $apilist->params['API Key'] ?? $apilist->params['CRM Key'] ?? $apilist->params['key'] ?? '';
            $this->info("Processing API ID: {$apilist->lead_api_id} ({$apilist->api->name})");
            Log::info("ApiFetch: Processing API for Box [{$apilist->box_id}] - {$apilist->api->name} (ID: {$apilist->lead_api_id})");
            $apiName = strtolower($apilist->api->name);
            // India Mart API
            if (strpos($apiName, 'india mart') !== false) {
                // Rate limit: 5-minute cooldown to avoid 429 error
                if ($apilist->last_checked_date && Carbon::parse($apilist->last_checked_date)->addMinutes(5)->isFuture()) {
                    Log::info("ApiFetch: [India Mart] Skipping Box [{$apilist->box_id}] - Waiting for 5-minute cooldown.");
                    continue;
                }
                // India Mart strictly allows max 7 days difference between start_time and end_time
                // A safe window of 167 hours (slightly less than 168h/7d) prevents rejection
                $maxLookback = time() - (167 * 3600);
                $lastLead = IndiaMartLead::where('lead_api_box_id', $apilist->id)->orderBy('date_time_re', 'desc')->first();

                if ($lastLead && $lastLead->date_time_re) {
                    $lastLeadTime = strtotime($lastLead->date_time_re);
                    // Ensure we stay within the 7-day API limit from the current moment
                    $finalStartTime = ($lastLeadTime < $maxLookback) ? $maxLookback : $lastLeadTime;
                    $startDate = date('d-M-Y H:i:s', $finalStartTime);
                } else {
                    $startDate = date('d-M-Y H:i:s', $maxLookback);
                }

                $endDate = date('d-M-Y H:i:s');
                $imKey = $apilist->params['CRM Key'] ?? $apilist->params['key'] ?? $apilist->params['code'] ?? $key;

                // IndiaMart expects d-M-Y format (e.g. 05-Apr-2024)
                $url = "https://mapi.indiamart.com/wservce/crm/crmListing/v2/?glusr_crm_key=" . $imKey . "&start_time=" . str_replace(' ', '%20', $startDate) . "&end_time=" . str_replace(' ', '%20', $endDate);
                Log::info("ApiFetch: [India Mart] Fetching URL: " . $url);
                $mapper = [
                    "query_id" => "UNIQUE_QUERY_ID",
                    "name" => "SENDER_NAME",
                    "email" => "SENDER_EMAIL",
                    "phone" => "SENDER_MOBILE",
                    "company_name" => "SENDER_COMPANY",
                    "address" => "SENDER_ADDRESS",
                    "city" => "SENDER_CITY",
                    "state" => "SENDER_STATE",
                    "country" => "SENDER_COUNTRY_ISO",
                    "query" => "QUERY_MESSAGE",
                    "subject" => "SUBJECT",
                    "product_name" => "QUERY_PRODUCT_NAME",
                    "mcenquiry_id" => "MC_QUERY_ID",
                    "query_type" => "QUERY_TYPE"
                ];
                $this->fetchAndProcess($apilist, IndiaMartLead::class, $url, $mapper, 'processIndiaMart', 'query_id', 'QUERY_TIME', 'date_time_re');
            }
            // Pharma Khabar API
            elseif (strpos($apiName, 'pharma khabar') !== false) {
                $apiKey = $apilist->params['api_key'] ?? $key;
                if (!$apiKey) {
                    Log::warning("ApiFetch: [Pharma Khabar] No API key found for Box [{$apilist->box_id}]");
                    continue;
                }
                $lastLead = PharmaKhabarLead::where('lead_api_box_id', $apilist->id)->orderBy('submit_time', 'desc')->first();
                $now = now();
                if ($lastLead) {
                    $start = Carbon::parse($lastLead->submit_time);
                    Log::info("ApiFetch: [Pharma Khabar] Last Lead: " . $lastLead->id . ' ' . $start->format('Y-m-d H:i:s'));
                    $url = "https://www.pharmakhabar.com/api/queries/{$apiKey}?start=" . urlencode($start->format('Y-m-d H:i:s')) . "&end=" . urlencode($now->format('Y-m-d H:i:s'));
                } else {
                    $url = "https://www.pharmakhabar.com/api/queries/{$apiKey}";
                }
                Log::info("ApiFetch: [Pharma Khabar] Fetching URL: " . $url);
                $mapper = [
                    "query_id" => "query_id",
                    "name" => "person_name",
                    "email" => "email",
                    "phone" => "mobile",
                    "company_name" => "company_name",
                    "address" => "address",
                    "city" => "city",
                    "state" => "state",
                    "pincode" => "pincode",
                    "location" => "location",
                    "drugs_license" => "drugs_license",
                    "have_gst_number" => "have_gst_number",
                    "purchase_period" => "purchase_period",
                    "call_back_time" => "call_back_time",
                    "query" => "requirement",
                    "profession" => "profession",
                    "minimum_investment" => "minimum_investment",
                    "qtype" => "qtype",
                    "submit_ip" => "submit_ip",
                    "submit_time" => "submit_time"
                ];
                $this->fetchAndProcess($apilist, PharmaKhabarLead::class, $url, $mapper, 'processPharmaKhabar', 'query_id', 'submit_time');
            }
            // Trade India API
            elseif (strpos($apiName, 'trade india') !== false) {
                $userId = $apilist->params['User ID'] ?? $apilist->params['user_id'] ?? $apilist->params['userid'] ?? '';
                $profileId = $apilist->params['Profile ID'] ?? $apilist->params['profile_id'] ?? '';
                $lastLead = TradeIndiaLead::where('lead_api_box_id', $apilist->id)->orderBy('submit_time', 'desc')->first();
                $start = $lastLead ? Carbon::parse($lastLead->submit_time) : now()->subDays(30);
                $end = now();
                $url = "https://www.tradeindia.com/utils/my_inquiry.html?userid=" . $userId . "&profile_id=" . $profileId . "&key=" . $key . "&from_date=" . urlencode($start->format('Y-m-d 00:00:00')) . "&to_date=" . urlencode($end->format('Y-m-d 23:59:59'));
                $mapper = [
                    "rfi_id" => "rfi_id",
                    "sender_name" => "sender_name",
                    "sender_email" => "sender_email",
                    "sender_mobile" => "sender_mobile",
                    "sender_company" => "sender_company",
                    "sender_address" => "sender_address",
                    "sender_city" => "sender_city",
                    "sender_state" => "sender_state",
                    "sender_country" => "sender_country",
                    "message" => "message",
                    "product_name" => "product_name"
                ];
                $this->fetchAndProcess($apilist, TradeIndiaLead::class, $url, $mapper, 'processTradeIndia', 'rfi_id', 'generated');
            }
            // Trade XL API
            elseif (strpos($apiName, 'tradexl') !== false || strpos($apiName, 'trade xl') !== false) {
                $uid = $apilist->params['uid'] ?? '';
                $apiKey = $apilist->params['key'] ?? $key;
                $fetchDate = now()->format('Y-m-d');
                $jdata = json_encode(["uid" => $uid, "key" => $apiKey, "date" => $fetchDate]);
                $url = "https://www.tradexl.com/api/getenquiry.php?jdata=" . urlencode($jdata);
                $mapper = ["unique_id" => "id", "sender_name" => "name", "sender_email" => "email", "sender_mobile" => "mobile", "message" => "message"];
                $this->fetchAndProcess($apilist, TradeXlLead::class, $url, $mapper, 'processTradeXl', 'unique_id', 'created_at');
            }
            // 99Acres API
            elseif (strpos($apiName, '99acers') !== false || strpos($apiName, '99acres') !== false) {
                $this->fetchAndProcess99Acres($apilist);
            }
            // PharmaHopers
            elseif (strpos($apiName, 'pharmahopers') !== false) {
                $username = $apilist->params['username'] ?? '';
                $password = $apilist->params['password'] ?? $apilist->params['key'] ?? $key;
                $token = null;

                try {
                    $authResponse = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->withBody(json_encode([
                                    'username' => $username,
                                    'password' => $password
                                ]), 'application/json')
                        ->post('https://api.pharmahopers.com/auth/token');

                    Log::info("ApiFetch: [PharmaHopers] Auth Status: " . $authResponse->status() . " Response: " . $authResponse->body());

                    if ($authResponse->successful()) {
                        $token = $authResponse->json('access_token') ?? $authResponse->json('token');
                    } else {
                        Log::error("ApiFetch: [PharmaHopers] Auth Failed: " . $authResponse->body());
                    }
                } catch (\Exception $e) {
                    Log::error("ApiFetch: [PharmaHopers] Auth Exception: " . $e->getMessage());
                }

                if ($token) {
                    $lastLead = PharmaHoperLead::where('lead_api_box_id', $apilist->id)->orderBy('submit_time', 'desc')->first();
                    $start = $lastLead ? Carbon::parse($lastLead->submit_time) : now()->startOfDay();
                    $now = now();

                    $url = "https://api.pharmahopers.com/lead?access_token=" . $token . "&from=" . $start->format('Y-m-d') . "&to=" . $now->format('Y-m-d');
                    Log::info("ApiFetch: [PharmaHopers] Fetching URL: " . $url);

                    // Detailed mapper based on user's provided logic
                    $mapper = [
                        "query_id" => "id",
                        "message" => "message",
                        "service_type" => "service_type",
                        "medicine_type" => "medicine_type",
                        "service_for" => "service_for",
                        "call_time" => "call_time",
                        "required_when" => "required_when",
                        "selling_purpose" => "selling_purpose",
                        "investment" => "investment",
                        "quantity" => "quantity",
                        "order_value" => "order_value",
                        "address" => "address",
                        "gst_number" => "gst_number",
                        "drug_license" => "drug_license",
                        "firm_name" => "firm_name",
                        "profession" => "profession",
                        "experience" => "experience",
                        "buyer_since" => "buyer_since",
                        "type" => "type",
                        "name" => "name",
                        "email" => "email",
                        "phone" => "mobile"
                    ];
                    $this->fetchAndProcess($apilist, PharmaHoperLead::class, $url, $mapper, 'processPharmaHoper', 'query_id', 'date', 'submit_time');
                }
            }
            // PharmaVends 
            elseif (strpos($apiName, 'pharmavends') !== false) {
                $lastLead = PharmaVendLead::where('lead_api_box_id', $apilist->id)->orderBy('submit_time', 'desc')->first();
                // Set lookback: use last lead date or start of today
                $start = $lastLead ? Carbon::parse($lastLead->submit_time) : now()->startOfDay();
                
                $apiToken = $apilist->params['apitoken'] ?? $apilist->params['api_key'] ?? $apilist->params['password'] ?? $key;
                $url = "https://pharmavends.net/api/company-profile?apitoken=" . $apiToken . "&start_date=" . $start->format('Y-m-d') . "&end_date=" . now()->format('Y-m-d');
                Log::info("ApiFetch: [PharmaVends] Fetching URL: " . $url);
                
                // Map API response fields to pharma_vend_leads table columns based on user snippet
                $mapper = [
                    "query_id" => "uid",
                    "name" => "name",
                    "companyname" => "companyname",
                    "email" => "email",
                    "phone" => "ContactNo",
                    "whatsapp_number" => "WhatsApp",
                    "location" => "Location",
                    "state" => "State",
                    "pincode" => "Pincode",
                    "message" => "Description",
                    "gst_number" => "GStNo",
                    "drug_license" => "DrugLiencence",
                    "requirment_type" => "Requirmenttype",
                    "call_time" => "Timetocall",
                    "profession" => "Profession",
                    "experience" => "Experience"
                ];
                // Added "Receivedon" as source column for submit_time
                $this->fetchAndProcess($apilist, PharmaVendLead::class, $url, $mapper, 'processPharmaVend', 'query_id', 'Receivedon', 'submit_time');
            }
            // Facebook API 
            elseif (strpos($apiName, 'facebook') !== false) {
                if (isset($apilist->params['selected_form'])) {
                    $form = FacebookForm::find($apilist->params['selected_form']);
                    if ($form) {
                        try {
                            $whatsapp = new WhatsApp($apilist->box);
                            $whatsapp->downloadLeads($apilist->box, $form);
                            $leads = FacebookLead::where('form_id', $form->form_id)->where('status', 'Pending')->orderBy('created_time', 'asc')->get();
                            foreach ($leads as $lead) {
                                /** @var FacebookLead $lead */
                                $data = $lead->data;
                                $submit_time = date("Y-m-d H:i:s", strtotime($lead->created_time));
                                $field_data = $data['field_data'] ?? [];
                                $flatData = [];
                                foreach ($field_data as $fd) {
                                    $flatData[$fd['name']] = $fd['values'][0] ?? null;
                                }
                                $mapper = ['full_name' => 'name', 'email' => 'email', 'phone_number' => 'phone'];
                                if (isset($apilist->params['mapper'])) {
                                    foreach ($apilist->params['mapper'] as $sysField => $fbField) {
                                        $mapper[$fbField] = $sysField;
                                    }
                                }
                                $filterData = ['lead_source_id' => $apilist->params['source'] ?? null];
                                $LeadController->saveLead($this->apilist->box, $flatData, $filterData, $mapper, $submit_time, $this->apilist->box->superadmin_id, 'Facebook API');
                                $lead->status = 'Processed';
                                $lead->save();
                            }
                            if ($leads->count() > 0) {
                                $apilist->update(['last_lead_date' => now()]);
                            }
                        } catch (\Exception $e) {
                        }
                    }
                }
            }

            // WebHook Processing (Universal)
            $webhooks = WebHook::where('lead_api_box_id', $apilist->id)->where('status', 'Pending')->get();
            foreach ($webhooks as $webhook) {
                $data = $webhook->toArray();
                if (!empty($webhook->params)) {
                    $data = array_merge($data, $webhook->params);
                }
                $mapper = ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'query' => 'query', 'company_name' => 'company_name'];
                $filterData = $this->getFilterData($apilist);
                $leadData = $LeadController->saveLead($apilist->box, $data, $filterData, $mapper, $webhook->created_at, $webhook->added_by ?? $apilist->box->superadmin_id, 'WebHook');
                $webhook->update(['status' => 'Processed', 'lead_id' => $leadData->id]);
            }
            if ($webhooks->count() > 0) {
                $apilist->update(['last_lead_date' => now()]);
            }
        }
    }

    public function callAPI($url, $headers = [])
    {
        $ch = curl_init();
        if (strpos($url, 'mapi.indiamart.com') !== false) {
            curl_setopt($ch, CURLOPT_RESOLVE, ["mapi.indiamart.com:443:34.36.183.234"]);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_WHATEVER);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $curlHeaders = array_merge(['Accept: application/json'], $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            Log::error("ApiFetch: cURL Total Failure for {$url}: " . $err);
            $this->error("cURL Total Failure: " . $err);
            return null;
        }

        if ($httpCode >= 400 && $httpCode != 429) {
            Log::error("ApiFetch: HTTP Error {$httpCode} for {$url}. Response: |{$response}|");
            return null;
        }
        if ($response === "" || $response === null) {
            Log::info("ApiFetch: Received empty body with HTTP code {$httpCode} for {$url}");
        }
        return $response;
    }

    protected function fetchAndProcess99Acres($apilist)
    {
        $logPrefix = "ApiFetch [Box: {$apilist->box_id} | {$apilist->api->name}]:";
        $params = $apilist->params;
        $username = $params['username'] ?? $params['user_name'] ?? '';
        $password = $params['password'] ?? $params['pswd'] ?? '';
        if (!$username || !$password)
            return;
        $start = now();
        $xmlRequest = "<?xml version='1.0'?><query><user_name>{$username}</user_name><pswd>{$password}</pswd><start_date>" . $start->format('Y-m-d') . " 00:00:00</start_date><end_date>" . $start->format('Y-m-d') . " 23:59:59</end_date></query>";
        try {
            $endpoint = 'http://www.99acres.com/99api/v1/getmy99Response/OeAuXClO43hwseaXEQ/uid/';
            $response = Http::asForm()->post($endpoint, ['xml' => $xmlRequest]);
            if ($response->successful()) {
                $xmlBody = $response->body();
                $xml = new \SimpleXMLElement($xmlBody, LIBXML_NOCDATA);
                $dataItems = json_decode(json_encode($xml), true)['Resp']['QryDtl'] ?? [];
                if (isset($dataItems['QryId']))
                    $dataItems = [$dataItems];
                foreach ($dataItems as $item) {
                    $uniqueId = $item['QryId'] ?? null;
                    if (!$uniqueId)
                        continue;
                    $lead = NintyNineAcersLead::firstOrNew(['lead_api_box_id' => $apilist->id, 'lead_id' => $uniqueId]);
                    $lead->fill(['name' => $item['Name'] ?? '', 'email' => $item['Email'] ?? '', 'phone' => $item['Mobile'] ?? '', 'query' => $item['Query'] ?? '', 'date' => $item['Date'] ?? now(), 'status' => 'Fetched'])->save();
                    $this->processNintyNineAcers(new LeadsController, $lead, $item);
                }
                if (!empty($dataItems)) {
                    $apilist->update(['last_lead_date' => now()]);
                }
            }
        } catch (\Exception $e) {
            Log::error("{$logPrefix} Exception: " . $e->getMessage());
        }
    }

    protected function fetchAndProcess($apilist, $modelClass, $url, $mapper, $processMethod, $uniqueColumn = 'query_id', $dateSourceColumn = 'created_at', $dateTargetColumn = 'submit_time', $headers = [])
    {
        $logPrefix = "ApiFetch [Box: {$apilist->box_id} | {$apilist->api->name}]:";
        $this->info("{$logPrefix} Starting fetch...");
        try {
            $this->info("{$logPrefix} Fetching URL: {$url}");
            $response = $this->callAPI($url, $headers);
            Log::info("{$logPrefix} Raw Response from {$url}: |" . ($response ?: 'Empty Response') . "|");
            // Once we get a response, it is definitely 'Connected'
            $apilist->update(['connection_status' => 'Connected', 'error_log' => null, 'last_checked_date' => now()]);
            $json = json_decode($response, true);

            // Handle Rate Limit (429) or Failure Status from API (Common format or PharmaVends format)
            if (isset($json['CODE']) && ($json['CODE'] == 429 || ($json['STATUS'] ?? '') == 'FAILURE' || ($json['status'] ?? '') == 'false')) {
                $errorMessage = $json['MESSAGE'] ?? $json['message'] ?? 'API Rate limit exceeded or failure';
                $this->error("{$logPrefix} {$errorMessage}");
                Log::info("{$logPrefix} API Failure Response: " . json_encode($json, JSON_PRETTY_PRINT));

                // If we got this JSON, the connection is technically working, so we keep it as 'Connected'
                $apilist->update([
                    'connection_status' => 'Connected',
                    'error_log' => $errorMessage,
                    'last_checked_date' => now()
                ]);
                return;
            }

            $dataItems = $json['RESPONSE'] ?? $json['data'] ?? $json['purchased_leads'] ?? (is_array($json) && isset($json[0]) ? $json : []);
            if (!empty($dataItems)) {
                $LeadController = new LeadsController;
                $this->info("{$logPrefix} Found " . count($dataItems) . " items to process.");
                Log::info("{$logPrefix} Found " . count($dataItems) . " items to process.");
                $importedCount = 0;
                foreach ($dataItems as $value) {
                    Log::info("{$logPrefix} Processing Lead Data: " . json_encode($value));
                    $uniqueVal = $value[$mapper[$uniqueColumn] ?? $uniqueColumn] ?? null;
                    if (!$uniqueVal) {
                        Log::warning("{$logPrefix} Unique ID not found in lead data: " . json_encode($value));
                        continue;
                    }
                    $lead = $modelClass::where('lead_api_box_id', $apilist->id)->where($uniqueColumn, $uniqueVal)->first();
                    if (!$lead || $lead->lead_id === null) {
                        if (!$lead)
                            $lead = new $modelClass;
                        $lead->lead_api_box_id = $apilist->id;
                        $lead->box_id = $apilist->box_id;
                        foreach ($mapper as $dbKey => $apiKey) {
                            $lead->$dbKey = $value[$apiKey] ?? null;
                        }
                        $lead->$dateTargetColumn = date("Y-m-d H:i:s", strtotime($value[$dateSourceColumn] ?? now()));
                        $this->{$processMethod}($LeadController, $lead, $value);
                        $importedCount++;
                    }
                }
                if ($importedCount > 0) {
                    $apilist->update(['last_lead_date' => now()]);
                }
            }
        } catch (\Exception $e) {
            Log::error("{$logPrefix} Exception in fetchAndProcess: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $apilist->update(['connection_status' => 'Error', 'error_log' => $e->getMessage(), 'last_checked_date' => now()]);
        }
    }

    protected function processIndiaMart($LeadController, $lead, $rawData)
    {
        $data = $lead->toArray();
        unset($data['id'], $data['lead_id'], $data['created_at'], $data['updated_at']);

        $queryType = $rawData['QUERY_TYPE'] ?? '';
        if ($queryType == 'B') {
            $data['lead_type'] = 'Buy Lead';
        } elseif ($queryType == 'P') {
            $data['lead_type'] = 'Call Lead';
        } elseif ($queryType == 'W') {
            $data['lead_type'] = 'Direct Lead';
        } elseif ($queryType == 'BIZ') {
            $data['lead_type'] = 'Catalog-views';
        } elseif ($queryType == 'WA') {
            $data['lead_type'] = 'WhatsApp Enquiries';
        } else {
            // Fallback to the code itself if it's unknown, or Direct Lead
            $data['lead_type'] = $queryType ?: 'Direct Lead';
        }

        $location = $this->resolveLocation($rawData['SENDER_COUNTRY_ISO'] ?? null, $rawData['SENDER_STATE'] ?? null, $rawData['SENDER_CITY'] ?? null);
        $data = array_merge($data, $location);

        $filterData = $this->getFilterData($this->apilist);
        if (!$filterData['lead_source_id']) {
            $filterData['lead_source_id'] = \DB::table('crm_sources')->where('box_id', $this->apilist->box_id)->where('name', 'like', '%India Mart%')->value('id');
        }
        $mapper = ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'query' => 'query', 'company_name' => 'company_name', 'address' => 'address', 'lead_type' => 'lead_type', 'country_id' => 'country_id', 'state_id' => 'state_id', 'city_id' => 'city_id'];
        $mainLead = $LeadController->saveLead($this->apilist->box, $data, $filterData, $mapper, $lead->date_time_re ?? now(), $this->apilist->box->superadmin_id, 'India Mart API');
        if ($mainLead && $mainLead->id) {
            $lead->lead_id = $mainLead->id;
            $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
            $lead->status = 'Imported';
        }
        $lead->save();
    }

    protected function processPharmaKhabar($LeadController, $lead, $rawData)
    {
        $data = $lead->toArray();
        unset($data['id'], $data['lead_id'], $data['created_at'], $data['updated_at']);
        $location = $this->resolveLocation(null, $rawData['state'] ?? null, $rawData['city'] ?? null);
        $data = array_merge($data, $location);

        // Standardize lead type for Pharma Khabar
        if (($data['qtype'] ?? '') == "Direct") {
            $data['lead_type'] = "Direct Lead";
        } elseif (($data['qtype'] ?? '') == "Buy") {
            $data['lead_type'] = "Buy Lead";
        } else {
            $data['lead_type'] = $data['qtype'] ?? 'Direct Lead';
        }

        $filterData = $this->getFilterData($this->apilist);
        if (!$filterData['lead_source_id']) {
            $filterData['lead_source_id'] = \DB::table('crm_sources')
                ->where('box_id', $this->apilist->box_id)
                ->where('name', 'like', '%Pharma%Khabar%')
                ->value('id');
        }

        $mapper = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'query' => 'query',
            'company_name' => 'company_name',
            'address' => 'address',
            'lead_type' => 'lead_type',
            'country_id' => 'country_id',
            'state_id' => 'state_id',
            'city_id' => 'city_id'
        ];
        $mainLead = $LeadController->saveLead($this->apilist->box, $data, $filterData, $mapper, $lead->submit_time, $this->apilist->box->superadmin_id, 'Pharma Khabar API');
        if ($mainLead && $mainLead->id) {
            $lead->lead_id = $mainLead->id;
            $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
            $lead->status = 'Imported';
        }
        $lead->save();
    }

    protected function processTradeIndia($LeadController, $lead, $rawData)
    {
        $data = $lead->toArray();
        unset($data['id'], $data['lead_id'], $data['created_at'], $data['updated_at']);
        $location = $this->resolveLocation($rawData['sender_country'] ?? null, $rawData['sender_state'] ?? null, $rawData['sender_city'] ?? null);
        $data = array_merge($data, $location);
        $filterData = $this->getFilterData($this->apilist);
        $mapper = ['sender_name' => 'name', 'sender_email' => 'email', 'sender_mobile' => 'phone', 'sender_company' => 'company_name', 'sender_address' => 'address', 'message' => 'query', 'country_id' => 'country_id', 'state_id' => 'state_id', 'city_id' => 'city_id'];
        $mainLead = $LeadController->saveLead($this->apilist->box, $data, $filterData, $mapper, $lead->submit_time, $this->apilist->box->superadmin_id, 'Trade India API');
        if ($mainLead && $mainLead->id) {
            $lead->lead_id = $mainLead->id;
            $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
            $lead->status = 'Imported';
        }
        $lead->save();
    }

    protected function processTradeXl($LeadController, $lead, $rawData)
    {
        $data = $lead->toArray();
        unset($data['id'], $data['lead_id'], $data['created_at'], $data['updated_at']);
        $filterData = $this->getFilterData($this->apilist);
        $mapper = ['sender_name' => 'name', 'sender_email' => 'email', 'sender_mobile' => 'phone', 'message' => 'query'];
        $mainLead = $LeadController->saveLead($this->apilist->box, $data, $filterData, $mapper, $lead->submit_time ?? now(), $this->apilist->box->superadmin_id, 'TradeXL API');
        if ($mainLead && $mainLead->id) {
            $lead->lead_id = $mainLead->id;
            $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
            $lead->status = 'Imported';
        }
        $lead->save();
    }

    protected function processNintyNineAcers($LeadController, $lead, $rawData)
    {
        $data = $lead->toArray();
        unset($data['id'], $data['lead_id'], $data['created_at'], $data['updated_at']);
        $filterData = $this->getFilterData($this->apilist);
        $mapper = ['name' => 'name', 'email' => 'email', 'phone' => 'phone', 'query' => 'query'];
        $mainLead = $LeadController->saveLead($this->apilist->box, $data, $filterData, $mapper, $lead->submit_time ?? now(), $this->apilist->box->superadmin_id, '99Acers API');
        if ($mainLead && $mainLead->id) {
            $lead->lead_id = $mainLead->id;
            $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
            $lead->status = 'Imported';
        }
        $lead->save();
    }

    protected function processPharmaHoper($LeadController, $lead, $rawData)
    {
        $data = $lead->toArray();
        unset($data['id'], $data['lead_id'], $data['created_at'], $data['updated_at']);

        // Handle lead type mapping
        if (($data['type'] ?? '') == "1") {
            $data['lead_type'] = "Direct Lead";
        } elseif (($data['type'] ?? '') == "2") {
            $data['lead_type'] = "Buy Lead";
        } else {
            $data['lead_type'] = $data['type'] ?? 'Direct Lead';
        }

        $filterData = $this->getFilterData($this->apilist);

        // Detailed mapper for Leads table
        $mapper = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'message' => 'query',
            'firm_name' => 'company_name',
            'address' => 'address',
            'lead_type' => 'lead_type'
        ];

        $mainLead = $LeadController->saveLead($this->apilist->box, $data, $filterData, $mapper, $lead->submit_time ?? now(), $this->apilist->box->superadmin_id, 'PharmaHopers API');
        if ($mainLead && $mainLead->id) {
            $lead->lead_id = $mainLead->id;
            $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
            $lead->status = 'Imported';
        }
        $lead->save();
    }

    protected function processPharmaVend($LeadController, $lead, $rawData)
    {
        $data = $lead->toArray();
        unset($data['id'], $data['lead_id'], $data['created_at'], $data['updated_at']);
        
        // Resolve location names to IDs to avoid SQL integer errors
        $locationResult = $this->resolveLocation(null, $lead->state, $lead->location);
        
        $data['name'] = $lead->name ?: 'Unknown';
        $data['company_name'] = $lead->companyname;
        $data['email'] = $lead->email;
        $data['phone'] = $lead->phone;
        $data['WhatsApp Number'] = $lead->whatsapp_number;
        $data['location'] = $lead->location; // Keep the string for the address/location field
        $data['state_id'] = $locationResult['state_id'];
        $data['city_id'] = $locationResult['city_id'];
        $data['country_id'] = $locationResult['country_id'];
        $data['postal_code'] = $lead->pincode;
        $data['query'] = $lead->message;
        
        // Refined Yes/No logic to handle both "1"/"0" and "Yes"/"No" from API
        $data['gstin_number'] = (in_array(strtolower($lead->gst_number), ['1', 'yes', 'y']) ? "Yes" : "No");
        $data['drug_license'] = (in_array(strtolower($lead->drug_license), ['1', 'yes', 'y']) ? "Yes" : "No");

        $data['Profession'] = $lead->profession;
        $data['Experience'] = $lead->experience;
        $data['lead_type'] = $lead->type ?: 'Direct Lead';
        $data['Requirment Type'] = $lead->requirment_type;
        $data['Call Time'] = $lead->call_time;

        $filterData = $this->getFilterData($this->apilist);
        
        // Corrected mapper to use resolved IDs
        $mapper = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'lead_type' => 'lead_type',
            'company_name' => 'company_name',
            'state_id' => 'state_id',
            'city_id' => 'city_id',
            'country_id' => 'country_id',
            'query' => 'query',
            'location' => 'location',
            'gstin_number' => 'gstin_number',
            'drug_license' => 'drug_license',
            'postal_code' => 'postal_code'
        ];
        
        $mainLead = $LeadController->saveLead($this->apilist->box, $data, $filterData, $mapper, $lead->submit_time ?? now(), $this->apilist->box->superadmin_id, 'PharmaVends API');
        
        if ($mainLead && $mainLead->id) {
            $lead->lead_id = $mainLead->id;
            $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
            
            if ($mainLead->wasRecentlyCreated) {
                $lead->status = 'Imported';
            } else {
                $lead->status = 'Updated';
            }
        } else {
            $lead->status = 'Skipped';
        }
        
        if($lead->isDirty()) {
            $lead->save();
        }
    }

    protected function getFilterData($apilist)
    {
        $params = $apilist->params ?? [];
        $roundRobinUsers = ($apilist->users && $apilist->users->count() > 0)
            ? $apilist->users->pluck('id')->toArray()
            : ($params['round_robin_users'] ?? []);

        if (is_string($roundRobinUsers)) {
            $roundRobinUsers = array_filter(explode(',', $roundRobinUsers));
        }

        $autoTrash = $params['auto_trash'] ?? [];
        if (is_string($autoTrash)) {
            $autoTrash = array_filter(array_map('trim', explode(',', $autoTrash)));
        }

        $hasWords = $params['has_words'] ?? [];
        if (is_string($hasWords)) {
            $hasWords = array_filter(array_map('trim', explode(',', $hasWords)));
        }

        $autoUnassigned = $params['auto_unassigned'] ?? [];
        if (is_string($autoUnassigned)) {
            $autoUnassigned = array_filter(array_map('trim', explode(',', $autoUnassigned)));
        }

        return [
            'lead_source_id' => $params['lead_source_id'] ?? $params['source'] ?? null,
            'lead_status_id' => $params['lead_status_id'] ?? null,
            'assigned_to' => $params['assigned_to'] ?? null,
            'round_robin' => ($apilist->users && $apilist->users->count() > 0) ? 1 : ($params['round_robin'] ?? 0),
            'round_robin_users' => $roundRobinUsers,
            'auto_trash' => $autoTrash,
            'has_words' => $hasWords,
            'auto_unassigned' => $autoUnassigned,
            'enable_shifts' => $params['enable_shifts'] ?? 'No',
            'shifts' => $params['shifts'] ?? [],
            
            // User snippet specific nomenclature
            'round_robin_shits' => $params['round_robin_shits'] ?? '0',
            'round_robin_shits_timing' => $params['round_robin_shits_timing'] ?? null,
        ];
    }

    private function resolveLocation($countryNameOrCode = null, $stateName = null, $cityName = null)
    {
        $countryId = null;
        $stateId = null;
        $cityId = null;

        // RESOLVE COUNTRY
        if ($countryNameOrCode) {
            $countryNameOrCode = trim($countryNameOrCode);
            $countryId = \DB::table('countries')
                ->where('code', $countryNameOrCode)
                ->orWhereRaw('LOWER(name) = ?', [strtolower($countryNameOrCode)])
                ->value('id');
        }

        // RESOLVE STATE
        if ($stateName) {
            $stateName = trim($stateName);
            $stateId = \DB::table('states')
                ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($stateName) . '%'])
                ->when($countryId, function ($q) use ($countryId) {
                    return $q->where('country_id', $countryId);
                })
                ->value('id');
        }

        // RESOLVE CITY
        if ($cityName) {
            $cityName = trim($cityName);
            $city = \DB::table('cities')
                ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($cityName) . '%'])
                ->when($stateId, function ($q) use ($stateId) {
                    return $q->where('state_id', $stateId);
                })
                ->first();

            if ($city) {
                $cityId = $city->id;
                // Reverse resolution: if state_id is null, use the one from city
                if (!$stateId) {
                    $stateId = $city->state_id;
                }
            }
        }

        // FINAL REVERSE RESOLUTION: STATE TO COUNTRY
        if ($stateId && !$countryId) {
            $state = \DB::table('states')->where('id', $stateId)->first();
            if ($state) {
                $countryId = $state->country_id;
            }
        }

        return [
            'country_id' => $countryId,
            'state_id' => $stateId,
            'city_id' => $cityId
        ];
    }
}
