<?php
namespace App\Http\Controllers\Setting;
use App\Http\Controllers\Controller;
use App\Models\{Box, BoxCustomField, BoxUser, City, Country, GmailInbox, State, SystemField, UserColumnPreference, Company, EmployeeType, Department, Team, GmailMessage, Activity, Lead, ContactStage, CrmSource, LeadSubStatus};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Schema, Storage, Validator, Log, Http};
use App\Exports\CompanyExport;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class SettingsController extends Controller
{
    protected $box;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->box = $request->attributes->get('box');
            return $next($request);
        });
    }

    public function compaign_settings()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'campaign_settings'))) {
            abort(403, 'You do not have permission to access campaign settings.');
        }

        return view('settings.compaign');
    }

    public function compaing_details()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'campaign_details'))) {
            abort(403, 'You do not have permission to access campaign details.');
        }

        return view('settings.campaign-details');
    }

    public function create_project()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'project_create'))) {
            abort(403, 'You do not have permission to access project create settings.');
        }

        return view('settings.create_project');
    }

    public function companies()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        $canRead = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'companies');
        if (!$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view companies.'
            ], 403);
        }

        $boxId = $boxUser->box_id;
        $totalCompanies = Company::where('box_id', $boxId)->count();

        return view('settings.companies', compact('totalCompanies'));
    }

    public function getCompanies(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        $canRead = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'companies');
        if (!$canRead) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to view companies.'], 403);
        }
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();

        if (!$boxUser) {
            return response()->json(['data' => []]);
        }

        $companies = Company::where('box_id', $boxUser->box_id)
            ->with(['state', 'city', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $companies
        ]);
    }

    public function storeCompany(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        $canCreate = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'create', 'companies');
        if (!$canCreate) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to create companies.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'bill_name' => 'required|string|max:255',
            'bill_phone' => 'nullable|string|max:50',
            'bill_address' => 'nullable|string',
            'pan' => 'nullable|string|max:50',
            'gstin' => 'nullable|string|max:50',
            'bill_state_id' => 'nullable|exists:states,id',
            'bill_city_id' => 'nullable|exists:cities,id',
            'bill_pin_code' => 'nullable|string|max:20',
            'status' => 'nullable|in:Enable,Disabled',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();

        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'User not associated with a box.'], 403);
        }

        $company = new Company();
        $company->box_id = $boxUser->box_id;
        $company->fill($request->only([
            'bill_name',
            'bill_phone',
            'bill_address',
            'pan',
            'gstin',
            'bill_state_id',
            'bill_city_id',
            'bill_pin_code',
            'status'
        ]));

        $params = [
            'orientation' => $request->input('orientation', 'P'), // Default Portrait
            'packing' => $request->input('packing', 'No'),
            'hsn' => $request->input('hsn', 'No'),
            'mrp' => $request->input('mrp', 'No'),
            'jurisdiction' => $request->input('jurisdiction', null),
            'pdf_logo' => null
        ];

        if ($request->hasFile('company_logo')) {
            $path = Storage::disk('public')->putFile('uploads/company_logos', $request->file('company_logo'));
            $params['pdf_logo'] = $path;
        }
        $company->params = $params;

        $company->added_by = $userId;
        $company->last_updated_by = $userId;
        $company->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Company created successfully.',
            'company' => $company
        ]);
    }

    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        $canUpdate = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'companies');
        if (!$canUpdate) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to update companies.'], 403);
        }
        // Fallback: If ID is passed as 0 or not found in route but exists in body


        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:companies,id',
            'bill_name' => 'required|string|max:255',
            'bill_phone' => 'nullable|string|max:50',
            'bill_address' => 'nullable|string',
            'pan' => 'nullable|string|max:50',
            'gstin' => 'nullable|string|max:50',
            'bill_state_id' => 'nullable|exists:states,id',
            'bill_city_id' => 'nullable|exists:cities,id',
            'bill_pin_code' => 'nullable|string|max:20',
            'status' => 'nullable|in:Enable,Disabled',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $id = $request->input('id');
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['status' => 'error', 'message' => 'Company not found.'], 404);
        }

        $company->fill($request->only([
            'bill_name',
            'bill_phone',
            'bill_address',
            'pan',
            'gstin',
            'bill_state_id',
            'bill_city_id',
            'bill_pin_code',
            'status'
        ]));

        $params = $company->params ?? [];
        $params['orientation'] = $request->input('orientation', $params['orientation'] ?? 'P');
        $params['packing'] = $request->input('packing', $params['packing'] ?? 'No');
        $params['hsn'] = $request->input('hsn', $params['hsn'] ?? 'No');
        $params['mrp'] = $request->input('mrp', $params['mrp'] ?? 'No');
        $params['jurisdiction'] = $request->input('jurisdiction', $params['jurisdiction'] ?? null);

        // Handle File Upload for Company Logo if present
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if (!empty($params['pdf_logo']) && Storage::disk('public')->exists($params['pdf_logo'])) {
                Storage::disk('public')->delete($params['pdf_logo']);
            }

            $path = Storage::disk('public')->putFile('uploads/company_logos', $request->file('company_logo'));
            $params['pdf_logo'] = $path;
        }

        $company->params = $params;

        $company->last_updated_by = $userId;
        $company->save();

        return response()->json(['status' => 'success', 'message' => 'Company updated successfully.']);
    }

    public function destroyCompany(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        $canDelete = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'delete', 'companies');
        if (!$canDelete) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete companies.'], 403);
        }
        $company = Company::find($request->input('id'));
        if ($company) {
            // Delete logo if exists
            if (!empty($company->params['pdf_logo']) && Storage::disk('public')->exists($company->params['pdf_logo'])) {
                Storage::disk('public')->delete($company->params['pdf_logo']);
            }
            $company->delete();
            return response()->json(['status' => 'success', 'message' => 'Company deleted successfully.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Company not found.'], 404);
    }

    public function exportCompanies(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!$boxUser) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Box not found.'], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view companies.'
            ], 403);
        }

        $canExport = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'export', 'companies');

        if (!$canExport) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to export companies.'], 403);
            }
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to export companies.'
            ], 403);
        }

        $type = $request->query('type', 'excel');
        $fileName = 'companies_' . date('Y-m-d_H-i-s');

        if ($type === 'pdf') {
            return Excel::download(new CompanyExport($boxUser->box_id), $fileName . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
        else {
            return Excel::download(new CompanyExport($boxUser->box_id), $fileName . '.xlsx');
        }
    }

    public function companiesGrid()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        $canRead = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'companies_grid');
        if (!$canRead) {
            abort(403, 'You do not have permission to view companies grid.');
        }
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view companies grid.'
            ], 403);
        }
        $boxId = $boxUser->box_id;

        $companies = Company::where('box_id', $boxId)
            ->with(['state', 'city', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
        $totalCompanies = $companies->count();

        return view('settings.companies-grid', compact('companies', 'totalCompanies'));
    }

    public function companyDetails()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        $canRead = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'companies_details');
        if (!$canRead) {
            abort(403, 'You do not have permission to view company details.');
        }
        $userId = Auth::id();
        $id = request()->query('id');
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view company details.'
            ], 403);
        }

        $company = Company::where('id', $id)
            ->where('box_id', $boxUser->box_id)
            ->with(['state', 'city', 'creator'])
            ->first();

        if (!$company && $id) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found or you do not have permission to view it.'
            ], 403);
        }

        return view('settings.company-details', compact('company'));
    }
    public function companycurrency(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            abort(404, 'Box User not found');
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'company_currency'))) {
            abort(403, 'You do not have permission to access company currency settings.');
        }

        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();

        if (!$box) {
            $box = Box::find($boxUser->box_id);
        }

        $currencies = [
            ['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar'],
            ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro'],
            ['code' => 'GBP', 'symbol' => '£', 'name' => 'British Pound'],
            ['code' => 'INR', 'symbol' => '₹', 'name' => 'Indian Rupee'],
            ['code' => 'AUD', 'symbol' => 'A$', 'name' => 'Australian Dollar'],
            ['code' => 'CAD', 'symbol' => 'C$', 'name' => 'Canadian Dollar'],
            ['code' => 'SGD', 'symbol' => 'S$', 'name' => 'Singapore Dollar'],
            ['code' => 'JPY', 'symbol' => '¥', 'name' => 'Japanese Yen'],
            ['code' => 'CNY', 'symbol' => '¥', 'name' => 'Chinese Yuan'],
            ['code' => 'AED', 'symbol' => 'AED', 'name' => 'UAE Dirham'],
            ['code' => 'SAR', 'symbol' => 'SAR', 'name' => 'Saudi Riyal'],
            ['code' => 'KWD', 'symbol' => 'KWD', 'name' => 'Kuwaiti Dinar'],
            ['code' => 'QAR', 'symbol' => 'KWD', 'name' => 'Qatari Riyal'],
            ['code' => 'OMR', 'symbol' => 'QAR', 'name' => 'Omani Rial'],
            ['code' => 'BHD', 'symbol' => 'BD', 'name' => 'Bahraini Dinar'],
            ['code' => 'PKR', 'symbol' => 'PKR', 'name' => 'Pakistani Rupee'],
            ['code' => 'LKR', 'symbol' => 'LKR', 'name' => 'Sri Lankan Rupee'],
            ['code' => 'BDT', 'symbol' => 'BDT', 'name' => 'Bangladeshi Taka'],
            ['code' => 'NPR', 'symbol' => 'NPR', 'name' => 'Nepalese Rupee'],
            ['code' => 'IDR', 'symbol' => 'Rp', 'name' => 'Indonesian Rupiah'],
            ['code' => 'MYR', 'symbol' => 'Rp', 'name' => 'Malaysian Ringgit'],
            ['code' => 'PHP', 'symbol' => '₱', 'name' => 'Philippine Peso'],
            ['code' => 'THB', 'symbol' => '฿', 'name' => 'Thai Baht'],
            ['code' => 'VND', 'symbol' => '₫', 'name' => 'Vietnamese Dong'],
            ['code' => 'KRW', 'symbol' => '₩', 'name' => 'South Korean Won'],
            ['code' => 'TRY', 'symbol' => '₺', 'name' => 'Turkish Lira'],
            ['code' => 'RUB', 'symbol' => '₽', 'name' => 'Russian Ruble'],
            ['code' => 'ZAR', 'symbol' => 'R', 'name' => 'South African Rand'],
            ['code' => 'NGN', 'symbol' => '₦', 'name' => 'Nigerian Naira'],
            ['code' => 'KES', 'symbol' => '₦', 'name' => 'Kenyan Shilling'],
            ['code' => 'EGP', 'symbol' => 'E£', 'name' => 'Egyptian Pound'],
            ['code' => 'BRL', 'symbol' => 'R$', 'name' => 'Brazilian Real'],
            ['code' => 'MXN', 'symbol' => 'Mex$', 'name' => 'Mexican Peso'],
            ['code' => 'ARS', 'symbol' => 'Mex$', 'name' => 'Argentine Peso'],
            ['code' => 'COP', 'symbol' => 'Mex$', 'name' => 'Colombian Peso'],
            ['code' => 'CLP', 'symbol' => 'Mex$', 'name' => 'Chilean Peso'],
            ['code' => 'PEN', 'symbol' => 'Mex$', 'name' => 'Peruvian Sol'],
        ];

        return view('settings.company-currency', compact('currencies', 'box'));
    }

    public function updateCompanyCurrency(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['success' => false, 'message' => 'Box User not found'], 404);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'company_currency'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to update company currency.'], 403);
        }

        $request->validate([
            'currency_code' => 'required|string',
            'currency_symbol' => 'required|string',
            'currency_name' => 'nullable|string',
        ]);

        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();

        if (!$box) {
            $box = Box::find($boxUser->box_id);
        }

        $box->update([
            'currency_code' => $request->currency_code,
            'currency_symbol' => $request->currency_symbol,
        ]);

        $currencyName = $request->currency_name ?? $request->currency_code;

        return response()->json([
            'success' => true,
            'message' => "Company currency has been successfully changed to {$currencyName}.",
            'data' => [
                'currency_code' => $request->currency_code,
                'currency_symbol' => $request->currency_symbol,
                'currency_name' => $currencyName,
            ]
        ]);
    }
    public function hsns()
    {
        return view('settings.hsns-list');
    }
    public function packages()
    {
        return view('settings.packages');
    }
    public function company()
    {
        return view('settings.company-settings');
    }
    public function activityType()
    {
        return view('settings.activities');
    }
    public function leadSources()
    {
        return view('settings.sources');
    }
    public function leadStatus()
    {
        return view('settings.lead-status');
    }
    public function integrations(Request $request)
    {
        // Get box from request attributes (set by middleware) or from route
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = \App\Models\Box::where('slug', $boxSlug)->first();
            }
        }

        // Check Facebook connection status
        $facebookConnected = false;
        $facebookConnectionStatus = 'Not Connected';
        if ($box) {
            $box->refresh();
            $facebookConnected = !empty($box->facebook_api_params)
                && isset($box->facebook_api_params['access_token'])
                && !empty($box->facebook_api_params['access_token']);
            $facebookConnectionStatus = $facebookConnected ? 'Connected' : 'Not Connected';
        }

        // Check WhatsApp connection status
        $whatsappConnected = false;
        $whatsappConnectionStatus = 'Not Connected';
        $whatsappAccounts = [];
        if ($box) {
            $box->refresh();
            $whatsappConnected = !empty($box->whatsapp_token)
                && isset($box->whatsapp_token['access_token'])
                && !empty($box->whatsapp_token['access_token']);
            $whatsappConnectionStatus = $whatsappConnected ? 'Connected' : 'Not Connected';

            // Get WhatsApp Business accounts if connected
            if ($whatsappConnected) {
                $whatsappAccounts = \App\Models\WhatsAppBusinessAccount::where('box_id', $box->id)
                    ->where('account_status', 'ACTIVE')
                    ->get()
                    ->map(function ($account) {
                    return [
                    'id' => $account->id,
                    'waba_id' => $account->waba_id,
                    'name' => $account->name,
                    ];
                })
                    ->toArray();
            }
        }

        // Fetch last lead timestamps for all filters
        $lastLeadsRaw = \App\Models\MailFilter::where('box_id', $box->id)
            ->whereNotNull('last_lead_at')
            ->selectRaw('template_id, MAX(last_lead_at) as last_lead')
            ->groupBy('template_id')
            ->get();

        $lastLeads = [];
        foreach ($lastLeadsRaw as $row) {
            $lastLeads[$row->template_id] = \Carbon\Carbon::parse($row->last_lead);
        }

        // Fetch last lead timestamps for CRM Integrations
        $apiIntegrations = \App\Models\BoxCrmIntegration::with('api')->where('box_id', $box->id)->get();
        
        // Mapping for specific templates to show on cards
        $templates = \DB::table('lead_source_templates')->pluck('id', 'name')->toArray();

        foreach ($apiIntegrations as $integration) {
            if ($integration->last_lead_date && $integration->api) {
                $name = $integration->api->name;
                $date = \Carbon\Carbon::parse($integration->last_lead_date);
                
                // Add to lastLeads by template ID if mapped
                if (isset($templates[$name])) {
                    $lastLeads[$templates[$name]] = $date;
                }
                
                // Add to lastLeads by name as a fallback
                $lastLeads[$name] = $date;
            }
        }

        // IndiaMart API specific tracking (Fallback if integration record is old)
        $indiaMartApiLastLead = \DB::table('india_mart_leads')
            ->where('box_id', $box->id)
            ->max('created_at');
        
        if ($indiaMartApiLastLead) {
            $indiaMartApiLastLead = \Carbon\Carbon::parse($indiaMartApiLastLead);
            if (!isset($lastLeads['India Mart']) || $indiaMartApiLastLead->gt($lastLeads['India Mart'])) {
                $lastLeads['India Mart'] = $indiaMartApiLastLead;
            }
        }

        return view('settings.integrations', compact(
            'box', 
            'facebookConnected', 
            'facebookConnectionStatus', 
            'whatsappConnected', 
            'whatsappConnectionStatus', 
            'whatsappAccounts', 
            'lastLeads',
            'templates',
            'indiaMartApiLastLead'
        ));
    }

    public function facebook_form(Request $request)
    {
        // Get box from request attributes (set by middleware) or from route
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = \App\Models\Box::where('slug', $boxSlug)->first();
            }
        }

        // Check if Facebook integration is enabled for this box
        $facebookIntegration = \App\Models\BoxCrmIntegration::where('box_id', $box->id)
            ->whereHas('api', function($q) {
                $q->where('system_name', 'facebook');
            })
            ->first();
            
        $isIntegrationEnabled = ($facebookIntegration && $facebookIntegration->status === 'Enabled');
        $facebookConnected = !empty($box->facebook_api_params) && !empty($box->facebook_api_params['access_token']);

        return view('settings.facebook-forms', compact('box', 'isIntegrationEnabled', 'facebookConnected', 'facebookIntegration'));
    }

    /**
     * Save Facebook integration - receives authorization code and exchanges for token
     * Similar to WhatsApp integration flow
     */
    public function saveFacebookIntegration(Request $request)
    {
        Log::info('saveFacebookIntegration called', [
            'all' => $request->all(),
            'manual_entry' => $request->input('manual_entry'),
            'type' => $request->input('type'),
        ]);

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'api_keys'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to update API integrations.'], 403);
        }

        try {
            // Support both OAuth code flow and manual token entry
            $manualEntry = $request->input('manual_entry', false);
            $accessToken = $request->input('access_token');

            if ($manualEntry && $accessToken) {
                // Manual token entry - directly save the token
                $userId = Auth::id();
                $boxUser = BoxUser::where('user_id', $userId)->first();
                if (!$boxUser) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Box user not found',
                    ], 404);
                }
                $box = Box::find($boxUser->box_id);
                if (!$box) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Box not found',
                    ], 404);
                }

                $type = $request->input('type', 'whatsapp');

                if ($type === 'whatsapp') {
                    // Save WhatsApp token
                    $params = $box->whatsapp_token ?? [];
                    $params['access_token'] = $accessToken;
                    $params['expires_at'] = null; // Manual tokens don't have expiry info
                    $params['token_type'] = 'bearer';
                    $params['connected_at'] = now()->toDateTimeString();
                    $params['manual_entry'] = true;

                    $box->whatsapp_token = $params;
                    $box->save();

                    // Try to fetch WABA
                    $whatsapp = new \App\Helpers\WhatsApp($box);
                    $wabaResult = $whatsapp->getWABA($box);

                    return response()->json([
                        'success' => true,
                        'message' => 'WhatsApp Business account connected successfully!',
                        'show_selector' => ($wabaResult['data']['active_accounts'] ?? 0) > 1,
                        'data' => [
                            'waba_result' => $wabaResult,
                        ],
                    ]);
                }
                else {
                    // Save Facebook API token
                    $params = $box->facebook_api_params ?? [];
                    $params['access_token'] = $accessToken;
                    $params['expires_at'] = null;
                    $params['token_type'] = 'bearer';
                    $params['connected_at'] = now()->toDateTimeString();
                    $params['manual_entry'] = true;

                    $box->facebook_api_params = $params;
                    $box->facebook_access_token = $accessToken; // Also save to dedicated column
                    $box->save();

                    // Save token to box_crm_integrations if it exists
                    $fbIntegration = \App\Models\BoxCrmIntegration::where('box_id', $box->id)
                        ->whereHas('api', function ($q) {
                        $q->where('system_name', 'facebook');
                    })->first();

                    if ($fbIntegration) {
                        $fbIntegration->token = $accessToken;
                        $fbIntegration->connection_status = 'Connected';
                        $fbIntegration->save();
                    }

                    // Also fetch pages and forms
                    try {
                        $whatsapp = new \App\Helpers\WhatsApp($box);
                        $whatsapp->getPages($box);
                    }
                    catch (\Exception $e) {
                        Log::warning('Failed to fetch pages during manual token entry', [
                            'box_id' => $box->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Facebook token saved successfully',
                    ]);
                }
            }

            // Regular OAuth flow
            $validator = Validator::make($request->all(), [
                'code' => 'required|string', // Authorization code from FB.login()
                'type' => 'nullable|string|in:api,whatsapp', // Type of integration
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box user not found',
                ], 404);
            }
            $box = Box::find($boxUser->box_id);
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found',
                ], 404);
            }
            $whatsapp = new \App\Helpers\WhatsApp($box);
            $result = $whatsapp->authorize($box, $request);
            
            $box->refresh();
            
            // Determine active token based on type
            $accessToken = null;
            if ($request->type === 'whatsapp') {
                $accessToken = isset($box->whatsapp_token['access_token']) ? $box->whatsapp_token['access_token'] : null;
                
                // Also enable WhatsApp for this box
                if ($accessToken) {
                    $box->whatsapp_enable = 'Yes';
                    $box->save();
                }
            } else {
                $accessToken = isset($box->facebook_api_params['access_token']) ? $box->facebook_api_params['access_token'] : null;
            }

            if ($result && isset($result['success']) && $result['success']) {
                // Save token to box_crm_integrations if it exists (for Leads/Sync visibility)
                $fbIntegrationName = ($request->type === 'whatsapp') ? 'whatsapp' : 'facebook';
                $fbIntegration = \App\Models\BoxCrmIntegration::where('box_id', $box->id)
                    ->whereHas('api', function ($q) use ($fbIntegrationName) {
                        $q->where('system_name', $fbIntegrationName);
                    })->first();

                // If no specific whatsapp integration entry, fallback to facebook entry for backward compatibility
                if (!$fbIntegration && $request->type === 'whatsapp') {
                    $fbIntegration = \App\Models\BoxCrmIntegration::where('box_id', $box->id)
                        ->whereHas('api', function ($q) {
                            $q->where('system_name', 'facebook');
                        })->first();
                }

                if ($fbIntegration) {
                    $fbIntegration->token = $accessToken;
                    $fbIntegration->connection_status = 'Connected';
                    $fbIntegration->save();
                }

                if ($request->type === 'whatsapp') {
                    // WABA discovery is already handled inside $whatsapp->authorize()
                    $wabaResult = $result['data']['waba_result'] ?? null;
                    if ($wabaResult && isset($wabaResult['success']) && $wabaResult['success']) {
                        $activeAccounts = $wabaResult['data']['active_accounts'] ?? 0;
                        $connectedPhones = $wabaResult['data']['connected_phones'] ?? 0;
                        $activeAccountDetails = $wabaResult['data']['active_account_details'] ?? [];
                        $message = 'WhatsApp Business account connected successfully!';
                        
                        // Set flash to show selector if multiple accounts were saved to the database
                        // (With the new strict discovery, this should normally be 1, but we keep it for safety)
                        if (($wabaResult['data']['accounts'] ?? 0) > 1) {
                            session()->flash('show_whatsapp_selector', true);
                        }

                        if ($activeAccounts > 0) {
                            $message .= "\n\n✅ Active Accounts: {$activeAccounts}";
                            $message .= "\n✅ Connected Phone Numbers: {$connectedPhones}";
                            if (!empty($activeAccountDetails)) {
                                $message .= "\n\nActive Account Details:";
                                foreach ($activeAccountDetails as $account) {
                                    $message .= "\n• " . ($account['name'] ?? 'Unnamed Account');
                                    $message .= " (Status: " . ($account['account_status'] ?? 'N/A') . ")";
                                    if (!empty($account['phone_numbers'])) {
                                        $message .= " - " . count($account['phone_numbers']) . " connected phone(s)";
                                        foreach ($account['phone_numbers'] as $phone) {
                                            $message .= "\n  - " . ($phone['display_phone_number'] ?? 'N/A');
                                            $message .= " (" . ($phone['verified_name'] ?? 'Unverified') . ")";
                                        }
                                    }
                                }
                            }
                        }
                        else {
                            $message .= "\n\n⚠️ No active accounts found. Please check your WhatsApp Business account status.";
                        }
                        return response()->json([
                            'success' => true,
                            'message' => $message,
                            'show_selector' => ($wabaResult['data']['accounts'] ?? 0) > 1,
                            'data' => array_merge($result['data'] ?? [], $wabaResult['data'] ?? []),
                        ]);
                    }
                    else {
                        return response()->json([
                            'success' => true,
                            'message' => 'WhatsApp Business connected. Token saved successfully.',
                            'data' => $result['data'] ?? [],
                            'warning' => $wabaResult['error'] ?? 'Could not fetch WhatsApp Business accounts',
                        ]);
                    }
                }
                else {
                    $box->refresh();
                    $hasParams = !empty($box->facebook_api_params);
                    $accessToken = $hasParams ? ($box->facebook_api_params['access_token'] ?? null) : null;
                    $tokenSaved = $hasParams && !empty($accessToken);
                    if (!$tokenSaved) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Token exchange successful but failed to save. Please try again.',
                            'debug' => [
                                'has_facebook_api_params' => $hasParams,
                                'has_access_token_key' => $hasParams && isset($box->facebook_api_params['access_token']),
                                'access_token_empty' => empty($accessToken),
                            ],
                        ], 500);
                    }
                    try {
                        $integrationResult = $whatsapp->getBusinessIntegration($box);
                        if ($integrationResult['success']) {
                            $box->refresh();
                        }
                        else {
                        }
                    }
                    catch (\Exception $e) {

                    // Don't fail the whole process if business integration fetch fails
                    }

                    // Fetch pages and forms - already handled inside $whatsapp->authorize()
                    // Just refresh to get updated counts
                    $box->refresh();

                    $box->refresh();
                    $savedIntegration = $box->facebook_api_params['business_integration'] ?? null;
                    $pagesCount = \App\Models\FacebookPage::where('box_id', $box->id)->count();
                    $formsCount = \App\Models\FacebookForm::where('box_id', $box->id)->count();
                    $responseData = [
                        'access_token_saved' => true,
                        'pages_count' => $pagesCount,
                        'forms_count' => $formsCount,
                    ];
                    if ($savedIntegration) {
                        $responseData['business_integration'] = [
                            'id' => $savedIntegration['id'] ?? null,
                            'name' => $savedIntegration['name'] ?? 'BizzCRM',
                            'app_id' => $savedIntegration['app_id'] ?? null,
                            'created_time' => $savedIntegration['created_time'] ?? null,
                        ];
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Facebook integration saved successfully! ' .
                        ($savedIntegration ? 'Business integration details have been fetched and saved.' : 'Business integration details could not be fetched automatically. You can fetch them manually using "Get Integration Details" button.'),
                        'data' => $responseData,
                        'verified' => true,
                    ]);
                }
            }
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to exchange code for access token',
            ], 400);
        }
        catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to save Facebook integration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function labels()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'labels'))) {
            abort(403, 'You do not have permission to access labels settings.');
        }

        return view('settings.labels');
    }
    public function bulk_uploades()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('integrations', 'read', 'bulk_upload'))) {
            abort(403, 'You do not have permission to access bulk uploads.');
        }

        return view('settings.bulk_uploades');
    }
    public function teams()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read'))) {
            abort(403, 'You do not have permission to access teams settings.');
        }

        return view('settings.teams');
    }
    public function customFields(Request $request)
    {
        return $this->index($request);
    }

    public function salaryCustomFields(Request $request)
    {
        $userId = Auth::id();
        $box = BoxUser::where('user_id', $userId)->select('id', 'box_id')->first();
        $fields = BoxCustomField::where('box_id', $box->box_id)
            ->where('object', 'like', 'salary_%')
            ->orderBy('ordering', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $fields,
            'count' => $fields->count(),
        ]);
    }
    public function emailInboxes()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'email_inboxes'))) {
            abort(403, 'You do not have permission to access email inboxes settings.');
        }

        $box = $this->box;
        $inboxes = [];
        if ($box) {
            $inboxes = GmailInbox::with(['filters' => function($q) {
                $q->with(['template', 'assignee', 'leadSource', 'leadStatus']);
            }])->where('box_id', $box->id)->get();
        }

        return view('settings.email-inboxes', compact('inboxes', 'box'));
    }

    public function emailMessages()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'email_messages'))) {
            abort(403, 'You do not have permission to access email messages.');
        }

        $box = $this->box;
        $inboxes = [];
        if ($box) {
            $inboxes = GmailInbox::where('box_id', $box->id)->get();
        }

        return view('settings.email-messages', compact('inboxes'));
    }

    public function getEmailMessages($box_slug, Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'email_messages'))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $box = $this->box;
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $inboxId = $request->query('inbox_id');
        $query = GmailMessage::with('inbox');

        if ($inboxId) {
            $query->where('gmail_inbox_id', $inboxId);
        }
        else {
            $inboxIds = GmailInbox::where('box_id', $box->id)->pluck('id');
            $query->whereIn('gmail_inbox_id', $inboxIds);
        }

        return DataTables::of($query)
            ->addColumn('from', function ($msg) {
            return '<strong>' . e($msg->from_name ?: 'Unknown') . '</strong><br><small class="text-muted">' . e($msg->from_email) . '</small>';
        })
            ->addColumn('subject_snippet', function ($msg) {
            // Check for attachments in raw_data
            $hasAttachments = false;
            if ($msg->raw_data && isset($msg->raw_data['payload']['parts'])) {
                foreach ($msg->raw_data['payload']['parts'] as $part) {
                    if (isset($part['body']['attachmentId'])) {
                        $hasAttachments = true;
                        break;
                    }
                    if (isset($part['parts'])) {
                        foreach ($part['parts'] as $subPart) {
                            if (isset($subPart['body']['attachmentId'])) {
                                $hasAttachments = true;
                                break;
                            }
                        }
                        if ($hasAttachments)
                            break;
                    }
                }
            }

            $attachmentIcon = $hasAttachments ? '<i class="ti ti-paperclip text-muted ms-1" title="Has attachment" style="font-size: 14px;"></i>' : '';

            return '<div class="text-dark fw-medium d-flex align-items-center">' . e(Str::limit($msg->subject, 50)) . $attachmentIcon . '</div>' .
                '<div class="text-muted small">' . e(Str::limit($msg->snippet, 70)) . '</div>';
        })
            ->editColumn('is_processed', function ($msg) {
            if ($msg->activity_status === 'Uploaded' || ($msg->is_processed && $msg->lead_id)) {
                return '<span class="badge bg-soft-success text-success"><i class="ti ti-check me-1"></i>Uploaded</span>';
            }
            return '<span class="badge bg-soft-warning text-warning">Skip</span>';
        })
            ->editColumn('received_at', function ($msg) {
            return $msg->received_at->format('d M Y, h:i A');
        })
            ->addColumn('action', function ($msg) {
            return '<button class="btn btn-action-icon" onclick="viewEmailDetails(' . $msg->id . ')">
                            <i class="ti ti-eye"></i>
                        </button>';
        })
            ->rawColumns(['from', 'subject_snippet', 'is_processed', 'action'])
            ->make(true);
    }

    public function getEmailMessageDetail(Request $request, $box_slug, $id)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'email_messages'))) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $box = $this->box;
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box context missing. Please reload page.'], 404);
            }

            $message = GmailMessage::with('inbox')
                ->whereHas('inbox', function ($q) use ($box) {
                $q->where('box_id', $box->id);
            })
                ->find($id);

            if (!$message) {
                Log::warning("EmailDetail Error: Message ID $id not found for box slug $box_slug (Box ID: " . $box->id . ")");
                return response()->json(['success' => false, 'message' => 'Email message not found.'], 404);
            }

            $attachments = [];
            if ($message->raw_data && isset($message->raw_data['payload']['parts'])) {
                foreach ($message->raw_data['payload']['parts'] as $part) {
                    if (isset($part['body']['attachmentId'])) {
                        $attachments[] = [
                            'attachmentId' => $part['body']['attachmentId'],
                            'filename' => $part['filename'] ?? 'unnamed',
                            'mimeType' => $part['mimeType'] ?? 'application/octet-stream',
                            'size' => $part['body']['size'] ?? 0,
                        ];
                    }
                    // Handle nested parts (common in multipart messages)
                    if (isset($part['parts'])) {
                        foreach ($part['parts'] as $subPart) {
                            if (isset($subPart['body']['attachmentId'])) {
                                $attachments[] = [
                                    'attachmentId' => $subPart['body']['attachmentId'],
                                    'filename' => $subPart['filename'] ?? 'unnamed',
                                    'mimeType' => $subPart['mimeType'] ?? 'application/octet-stream',
                                    'size' => $subPart['body']['size'] ?? 0,
                                ];
                            }
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $message,
                'received_at_formatted' => $message->received_at ? $message->received_at->format('d M Y, h:i A') : 'Unknown',
                'attachments' => $attachments
            ]);
        }
        catch (\Exception $e) {
            Log::error("EmailDetail Fatal Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function addGmailMessageAsLead(Request $request, $box_slug)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $box = $this->box;

            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box context missing.'], 404);
            }

            $id = $request->input('message_id');
            $message = GmailMessage::whereHas('inbox', function ($q) use ($box) {
                $q->where('box_id', $box->id);
            })->find($id);

            if (!$message) {
                return response()->json(['success' => false, 'message' => 'Message not found.'], 404);
            }

            if ($message->is_processed) {
                return response()->json(['success' => false, 'message' => 'This message is already processed.'], 400);
            }

            // Check if lead already exists by email
            $existingLead = Lead::where('box_id', $box->id)
                ->where(function ($q) use ($message) {
                $q->where('email', $message->from_email)
                    ->orWhere('sec_email', $message->from_email);
            })->first();

            DB::beginTransaction();

            if (!$existingLead) {
                // Create new lead
                $existingLead = Lead::create([
                    'box_id' => $box->id,
                    'name' => $message->from_name ?: $message->from_email,
                    'email' => $message->from_email,
                    'phone' => '0000000000', // Dummy phone as it might be required
                    'lead_type' => 'Direct Lead',
                    'added_by' => $user->id,
                    'description' => 'Created from Gmail Sync (Manual)',
                    'status' => 'Active',
                    'is_paid' => 1,
                    'assignment_type' => 'gmail_sync',
                ]);

                // Create initial activity for lead creation
                Activity::create([
                    'box_id' => $box->id,
                    'activity_id' => $existingLead->id,
                    'type' => 'history',
                    'subject' => 'Lead Created (Gmail Sync)',
                    'description' => 'Lead automatically created from Gmail message manual conversion.',
                    'activity_date' => now(),
                    'added_by' => $user->id,
                    'status' => 'Close'
                ]);
            }

            // Always create a new activity for THIS email
            Activity::create([
                'box_id' => $box->id,
                'activity_id' => $existingLead->id,
                'type' => 'email', // Or 'inbox' depending on your types
                'subject' => 'Gmail: ' . ($message->subject ?: '(No Subject)'),
                'description' => strip_tags($message->body),
                'activity_date' => $message->received_at,
                'added_by' => $user->id,
                'status' => 'Close',
                'completed_date' => now(),
                'params' => json_encode([
                    'message_id' => $message->message_id,
                    'thread_id' => $message->thread_id,
                    'from' => $message->from_email
                ])
            ]);

            // Mark message as processed
            $message->update([
                'is_processed' => true,
                'lead_id' => $existingLead->id,
                'activity_status' => 'Uploaded'
            ]);

            // Mark as read in Gmail
            try {
                $googleController = new \App\Http\Controllers\GoogleController();
                $googleController->refreshTokenIfExpired($message->inbox);

                Http::withToken($message->inbox->access_token)
                    ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$message->message_id}/modify", [
                    'removeLabelIds' => ['UNREAD']
                ]);
            }
            catch (\Exception $e) {
                Log::error("Manual Mark Read Error: " . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead ' . ($existingLead->wasRecentlyCreated ? 'created' : 'linked') . ' and activity recorded successfully.',
                'lead_id' => $existingLead->id
            ]);

        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error("AddGmailAsLead Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function apiKeys()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'api_keys'))) {
            abort(403, 'You do not have permission to access api keys settings.');
        }

        $box = $this->box;
        $googleConnected = false;
        $connectedGmailCount = 0;
        $connectedGmails = [];

        if ($box) {
            $facebookConnected = !empty($box->facebook_access_token) || !empty($box->facebook_api_params);
            $gmailInboxes = GmailInbox::where('box_id', $box->id)->where('status', 'Enabled')->get();
            $connectedGmailCount = $gmailInboxes->count();
            $googleConnected = $connectedGmailCount > 0;
            $connectedGmails = $gmailInboxes;
        }

        return view('settings.api-keys', [
            'box' => $box,
            'facebookConnected' => $facebookConnected,
            'googleConnected' => $googleConnected,
            'connectedGmailCount' => $connectedGmailCount,
            'connectedGmails' => $connectedGmails
        ]);
    }


    public function rolesPermissions()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $box = $this->box;
        $boxUser = $box ?BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first() : null;
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'roles_permissions'))) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view roles and permissions.');
        }

        $box = $this->box;
        if (!$box) {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid box context.');
        }

        // Fetch departments related to the box
        $departments = Department::where('box_id', $box->id)->get();
        // Since we are attaching permissions to departments now, we treat departments similar to roles in this context
        // Each department can have its own set of permissions

        $roles = \Spatie\Permission\Models\Role::all(); // Keep system roles for reference or global templates
        $permissions = \Spatie\Permission\Models\Permission::all();

        return view('settings.roles-permissions', compact('box', 'departments', 'roles', 'permissions'));
    }

    public function permissions(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $box = $this->box;
        $boxUser = $box ?BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first() : null;
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('user_management', 'read', 'permissions'))) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access permissions.'
                ], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access permissions.');
        }

        // 2. Initial Page Load (Shell only)
        if (!$request->ajax()) {
            return view('user_management.permissions.index');
        }

        // 1. Common Data for Dropdowns
        // Filter only 'admin' and 'employee' roles
        $roles = \Spatie\Permission\Models\Role::whereIn('name', ['admin', 'employee'])->get();

        $departments = collect();
        if ($this->box) {
            $departments = Department::where('box_id', $this->box->id)->get();
        }

        // 3. AJAX Logic (Fetch Permissions Data)
        $selectedRoleId = $request->get('role_id');
        $rawDepartmentId = $request->get('department_id');
        $selectedDepartmentId = is_array($rawDepartmentId) ? ($rawDepartmentId[0] ?? null) : $rawDepartmentId;

        $targetEntity = null; // Will be either a Role or a Department model
        $rolePermissions = [];

        $teams = collect();
        if ($rawDepartmentId) {
            $deptIdsForTeams = is_array($rawDepartmentId) ? $rawDepartmentId : [$rawDepartmentId];
            $teams = \App\Models\Team::whereIn('department_id', $deptIdsForTeams)->get();
        }
        elseif ($this->box) {
            $teams = \App\Models\Team::where('box_id', $this->box->id)->get();
        }

        $rawTeamId = $request->get('team_id');
        $selectedTeamId = is_array($rawTeamId) ? ($rawTeamId[0] ?? null) : $rawTeamId;

        // Determine the Target Entity (Team OR Department OR Role)
        if ($selectedTeamId) {
            // Mode: Managing Team Permissions
            $targetEntity = \App\Models\Team::find($selectedTeamId);

            // Verify it belongs to selected department if set
            if ($selectedDepartmentId && $targetEntity && $targetEntity->department_id != $selectedDepartmentId) {
                $targetEntity = null; // Mismatch
            }
        }
        elseif ($selectedDepartmentId) {
            // Mode: Managing Department Permissions
            // Ensure the department belongs to the current box
            $boxId = $this->box ? $this->box->id : null;
            if ($boxId) {
                $targetEntity = Department::where('box_id', $boxId)->where('id', $selectedDepartmentId)->first();
            }
            else {
                $targetEntity = Department::find($selectedDepartmentId);
            }
        }
        elseif ($selectedRoleId) {
            $targetEntity = \Spatie\Permission\Models\Role::find($selectedRoleId);
        }
        else {
            // Fallback Logic for AJAX:
            // If no specific ID requested, we default to the 'employee' role (Designation)
            $defaultRole = $roles->where('name', 'employee')->first();
            // If employee role not found, fall back to admin, then first available role
            if (!$defaultRole) {
                $defaultRole = $roles->where('name', 'admin')->first();
            }

            $targetEntity = $defaultRole ?? $roles->first();
            $selectedRoleId = $targetEntity ? $targetEntity->id : null;
        }

        // Load Permissions
        if ($targetEntity) {
            $rolePermissions = $targetEntity->permissions->pluck('name')->toArray();
        }

        // Prepare Grouped Permissions
        $permissions = \Spatie\Permission\Models\Permission::all();
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            if ($permission->name === 'all.permissions') {
                $groupedPermissions['All Modules']['All'][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'action' => 'All Permissions',
                    'checked' => in_array($permission->name, $rolePermissions)
                ];
                continue;
            }

            $parts = explode('.', $permission->name);
            if (count($parts) >= 2) {
                $module = $parts[0];
                $subModule = count($parts) === 3 ? $parts[1] : 'General';
                $action = $parts[count($parts) - 1];

                $groupedPermissions[$module][$subModule][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'action' => $action,
                    'checked' => in_array($permission->name, $rolePermissions)
                ];
            }
            else {
                $groupedPermissions['Other']['General'][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'action' => $permission->name,
                    'checked' => in_array($permission->name, $rolePermissions)
                ];
            }
        }

        ksort($groupedPermissions);
        foreach ($groupedPermissions as $module => &$subModules) {
            ksort($subModules);
        }

        if ($request->ajax() && $request->has('ajax_load')) {
            $entityName = '';
            if ($targetEntity) {
                if ($targetEntity instanceof \App\Models\Team) {
                    $entityName = $targetEntity->name . ' Team';
                }
                elseif ($targetEntity instanceof Department) {
                    $entityName = $targetEntity->name . ' Department';
                }
                else {
                    $entityName = ucfirst($targetEntity->name) . ' Designation';
                }
            }
            $availableModules = [];
            $availableSubmodules = [];
            foreach ($groupedPermissions as $mod => $subs) {
                if ($mod !== 'All Modules' && $mod !== 'Other') {
                    $availableModules[] = $mod;
                }
                foreach (array_keys($subs) as $sub) {
                    if ($sub !== 'All' && $sub !== 'General') {
                        $availableSubmodules[] = $sub;
                    }
                }
            }

            return response()->json([
                'html' => view('user_management.permissions.partials.permissions_table', [
                    'permissions_form_route' => box_route('user.permissions.updateRole'),
                    'selectedDepartmentId' => $selectedDepartmentId,
                    'selectedTeamId' => $selectedTeamId ?? null,
                    'selectedRoleId' => $selectedRoleId,
                    'groupedPermissions' => $groupedPermissions,
                    'targetEntity' => $targetEntity,
                    'teams' => $teams
                ])->render(),
                'entityName' => $entityName,
                // Return IDs back ensuring frontend sync
                'department_id' => $selectedDepartmentId,
                'team_id' => $selectedTeamId ?? null,
                'role_id' => $selectedRoleId,
                'roles' => [
                    'employee' => $roles->where('name', 'employee')->first()->id ?? 0,
                    'admin' => $roles->where('name', 'admin')->first()->id ?? 0,
                ],
                'available_modules' => array_values(array_unique($availableModules)),
                'available_submodules' => array_values(array_unique($availableSubmodules)),
                'departments' => $departments->map(function ($dept) {
                return ['id' => $dept->id, 'name' => $dept->name];
            }),
                'teams' => $teams->map(function ($team) {
                return ['id' => $team->id, 'name' => $team->name];
            })
            ]);
        }

        $entityName = $targetEntity ? ($selectedDepartmentId ? $targetEntity->name . ' Department' : $targetEntity->name . ' Designation') : '';

        return response()->json([
            'html' => view('user_management.permissions.partials.permissions_table', [
                'permissions_form_route' => box_route('user.permissions.updateRole'),
                'selectedDepartmentId' => $selectedDepartmentId,
                'selectedRoleId' => $selectedRoleId,
                'groupedPermissions' => $groupedPermissions,
                'targetEntity' => $targetEntity
            ])->render(),
            'entityName' => $entityName,
            // Return IDs back ensuring frontend sync
            'department_id' => $selectedDepartmentId,
            'role_id' => $selectedRoleId
        ]);
    }

    public function updateRolePermissions(Request $request)
    {
        $user = Auth::user();
        $box = $this->box;
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web') || $user->id === 1;

        // Box superadmin check
        $isBoxSuperAdmin = $box && $box->superadmin_id == $user->id;

        $boxUser = $box ?BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first() : null;
        $isBoxAdmin = ($boxUser && strtolower($boxUser->staff_role ?? '') === 'admin') || $isBoxSuperAdmin;

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('user_management', 'update', 'permissions'))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update role permissions.'
            ], 403);
        }

        $request->validate([
            'permissions' => 'array',
        ]);

        $targetName = "Unknown";
        $count = 0;

        if ($request->has('team_id') && !empty($request->team_id)) {
            // Update Team Permissions AND Siblings
            $teamIds = is_array($request->team_id) ? $request->team_id : [$request->team_id];

            $baseTeams = \App\Models\Team::whereIn('id', $teamIds)->get();
            $teamsToSync = collect($baseTeams);

            // Find all siblings (teams with the same parent_id and department_id)
            foreach ($baseTeams as $bTeam) {
                $siblings = \App\Models\Team::where('department_id', $bTeam->department_id)
                    ->where('parent_id', $bTeam->parent_id)
                    ->get();
                $teamsToSync = $teamsToSync->merge($siblings);
            }

            foreach ($teamsToSync->unique('id') as $teamToSync) {
                $teamToSync->syncPermissions($request->permissions ?? []);
            }

            $targetName = "Selected Team(s) & Siblings";

        }
        elseif ($request->has('department_id') && !empty($request->department_id)) {
            // Update Department Permissions AND Siblings
            $deptIds = is_array($request->department_id) ? $request->department_id : [$request->department_id];

            $baseDepts = Department::whereIn('id', $deptIds)->get();
            $deptsToSync = collect($baseDepts);

            // Find siblings for the departments
            foreach ($baseDepts as $bDept) {
                $siblings = Department::where('box_id', $bDept->box_id)
                    ->where('parent_id', $bDept->parent_id)
                    ->get();
                $deptsToSync = $deptsToSync->merge($siblings);
            }

            foreach ($deptsToSync->unique('id') as $deptToSync) {
                $deptToSync->syncPermissions($request->permissions ?? []);
            }

            $targetName = "Selected Department(s) & Siblings";

        }
        else {
            // Update Role Permissions (Designations)
            $role = \Spatie\Permission\Models\Role::findOrFail($request->role_id);
            $role->syncPermissions($request->permissions ?? []);
            $targetName = ucfirst($role->name) . " Designation";
        }

        // Sync permissions and clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Permissions successfully updated for: ' . $targetName,
                'count' => count($request->permissions ?? [])
            ]);
        }

        return redirect()->back()->with('success', 'Permissions successfully updated for: ' . $targetName);
    }

    public function syncPermissions()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $box = $this->box;
        $boxUser = $box ?BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first() : null;
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('user_management', 'update', 'permissions'))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to sync permissions.'
            ], 403);
        }

        // Use centralized modules registry
        $modulesData = \App\Helpers\PermissionHelper::allModules();
        $modules = [];
        foreach ($modulesData as $key => $data) {
            $modules[$key] = $data['sub_modules'] ?? [];
        }

        $actions = ['read', 'create', 'update', 'delete', 'generate', 'import', 'export'];
        $allPermissionNames = [];

        // 1. Create all.permissions
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'all.permissions', 'guard_name' => 'web']);
        $allPermissionNames[] = 'all.permissions';

        // 2. Generate permissions for modules and submodules
        foreach ($modules as $module => $subs) {
            // Module level
            foreach ($actions as $action) {
                $name = "$module.$action";
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                $allPermissionNames[] = $name;
            }
            // Submodule level
            foreach ($subs as $sub) {
                foreach ($actions as $action) {
                    $name = "$module.$sub.$action";
                    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                    $allPermissionNames[] = $name;
                }
            }
        }

        // 3. Sync Roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $employeeRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Admin gets everything
        $adminRole->syncPermissions($allPermissionNames);

        // Manager gets Read, Create, Update
        $managerPermissions = [];
        foreach ($allPermissionNames as $pName) {
            if (str_ends_with($pName, '.read') || str_ends_with($pName, '.create') || str_ends_with($pName, '.update')) {
                $managerPermissions[] = $pName;
            }
        }
        $managerRole->syncPermissions($managerPermissions);

        // Employee gets Read for specific modules
        $employeePermissions = [];
        $employeeAllowed = ['attendance', 'hr_tools', 'payroll'];
        foreach ($allPermissionNames as $pName) {
            foreach ($employeeAllowed as $empMod) {
                if (str_starts_with($pName, $empMod . '.') && str_ends_with($pName, '.read')) {
                    $employeePermissions[] = $pName;
                }
            }
        }
        $employeeRole->syncPermissions($employeePermissions);

        return response()->json([
            'status' => true,
            'message' => 'Permissions successfully synced.'
        ]);
    }

    public function storePermission(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $box = $this->box;
        $boxUser = $box ?BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first() : null;
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('user_management', 'create', 'permissions'))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to store permissions.'
            ], 403);
        }

        $request->validate([
            'module' => 'required',
            'submodule' => 'nullable',
            'actions' => 'required_without:custom_actions|array',
            'custom_actions' => 'nullable',
            'role_ids' => 'nullable|array',
            'guard_name' => 'nullable'
        ]);

        $actions = $request->actions ?? [];
        if ($request->custom_actions) {
            $custom = explode(',', $request->custom_actions);
            foreach ($custom as $c) {
                $trimmed = trim($c);
                if ($trimmed) {
                    $actions[] = $trimmed;
                }
            }
        }

        $actions = array_unique($actions);
        $count = 0;
        $module = strtolower(trim($request->module));
        $submodule = $request->submodule ? strtolower(trim($request->submodule)) : null;
        $guard = $request->guard_name ?? 'web';
        $roleIds = $request->role_ids ?? [];

        foreach ($actions as $action) {
            $name = $module . ($submodule ? '.' . $submodule : '') . '.' . strtolower(trim($action));

            $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => $guard
            ]);

            // Assign to roles if selected
            if (!empty($roleIds)) {
                foreach ($roleIds as $roleId) {
                    $role = \Spatie\Permission\Models\Role::find($roleId);
                    if ($role) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
            $count++;
        }

        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to create permissions.'
        ], 403);
    }

    // Website Settings
    public function localization()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'localization'))) {
            abort(403, 'You do not have permission to access localization settings.');
        }

        return view('settings.localization');
    }
    public function prefixes()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'prefixes'))) {
            abort(403, 'You do not have permission to access prefixes settings.');
        }

        return view('settings.prefixes');
    }
    public function preference()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'preference'))) {
            abort(403, 'You do not have permission to access preference settings.');
        }

        return view('settings.preference');
    }
    public function appearance()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'appearance'))) {
            abort(403, 'You do not have permission to access appearance settings.');
        }

        return view('settings.appearance');
    }
    public function language()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'language'))) {
            abort(403, 'You do not have permission to access language settings.');
        }

        return view('settings.language');
    }


    // App Settings
    public function invoiceSettings()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('app_settings', 'read', 'invoice'))) {
            abort(403, 'You do not have permission to access invoice settings.');
        }

        $settings = \App\Models\Accounts\InvoiceSetting::firstOrCreate(
            ['box_id' => $boxUser->box_id],
            [
                'invoice_prefix' => '-',
                'invoice_due_days' => 5,
                'enable_round_off' => true,
                'round_off_type' => 'Up',
                'show_company_details' => true
            ]
        );

        return view('settings.invoice-settings', compact('settings'));
    }
    public function printers()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('app_settings', 'read', 'printers'))) {
            abort(403, 'You do not have permission to access printers settings.');
        }

        return view('settings.printers');
    }

    // System Settings
    public function emailSettings()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('system_settings', 'read', 'email'))) {
            abort(403, 'You do not have permission to access email settings.');
        }

        return view('settings.email-settings');
    }
    public function smsGateways()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('system_settings', 'read', 'sms_gateways'))) {
            abort(403, 'You do not have permission to access SMS gateways settings.');
        }

        return view('settings.sms-gateways');
    }
    public function gdprCookies()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('system_settings', 'read', 'gdpr'))) {
            abort(403, 'You do not have permission to access GDPR/Cookies settings.');
        }

        return view('settings.gdpr-cookies');
    }

    // Financial Settings
    public function paymentGateways()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'payment_gateways'))) {
            abort(403, 'You do not have permission to access payment gateways settings.');
        }

        return view('settings.payment-gateways');
    }
    public function taxRates()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'tax_rates'))) {
            abort(403, 'You do not have permission to access tax rates settings.');
        }

        return view('settings.tax-rates');
    }

    /**
     * Get user column preferences for a specific page
     */
    public function getColumnPreferences(Request $request)
    {
        try {
            $userId = Auth::id();
            $pageIdentifier = $request->input('page_identifier');

            if (!$pageIdentifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page identifier is required'
                ], 400);
            }

            $preference = \App\Models\UserColumnPreference::where('user_id', $userId)
                ->where('page_identifier', $pageIdentifier)
                ->first();

            return response()->json([
                'success' => true,
                'data' => $preference ? $preference->column_preferences : []
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching column preferences',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Save user column preferences for a specific page
     */
    public function saveColumnPreferences(Request $request)
    {
        try {
            $userId = Auth::id();
            $pageIdentifier = $request->input('page_identifier');
            $columnPreferences = $request->input('column_preferences');

            \Log::info('Saving column preferences', [
                'user_id' => $userId,
                'page_identifier' => $pageIdentifier,
                'preferences_count' => is_array($columnPreferences) ? count($columnPreferences) : 'not an array'
            ]);

            if (!$pageIdentifier || !$columnPreferences) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page identifier and column preferences are required'
                ], 400);
            }

            $preference = \App\Models\UserColumnPreference::updateOrCreate(
            [
                'user_id' => $userId,
                'page_identifier' => $pageIdentifier
            ],
            [
                'column_preferences' => $columnPreferences
            ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Column preferences saved successfully',
                'data' => $preference->column_preferences
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving column preferences',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function currencies()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('financial_settings', 'read', 'currencies'))) {
            abort(403, 'You do not have permission to access currencies settings.');
        }

        return view('settings.currencies');
    }

    // Other
    public function storage()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('other_settings', 'read', 'storage'))) {
            abort(403, 'You do not have permission to access storage settings.');
        }

        $box = \App\Models\Box::find($boxUser->box_id);

        return view('settings.storage', compact('box'));
    }
    public function banIpAddress()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('other_settings', 'read', 'ban_ip'))) {
            abort(403, 'You do not have permission to access ban ip settings.');
        }

        return view('settings.ban-ip-address');
    }

    public function box_settings()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('other_settings', 'read', 'box_settings'))) {
            abort(403, 'You do not have permission to access box settings.');
        }

        return view('settings.box_settings');
    }

    public function SearchCountry()
    {
        try {
            $countries = Country::select('id', 'name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $countries,
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load countries.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function box_settings_get(Request $request)
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();

            $boxUser = BoxUser::where('user_id', $userId)
                ->select(
                'id',
                'box_id',
                'working_days',
                'day_start_time',
                'day_end_time',
                'user_box_timing',
                'exlude_ip_restriction',
                'allowed_ips'
            )
                ->first();

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('other_settings', 'read', 'box_settings'))) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to view box settings.'], 403);
            }

            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'BoxUser not found.',
                ], 404);
            }

            // Get Box with users relation
            $boxData = Box::where('id', $boxUser->box_id)
                ->with('users')
                ->first();

            // Helper to safely decode JSON
            $safeJsonDecode = function ($value) {
                if (is_string($value)) {
                    return json_decode($value, true) ?: [];
                }
                if (is_array($value)) {
                    return $value;
                }
                return [];
            };

            // Helper to safely convert allowed_ips to array
            $parseAllowedIps = function ($value) {
                if (is_string($value)) {
                    return explode(',', $value);
                }
                if (is_array($value)) {
                    return $value;
                }
                return [];
            };

            return response()->json([
                'success' => true,
                'message' => 'Box settings fetched successfully.',
                'data' => [
                    'user_settings' => [
                        'working_days' => $safeJsonDecode($boxUser->working_days),
                        'day_start_time' => $boxUser->day_start_time ?? null,
                        'day_end_time' => $boxUser->day_end_time ?? null,
                        'user_box_timing' => $boxUser->user_box_timing ?? false,
                        'exclude_ip_restriction' => $boxUser->exlude_ip_restriction ?? false,
                        'allowed_ips' => $parseAllowedIps($boxUser->allowed_ips),
                    ],
                    'box_settings' => $boxData ? $boxData->toArray() : null,
                ],
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching box settings.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function box_settings_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'reminder_before' => 'nullable|string',
            'daily_reminder_time' => 'nullable|string',
            'lead_required_field' => 'nullable|string',
            'working_days' => 'nullable|array',
            'working_days.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'round_robin' => 'required|in:0,1',
            'round_robin_users' => 'nullable|array',
            'can_use_admin_mail' => 'required|in:0,1',
            'admin_mail_users' => 'nullable|array',
            'day_start_time' => 'nullable|string',
            'day_end_time' => 'nullable|string',
            'change_lead_assigned_if_admin' => 'required',
            'change_lead_status_if_admin' => 'required',
            'change_lead_source_if_admin' => 'required',
            'activity_reminder' => 'nullable|in:Disabled,Enable Only Notification,Enable Only Email,Enable Both',
            'allow_duplicate_leads' => 'required',
            'allow_duplicate_leads_auto' => 'required',
            'auto_task_close' => 'required',
            'auto_task_close_days' => 'nullable|integer|min:0',
            'allowed_ip' => 'nullable|string|max:255',
            'bulk_lead_assignment' => 'nullable',
            'lead_assignment' => 'nullable',
            'last_task_calculation_time' => 'nullable|string',
            'customer_location_radius' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $userId)->first();

        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('other_settings', 'update', 'box_settings'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to update box settings.'], 403);
        }

        try {
            $data = $validator->validated();
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            $boxId = $boxUser->box_id;
            $boxUser->update([
                'day_start_time' => $data['day_start_time'] ?? null,
                'day_end_time' => $data['day_end_time'] ?? null,
                'working_days' => json_encode($data['working_days'] ?? []),
            ]);

            Box::where('id', $boxId)->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'day_start_time' => $data['day_start_time'] ?? null,
                'day_end_time' => $data['day_end_time'] ?? null,
                'daily_reminder_time' => $data['daily_reminder_time'] ?? null,
                'reminder_before' => $data['reminder_before'] ?? null,
                'working_days' => json_encode($data['working_days'] ?? []),
                'auto_task_close' => $data['auto_task_close'],
                'auto_task_close_days' => $data['auto_task_close_days'] ?? null,
                'can_use_admin_mail' => $data['can_use_admin_mail'],
                'admin_mail_users' => json_encode($data['admin_mail_users'] ?? []),
                'round_robin' => $data['round_robin'],
                'round_robin_users' => json_encode($data['round_robin_users'] ?? []),
                'lead_required_field' => $data['lead_required_field'] ?? null,
                'allow_duplicate_leads' => $data['allow_duplicate_leads'],
                'allow_duplicate_leads_auto' => $data['allow_duplicate_leads_auto'],
                'activity_reminder' => $data['activity_reminder'] ?? null,
                'change_lead_assigned_if_admin' => $data['change_lead_assigned_if_admin'],
                'change_lead_status_if_admin' => $data['change_lead_status_if_admin'],
                'change_lead_source_if_admin' => $data['change_lead_source_if_admin'],
                'ip_address' => $data['allowed_ip'] ?? null,
                'bulk_lead_assignment' => $data['bulk_lead_assignment'] ?? null,
                'lead_assignment' => $data['lead_assignment'] ?? '0',
                'last_task_calculation_time' => $data['last_task_calculation_time'] ?? null,
                'customer_location_radius' => $data['customer_location_radius'] ?? 30
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Box settings saved successfully.',
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save box settings.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function attendace_module()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'attendance_module'))) {
            abort(403, 'You do not have permission to access attendance module settings.');
        }

        $authId = Auth::id();
        $boxUser = BoxUser::where('user_id', $authId)->first();
        if (!$boxUser) {
            return abort(404);
        }
        $box = Box::find($boxUser->box_id);
        if (!$box) {
            return abort(404);
        }

        return view('settings.attendace_module', [
            'box' => $box,
        ]);
    }

    public function getAttendanceModuleSettings()
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json(['success' => false, 'message' => 'Box user not found.'], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'attendance_module'))) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to view attendance module settings.'], 403);
            }

            $box = Box::find($boxUser->box_id);
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            // Get from_date - check if column exists, otherwise use start_day_of_every_month
            $fromDate = null;
            if ($box->leave_credit_mode === 'specific_number') {
                if (Schema::hasColumn('boxes', 'attendace_module_from_date')) {
                    $fromDate = $box->attendace_module_from_date;
                }
                else {
                    // Fallback: use start_day_of_every_month or leave_credit_selected_days
                    $fromDate = $box->start_day_of_every_month ?? $box->leave_credit_selected_days;
                }
            }
            else {
                $fromDate = $box->start_day_of_every_month;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'attendace_module_date' => $box->attendace_module_date,
                    // When specific_number mode, from_date comes from attendace_module_from_date
                    // Otherwise, use start_day_of_every_month for backward compatibility
                    'attendace_module_from_date' => $fromDate,
                    'attendace_module_to_date' => Schema::hasColumn('boxes', 'attendace_module_to_date')
                    ? $box->attendace_module_to_date
                    : $box->last_day_of_every_month,
                    'leave_credit_mode' => $box->leave_credit_mode,
                    'leave_credit_time' => $box->leave_credit_time,
                    'start_day_of_every_month' => $box->start_day_of_every_month,
                    'last_day_of_every_month' => $box->last_day_of_every_month,
                    'leave_credit_selected_days' => $box->leave_credit_selected_days,
                ],
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance module settings.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function saveAttendanceModuleSettings(Request $request)
    {
        try {
            $authId = Auth::id();
            $boxUser = BoxUser::where('user_id', $authId)->first();

            if (!$boxUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box user not found.',
                ], 404);
            }

            $user = Auth::user();
            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'attendance_module'))) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to update attendance module settings.'], 403);
            }

            $box = Box::find($boxUser->box_id);
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            // ===============================
            // VALIDATION
            // ===============================
            $validated = $request->validate([
                'date_time' => 'required|string',
                'from_date' => 'nullable|integer|min:1|max:30',
                'to_date' => 'nullable|integer|min:1|max:30',
                'credit_mode' => 'required|in:last_day_of_every_month,start_day_of_every_month,specific_number',
                'credit_time' => 'required|string',
                'selected_days' => 'nullable|string',
            ]);

            // ===============================
            // FROM_DATE / TO_DATE CONDITION
            // They CAN be different, only SAME is NOT allowed.
            // ===============================
            // Commented out since From Day and To Day sections are hidden
            // if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            //     if ((int)$validated['from_date'] === (int)$validated['to_date']) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'From day and To day cannot be the same.',
            //         ], 422);
            //     }
            // }

            // ===============================
            // SPECIFIC MODE → selected_days required
            // OTHER MODES  → selected_days = null
            // ===============================

            if ($validated['credit_mode'] === 'specific_number') {

                if (empty($validated['selected_days'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a day number for specific number mode.',
                    ], 422);
                }

                $selectedDay = $validated['selected_days']; // keep value
                // When specific_number is selected, save selected day to from_date (attendace_module_from_date)
                $fromDate = $validated['selected_days'];
            }
            else {

                // If mode is not specific_number → selected_days MUST be null
                $selectedDay = null;
                // from_date is not used when not specific_number (section is commented out)
                $fromDate = null;
            }

            // ===============================
            // SAVE SETTINGS
            // ===============================
            $updateData = [
                'attendace_module_date' => $validated['date_time'],
                'leave_credit_mode' => $validated['credit_mode'],
                'leave_credit_time' => $validated['credit_time'],
                'start_day_of_every_month' => null,
                'last_day_of_every_month' => $validated['to_date'] ?? null,
                'leave_credit_selected_days' => $selectedDay, // updated logic
            ];

            // When specific_number is selected, save selected day
            if ($validated['credit_mode'] === 'specific_number' && $fromDate !== null) {
                // Check if attendace_module_from_date column exists
                if (Schema::hasColumn('boxes', 'attendace_module_from_date')) {
                    $updateData['attendace_module_from_date'] = $fromDate;
                }
                else {
                    // Fallback: use start_day_of_every_month until migration is run
                    $updateData['start_day_of_every_month'] = $fromDate;
                }
            }
            else {
                // For other modes, use from_date if provided
                $updateData['start_day_of_every_month'] = $validated['from_date'] ?? null;
            }

            $box->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Attendance module settings saved successfully.',
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }
        catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to save attendance module settings.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function CompanySettings()
    {
        $authId = Auth::id();
        $boxUser = BoxUser::where('user_id', $authId)->first();
        if (!$boxUser) {
            return abort(404);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'company'))) {
            abort(403, 'You do not have permission to access company settings.');
        }
        $box = Box::with(['country', 'state', 'city'])->find($boxUser->box_id);
        if (!$box) {
            return abort(404);
        }
        $countries = Country::pluck('name', 'id');

        return view('settings.companysettings', [
            'box' => $box,
            'countries' => $countries,
        ]);
    }

    public function branchSettings()
    {
        $authId = Auth::id();
        $boxUser = BoxUser::where('user_id', $authId)->first();
        if (!$boxUser) {
            return abort(404);
        }
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'branch'))) {
            abort(403, 'You do not have permission to access branch settings.');
        }
        $box = Box::with(['country', 'state', 'city'])->find($boxUser->box_id);
        if (!$box) {
            return abort(404);
        }
        $countries = Country::pluck('name', 'id');
        return view('settings.branchsettings', [
            'box' => $box,
            'countries' => $countries,
        ]);
    }

    public function employeeTypeSettings()
    {
        // $user = Auth::user();
        // $boxUser = BoxUser::where('user_id', $user->id)->first();
        // $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        // $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        // if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'employee_type'))) {
        //      abort(403, 'You do not have permission to access employee type settings.');
        // }

        return view('settings.employeetypesettings');
    }

    public function getEmployeeTypes(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'Box user not found'], 404);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'employee_type'))) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to view employee types.'], 403);
        }

        $employeeTypes = EmployeeType::where('box_id', $boxUser->box_id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $employeeTypes
        ]);
    }

    public function storeEmployeeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'Box user not found'], 403);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'create', 'employee_type'))) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to create employee types.'], 403);
        }

        EmployeeType::create([
            'box_id' => $boxUser->box_id,
            'name' => $request->name,
            'status' => $request->status,
            'added_by' => $userId,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Employee type added successfully.']);
    }

    public function updateEmployeeType(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:employee_types,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'Box user not found'], 403);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'employee_type'))) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to update employee types.'], 403);
        }

        $type = EmployeeType::find($request->type_id);

        if (!$type) {
            return response()->json(['status' => 'error', 'message' => 'Employee type not found.'], 404);
        }

        $type->update([
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => $userId,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Employee type updated successfully.']);
    }

    public function deleteEmployeeType(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:employee_types,id',
        ]);

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'error', 'message' => 'Box user not found'], 403);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'delete', 'employee_type'))) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete employee types.'], 403);
        }

        $type = EmployeeType::find($request->type_id);

        if (!$type) {
            return response()->json(['status' => 'error', 'message' => 'Employee type not found.'], 404);
        }

        $type->update(['deleted_by' => $userId]);
        $type->delete();

        return response()->json(['status' => 'success', 'message' => 'Employee type deleted successfully.']);
    }

    public function work_mode_Settings()
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'work_mode'))) {
            abort(403, 'You do not have permission to access work mode settings.');
        }

        return view('settings.work_mode');
    }

    public function getStatesSettings(Request $request)
    {
        $countryId = $request->input('country_id');

        $states = State::where('country_id', $countryId)->pluck('name', 'id');
        return response()->json($states);
    }

    public function getCitiesSettings(Request $request)
    {
        $stateId = $request->input('state_id');
        $cities = City::where('state_id', $stateId)->pluck('name', 'id');
        return response()->json($cities);
    }

    public function updateCompanySettings(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'company_email' => 'nullable|email',
            'company_phone' => 'nullable|string',
            'fax_number' => 'nullable|string',
            'slug' => 'nullable|string',
            'address' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        $boxDeta = Auth::id();
        $boxUser = BoxUser::where('user_id', $boxDeta)->select('id', 'box_id')->first();

        if (!$boxUser) {
            return response()->json(['status' => 404, 'message' => 'Box user not found'], 404);
        }

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'company'))) {
            return response()->json(['status' => 403, 'message' => 'You do not have permission to update company settings.'], 403);
        }
        $box = Box::find($boxUser->box_id);

        if (!$box) {
            return response()->json(['status' => 404, 'message' => 'Box not found'], 404);
        }

        if ($request->hasFile('upload_image')) {
            // Delete old logo if exists
            if ($box->company_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($box->company_logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($box->company_logo);
            }
            $imagePath = $request->file('upload_image')->store('company_images', 'public');
            $data['company_logo'] = $imagePath;
        }
        elseif ($request->input('remove_logo') == 1) {
            // Delete old logo if exists
            if ($box->company_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($box->company_logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($box->company_logo);
            }
            $data['company_logo'] = null;
        }

        $box->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Settings updated successfully',
            'data' => [
                'box' => $box->fresh(),
                'logo_url' => $box->company_logo ? asset('storage/' . $box->company_logo) : null,
            ],
        ]);
    }

    public function storeCustomField(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'custom_type' => 'required|string',
            'col_name' => 'required|string',
            'name' => 'required|string|max:255',
            'error_message' => 'nullable|string',
            'params' => 'nullable|json',
            'ordering' => 'nullable|integer',
            'quick_edit' => 'nullable|boolean',
            'required' => 'required|boolean',
            'status' => 'required|in:Enabled,Disabled',
        ]);
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'create', 'custom_fields') || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'create', 'custom_fields'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to create custom fields.'], 403);
        }

        $optionTypes = ['select', 'radio', 'checkbox', 'multi_checkbox', 'multi_select'];
        $params = json_decode($request->params, true);
        if (in_array(strtolower($request->type), $optionTypes)) {
            if (!isset($params['options']) || empty($params['options'])) {
                return response()->json(['success' => false, 'message' => 'At least one option is required for ' . $request->type . ' fields.'], 422);
            }
        }

        if (isset($params['options']) && is_array($params['options'])) {
            $normalizedOptions = [];
            foreach ($params['options'] as $option) {
                if (is_string($option)) {
                    $normalizedOptions[] = ['value' => $option, 'label' => ucfirst($option)];
                }
                elseif (is_array($option) && isset($option['value'], $option['label'])) {
                    $normalizedOptions[] = $option;
                }
            }
            $params['options'] = $normalizedOptions;
        }

        $box = BoxUser::where('user_id', $userId)->select('box_id')->first();
        $column = $request->col_name;
        $type = strtolower($request->type);

        // Schema operations (DDL) should be done BEFORE the transaction
        // DDL operations are often auto-committed in MySQL and can't be rolled back
        try {
            // If custom_type starts with salary_, use tenant-specific salary_settings table
            if (str_starts_with($request->custom_type, 'salary_')) {
                \App\Helpers\SalarySettingHelper::ensureTableExists($box->box_id);
                \App\Helpers\SalarySettingHelper::addCustomColumn($box->box_id, $column, $type);
            }
            else {
                $tableName = 'data_' . $box->box_id;
                if (!Schema::hasTable($tableName)) {
                    Schema::create($tableName, function (Blueprint $table) use ($column, $type) {
                        $table->bigIncrements('id');
                        $table->unsignedBigInteger('lead_id')->index();
                        match ($type) {
                                'number' => $table->decimal($column, 15, 2)->nullable(),
                                'date' => $table->date($column)->nullable(),
                                default => $table->string($column)->nullable(),
                            };
                        $table->timestamps();
                    });
                }
                else {
                    if (!Schema::hasColumn($tableName, $column)) {
                        Schema::table($tableName, function (Blueprint $table) use ($column, $type) {
                            match ($type) {
                                    'number' => $table->decimal($column, 15, 2)->nullable(),
                                    'date' => $table->date($column)->nullable(),
                                    default => $table->string($column)->nullable(),
                                };
                        });
                    }
                }
            }
        }
        catch (\Exception $schemaException) {
            // If schema operation fails, return error immediately
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add custom field column: ' . $schemaException->getMessage()
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create custom fields.'
            ], 403);
        }

        // Now do data operations (DML) inside transaction
        DB::beginTransaction();
        try {
            $nextSequence = SystemField::where('box_id', $box->box_id)
                ->where('object', $request->custom_type)
                ->max('sequence');
            $nextSequence = $nextSequence ? $nextSequence + 1 : 1;
            $box_custom = BoxCustomField::create([
                'box_id' => $box->box_id,
                'type' => $request->type,
                'col_name' => $request->col_name,
                'name' => $request->name,
                'error_message' => $request->error_message,
                'readonly' => false,
                'hidden' => false,
                'object' => $request->custom_type,
                'quick_edit' => $request->quick_edit,
                'params' => $params,
                'required' => $request->required,
                'status' => $request->status,
                'added_by' => $userId,
                'last_updated_by' => $userId,
            ]);
            // SystemField::create([
            //     'box_id'             => $box->box_id,
            //     'field'              => $request->col_name,
            //     'name'               => $request->name,
            //     'object'             => $request->custom_type,
            //     'field_type'         => $request->type,
            //     'field_name'         => $request->col_name,
            //     'options'            => $params['options'] ?? [],
            //     'validation_rules'   => null,
            //     'field_refernce_key' => $box_custom->id,
            //     'sequence'           => $nextSequence,
            //     'is_visible'         => $request->status === 'Enabled',
            //     'is_required'        => $request->required,
            //     'is_editable'        => true,
            //     'is_sortable'        => false,
            //     'is_searchable'      => false,
            //     'placeholder'        => '',
            //     'updated_by'         => $userId,
            // ]);

            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Custom field created successfully.']);
            }
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create custom fields.'
            ], 403);
        }
        catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create custom fields.'
            ], 403);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'read', 'custom_fields') || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'read', 'custom_fields'))) {
            return abort(403, 'You do not have permission to access custom fields.');
        }
        $userId = Auth::id();
        $box = BoxUser::where('user_id', $userId)->select('id', 'box_id')->first();
        $query = BoxCustomField::where('box_id', $box->box_id);
        if ($request->filled('object')) {
            $query->where('object', $request->object);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('order_by')) {
            $dir = $request->get('dir', 'asc');
            $query->orderBy($request->order_by, $dir);
        }
        else {
            $query->orderBy('ordering', 'asc');
        }
        $fields = $query->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $fields,
                'count' => $fields->count(),
            ]);
        }

        return view('settings.custom-fields', compact('fields'));
    }

    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*' => 'required|integer|exists:box_custom_fields,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed'], 422);
        }

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'custom_fields') || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'custom_fields'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to reorder custom fields.'], 403);
        }

        DB::beginTransaction();
        try {
            foreach ($request->orders as $index => $id) {
                BoxCustomField::where('id', $id)->update(['ordering' => $index + 1]);
                SystemField::where('field_refernce_key', $id)->update(['sequence' => $index + 1]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update order'], 500);
        }
    }

    // .new test

    public function update(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'custom_fields') || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'custom_fields'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to update custom fields.'], 403);
        }

        $box = BoxUser::where('user_id', $userId)->select('id', 'box_id')->first();
        $id = $request->id;
        $field = BoxCustomField::findOrFail($id);
        $systemField = SystemField::where('field_refernce_key', $field->id)->first();
        $validated = $request->validate([
            'custom_type' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'default_value' => 'nullable|string',
            'required' => 'nullable|boolean',
            'quick_edit' => 'nullable|boolean',
            'status' => 'nullable|in:Enabled,Disabled',
            'params' => 'nullable|json',
        ]);

        $params = null;
        if (!empty($validated['params'])) {
            $params = json_decode($validated['params'], true);

            $optionTypes = ['select', 'radio', 'checkbox', 'multi_checkbox', 'multi_select'];
            $currentType = $validated['type'] ?? $field->type;
            if (in_array(strtolower($currentType), $optionTypes)) {
                if (!isset($params['options']) || empty($params['options'])) {
                    return response()->json(['success' => false, 'message' => 'At least one option is required for ' . $currentType . ' fields.'], 422);
                }
            }

            if (isset($params['options']) && is_array($params['options'])) {
                $normalizedOptions = [];
                foreach ($params['options'] as $option) {
                    if (is_string($option)) {
                        $normalizedOptions[] = ['value' => $option, 'label' => ucfirst($option)];
                    }
                    elseif (is_array($option) && isset($option['value'], $option['label'])) {
                        $normalizedOptions[] = $option;
                    }
                }
                $params['options'] = $normalizedOptions;
            }
        }

        $field->update([
            'object' => $validated['custom_type'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'default_value' => $validated['default_value'] ?? null,
            'required' => $validated['required'],
            'quick_edit' => $validated['quick_edit'],
            'status' => $validated['status'],
            'params' => $params,
            'updated_by' => $userId,
        ]);
        if ($systemField) {
            $systemField->update([
                'name' => $validated['name'] ?? $systemField->name,
                'field_type' => $validated['type'],
                'options' => $params['options'] ?? [],
                'updated_by' => $userId,
                // 'object' => $validated['custom_type']
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Custom field updated.']);
    }

    public function DeleteCustom(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'delete', 'custom_fields') || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'delete', 'custom_fields'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to delete custom fields.'], 403);
        }

        DB::beginTransaction();

        try {
            $id = $request->id;
            $userId = Auth::id();
            $box = BoxUser::where('user_id', $userId)->select('box_id')->first();
            $customField = BoxCustomField::findOrFail($id);
            $columnName = $customField->col_name;
            $systemField = SystemField::where('field', $columnName)->first();

            // If custom_type starts with salary_, use tenant-specific salary_settings table
            if (str_starts_with($customField->object, 'salary_')) {
                // Use tenant-specific table: salary_settings_box_{box_id}
                $tableName = \App\Helpers\SalarySettingHelper::getTableName($box->box_id);

                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $columnName)) {
                    $hasData = DB::table($tableName)
                        ->whereNotNull($columnName)
                        ->where($columnName, '!=', 0)
                        ->where($columnName, '!=', '')
                        ->limit(1)
                        ->exists();

                    if ($hasData && !$request->has('force_delete')) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Field '{$customField->name}' has existing data. Are you sure you want to permanently delete it and all its values?",
                            'has_data' => true,
                        ]);
                    }
                    \App\Helpers\SalarySettingHelper::removeCustomColumn($box->box_id, $columnName);
                }
            }
            else {
                // For other custom types, use data_{box_id} table
                $tableName = 'data_' . $box->box_id;

                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $columnName)) {
                    $hasData = DB::table($tableName)
                        ->whereNotNull($columnName)
                        ->where($columnName, '!=', 0)
                        ->where($columnName, '!=', '')
                        ->limit(1)
                        ->exists();

                    if ($hasData && !$request->has('force_delete')) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Field '{$customField->name}' has existing data. Are you sure you want to permanently delete it and all its values?",
                            'has_data' => true,
                        ]);
                    }
                    Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                        $table->dropColumn($columnName);
                    });
                }
            }
            $customField->delete();
            if ($systemField) {
                $systemField->delete();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Custom field deleted successfully.',
            ]);
        }
        catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete field.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function toggleCustomStatus(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

        if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('general_settings', 'update', 'custom_fields') || \App\Helpers\PermissionHelper::hasModulePermission('website_settings', 'update', 'custom_fields'))) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to toggle custom field status.'], 403);
        }

        $id = $request->id;
        $field = BoxCustomField::findOrFail($id);
        $newStatus = ($field->status === 'Enabled') ? 'Disabled' : 'Enabled';

        $field->update(['status' => $newStatus]);

        SystemField::where('field_refernce_key', $id)->update([
            'is_visible' => ($newStatus === 'Enabled')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Field status updated to ' . $newStatus,
            'status' => $newStatus
        ]);
    }

    public function departments()
    {
        $box = $this->box;
        if (!$box) {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid box context.');
        }

        $departments = Department::where('box_id', $box->id)->get();

        return view('settings.departments', compact('box', 'departments'));
    }

    public function designations()
    {
        $box = $this->box;
        if (!$box) {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid box context.');
        }

        $designations = Team::where('box_id', $box->id)->get();
        $departments = Department::where('box_id', $box->id)->get();

        return view('settings.designations', compact('box', 'designations', 'departments'));
    }
    public function getDepartmentsList(Request $request)
    {
        $box = $this->box;
        if (!$box) return response()->json(['error' => 'Invalid box context.'], 400);

        $query = Department::where('box_id', $box->id)->latest();

        return DataTables::of($query)
            ->addColumn('id', function ($item) {
                return $item->id;
            })
            ->addColumn('_lft', function ($item) {
                return $item->_lft;
            })
            ->addColumn('_rgt', function ($item) {
                return $item->_rgt;
            })
            ->addColumn('status_badge', function ($item) {
                return $item->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($item) {
                return '
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <div class="dropdown dropdown-action">
                        <a href="#" class="btn btn-link dropdown-toggle p-0" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item edit-department" href="javascript:void(0);" data-id="'.$item->id.'" data-name="'.$item->name.'" data-status="'.$item->status.'">
                                <i class="far fa-edit me-2"></i>Edit
                            </a>
                            <a class="dropdown-item delete-department" href="javascript:void(0);" data-id="'.$item->id.'">
                                <i class="far fa-trash-alt me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>';
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function storeDepartment(Request $request)
    {
        $box = $this->box;
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box context.']);

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1'
        ]);

        $department = new Department();
        $department->box_id = $box->id;
        $department->name = $request->name;
        $department->status = $request->status;
        $department->save();

        return response()->json(['success' => true, 'message' => 'Department created successfully.']);
    }

    public function updateDepartment(Request $request, $box_slug, $id)
    {
        $box = $this->box;
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box context.']);

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1'
        ]);

        $department = Department::where('box_id', $box->id)->findOrFail($id);
        $department->name = $request->name;
        $department->status = $request->status;
        $department->save();

        return response()->json(['success' => true, 'message' => 'Department updated successfully.']);
    }

    public function deleteDepartment($box_slug, $id)
    {
        $box = $this->box;
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box context.']);

        // Check if assigned to any user
        if (BoxUser::where('department', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete department because it is assigned to one or more users.']);
        }

        $department = Department::where('box_id', $box->id)->findOrFail($id);
        $department->delete();

        return response()->json(['success' => true, 'message' => 'Department deleted successfully.']);
    }

    public function getDesignationsList(Request $request)
    {
        $box = $this->box;
        if (!$box) return response()->json(['error' => 'Invalid box context.'], 400);

        $query = Team::with('department')->where('box_id', $box->id)->latest();

        return DataTables::of($query)
            ->addColumn('department', function ($item) {
                return $item->department ? $item->department->name : '-';
            })
            ->addColumn('_lft', function ($item) {
                return $item->_lft;
            })
            ->addColumn('_rgt', function ($item) {
                return $item->_rgt;
            })
            ->addColumn('status_badge', function ($item) {
                return $item->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($item) {
                return '
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <div class="dropdown dropdown-action">
                        <a href="#" class="btn btn-link dropdown-toggle p-0" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item edit-designation" href="javascript:void(0);" data-id="'.$item->id.'" data-name="'.$item->name.'" data-status="'.$item->status.'" data-department="'.$item->department_id.'">
                                <i class="far fa-edit me-2"></i>Edit
                            </a>
                            <a class="dropdown-item delete-designation" href="javascript:void(0);" data-id="'.$item->id.'">
                                <i class="far fa-trash-alt me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>';
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function storeDesignation(Request $request)
    {
        $box = $this->box;
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box context.']);

        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer',
            'status' => 'required|in:0,1'
        ]);

        $team = new Team();
        $team->box_id = $box->id;
        $team->name = $request->name;
        $team->department_id = $request->department_id;
        $team->status = $request->status;
        $team->save();

        return response()->json(['success' => true, 'message' => 'Designation created successfully.']);
    }

    public function updateDesignation(Request $request, $box_slug, $id)
    {
        $box = $this->box;
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box context.']);

        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer',
            'status' => 'required|in:0,1'
        ]);

        $team = Team::where('box_id', $box->id)->findOrFail($id);
        $team->name = $request->name;
        $team->department_id = $request->department_id;
        $team->status = $request->status;
        $team->save();

        return response()->json(['success' => true, 'message' => 'Designation updated successfully.']);
    }

    public function deleteDesignation($box_slug, $id)
    {
        $box = $this->box;
        if (!$box) return response()->json(['success' => false, 'message' => 'Invalid box context.']);

        // Check if assigned to any user
        if (BoxUser::where('designation', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete designation because it is assigned to one or more users.']);
        }

        $team = Team::where('box_id', $box->id)->findOrFail($id);
        $team->delete();

        return response()->json(['success' => true, 'message' => 'Designation deleted successfully.']);
    }
}
