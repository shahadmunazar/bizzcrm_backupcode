<?php

namespace App\Http\Controllers;

use App\Helpers\WhatsApp;
use App\Models\Box;
use App\Models\FacebookForm;
use App\Models\FacebookLead;
use App\Models\FacebookPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use App\Models\BoxCrmIntegration;
use App\Models\CrmIntegration;
use Yajra\DataTables\Facades\DataTables;

class FacebookController extends Controller
{
    /**
     * Facebook Integration Dashboard (Static Shell)
     */
    public function dashboard(Request $request)
    {
        $box = $this->getBox($request);
        if (!$box) {
            return redirect()->back()->with('error', 'Box not found');
        }
        $box->refresh();
        $facebookConnected = !empty($box->facebook_api_params['access_token']);

        return view('facebook.facebook_dashboard', compact('box', 'facebookConnected'));
    }

    /**
     * Get Dashboard Data (AJAX)
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        $box = $this->getBox($request);
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        // Fetch Assets
        $accounts = \App\Models\FacebookAccount::where('box_id', $box->id)->get();
        $pages = \App\Models\FacebookPage::where('box_id', $box->id)->get();
        $forms = \App\Models\FacebookForm::where('box_id', $box->id)->get();
        
        // Stats
        $totalLeads = \App\Models\FacebookLead::where('box_id', $box->id)->count();

        // Recent Leads
        $recentLeads = \App\Models\FacebookLead::where('box_id', $box->id)
            ->with(['page'])
            ->orderBy('created_time', 'desc')
            ->limit(5)
            ->get()
            ->map(function($lead) use ($forms) {
                $fieldData = $lead->data['field_data'] ?? [];
                return [
                    'name' => collect($fieldData)->firstWhere('name', 'full_name')['values'][0] ?? 'Meta Lead',
                    'email' => collect($fieldData)->firstWhere('name', 'email')['values'][0] ?? 'N/A',
                    'phone' => collect($fieldData)->firstWhere('name', 'phone_number')['values'][0] ?? 'N/A',
                    'page_name' => $lead->page->name ?? 'Unknown',
                    'form_name' => $forms->where('form_id', $lead->form_id)->first()->name ?? $lead->form_id,
                    'status' => $lead->status,
                    'time' => $lead->created_time ? $lead->created_time->diffForHumans() : '-'
                ];
            });

        // Lead Graph Data (Last 12 Months)
        $chartData = [];
        $chartLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');
            $count = \App\Models\FacebookLead::where('box_id', $box->id)
                ->whereMonth('created_time', $date->month)
                ->whereYear('created_time', $date->year)
                ->count();
            
            $chartLabels[] = $month;
            $chartData[] = $count;
        }

        return response()->json([
            'success' => true,
            'stats' => [
                'total_leads' => number_format($totalLeads),
                'total_pages' => number_format($pages->count()),
                'total_forms' => number_format($forms->count()),
                'total_accounts' => number_format($accounts->count()),
            ],
            'accounts' => $accounts,
            'pages' => $pages->map(function($p) use ($forms) {
                $p->form_count = $forms->where('facebook_page_id', $p->id)->count();
                return $p;
            }),
            'recent_leads' => $recentLeads,
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData
            ]
        ]);
    }

    /**
     * Disconnect a specific Facebook Ad Account
     */
    public function disconnectAccount(Request $request): JsonResponse
    {
        $box = $this->getBox($request);
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $id = $request->input('id');
        $account = \App\Models\FacebookAccount::where('box_id', $box->id)->find($id);

        if ($account) {
            $account->delete(); // Soft delete
            return response()->json(['success' => true, 'message' => 'Account disconnected successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Account not found'], 404);
    }

    /**
     * Get the box from route parameter or authenticated user
     */
    protected function getBox(Request $request): ?Box
    {
        // 1. Try route parameter
        $boxSlug = $request->route('box_slug');
        if ($boxSlug) {
            $box = Box::where('slug', $boxSlug)->first();
            if ($box) return $box;
        }

        // 2. Try session (standard for this app)
        if (session()->has('box')) {
            return session('box');
        }

        // 3. Fallback: get from authenticated user's direct relation
        $user = Auth::user();
        if ($user) {
            if ($user->box) return $user->box;
            
            // Try box_user relation
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            if ($boxUser) {
                return Box::find($boxUser->box_id);
            }
        }

        return null;
    }

    /**
     * List Facebook Forms
     */
    public function index(Request $request = null): JsonResponse
    {
        if (ob_get_length()) ob_clean();
        
        try {
            $request = $request ?? request();
            $box = $this->getBox($request);

            if (!$box) {
                Log::warning('FacebookController@index: Box not found', [
                    'url' => $request->fullUrl(),
                    'box_slug' => $request->route('box_slug')
                ]);
                return response()->json([
                    'draw' => intval($request->input('draw', 1)),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Company context not found.'
                ], 404);
            }

            // Stats are calculated from a fresh query to ensure accuracy
            $stats = [
                'total_forms' => FacebookForm::where('box_id', $box->id)->count(),
                'active_forms' => FacebookForm::where('box_id', $box->id)->where('status', 'ACTIVE')->count(),
                'mapped_forms' => FacebookForm::where('box_id', $box->id)->where('is_mapped', 'Yes')->count(),
                'total_leads' => FacebookLead::where('box_id', $box->id)->count(),
            ];

            Log::info('Facebook Forms DataTables Request', [
                'box_id' => $box->id,
                'forms_found' => $stats['total_forms']
            ]);

            // Query with explicit columns to avoid collisions
            $query = FacebookForm::query()
                ->leftJoin('facebook_pages', 'facebook_pages.id', '=', 'facebook_forms.facebook_page_id')
                ->where('facebook_forms.box_id', $box->id)
                ->withCount([
                    'facebookleads as processed_leads_count' => function($q) {
                        $q->where('status', 'Processed');
                    },
                    'facebookleads as pending_leads_count' => function($q) {
                        $q->where('status', 'Pending');
                    }
                ])
                ->select([
                    'facebook_forms.id as id',
                    'facebook_forms.name as name',
                    'facebook_forms.status as status',
                    'facebook_forms.form_id as form_id',
                    'facebook_forms.is_mapped as is_mapped',
                    'facebook_forms.mapping as mapping',
                    'facebook_forms.box_id as box_id',
                    'facebook_forms.updated_at as updated_at',
                    'facebook_forms.facebook_page_id as facebook_page_id',
                    'facebook_forms.total_leads as total_leads',
                    'facebook_pages.name as page_name',
                    'facebook_pages.webhook_enabled as webhook_enabled'
                ]);

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('mapped_fields', function($row) {
                    if ($row->is_mapped !== 'Yes' || empty($row->mapping)) {
                        return '<span class="text-muted italic small">Not Mapped</span>';
                    }
                    
                    $mapping = is_string($row->mapping) ? json_decode($row->mapping, true) : $row->mapping;
                    $internal = $mapping['internal'] ?? [];
                    
                    if (empty($internal)) {
                        return '<span class="text-muted italic small">No fields linked</span>';
                    }
                    
                    $html = '<div class="d-flex flex-wrap gap-1">';
                    foreach ($internal as $fbKey => $crmKey) {
                        $html .= '<span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 10px;" title="FB: '.$fbKey.'">'.$crmKey.'</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->toDateTimeString() : null;
                })
                ->addColumn('facebookleads_count', function($row) {
                    $total = $row->total_leads ?? 0;
                    $processed = $row->processed_leads_count ?? 0;
                    
                    return '<span class="fw-bold">' . $total . '</span> <span class="text-muted small">/ ' . $processed . '</span>';
                })
                ->addColumn('actions', function($row) {
                    return ''; // Handled by frontend render
                })
                ->filterColumn('form_id', function($query, $keyword) {
                    $query->where('facebook_forms.form_id', 'like', "%{$keyword}%");
                })
                ->filterColumn('name', function($query, $keyword) {
                    $query->where('facebook_forms.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('page_name', function($query, $keyword) {
                    $query->where('facebook_pages.name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['mapped_fields', 'facebookleads_count', 'actions'])
                ->with([
                    'success' => true,
                    'stats' => $stats
                ])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('Facebook DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get form details
     */
    public function show(Request $request, $box_slug, $id): JsonResponse
    {
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        $template = FacebookForm::with('page')->where('box_id', $box->id)
            ->where('id', $id)
            ->first();

        if (!$template) {
            Log::warning('Facebook form not found', [
                'box_id' => $box->id,
                'form_id' => $id,
                'available_forms' => FacebookForm::where('box_id', $box->id)->pluck('id')->toArray(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid facebook form. Form ID ' . $id . ' not found for this box.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'template' => $template,
        ]);
    }

    /**
     * Update form mapping
     */
    public function updateForm(Request $request): JsonResponse
    {
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        $template = FacebookForm::where('box_id', $box->id)
        ->where('id', $request->id)
        ->first();

        if (empty($template)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid facebook form. Form ID ' . $request->id . ' not found for this box.',
            ], 404);
        }

        $mapping = [];
        if ($template->mapping) {
            $mapping = is_array($template->mapping) ? $template->mapping : json_decode($template->mapping, true);
        }

        Log::info('Facebook mapping update attempt', [
            'box_id' => $box->id,
            'form_id' => $request->id,
            'mapping' => $request->mapping
        ]);

        if (!isset($request->mapping['internal']) || empty($request->mapping['internal'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please map at least one field before saving.',
            ], 400);
        }

        $mapping['internal'] = $request->mapping['internal'];
        $template->mapping = $mapping;
        $template->is_mapped = 'Yes';
        $template->save();

        return response()->json([
            'success' => true,
            'message' => 'Form mapping updated successfully',
        ]);
    }

    /**
     * Sync all Facebook forms from all connected pages
     */
    public function syncForms(Request $request): JsonResponse
    {
        try {
            $box = $this->getBox($request);

            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found. Please ensure you are accessing from a valid box context.',
                ], 404);
            }

            // Refresh box to ensure we have latest data
            $box->refresh();

            // Try multiple ways to get the token
            // Method 1: From model attribute
            $facebookParams = $box->facebook_api_params;

            // Method 2: Fresh model instance (using model, not raw DB)
            $freshBox = Box::find($box->id);
            $freshParams = $freshBox ? $freshBox->facebook_api_params : null;

            // Method 3: Another fresh instance with refresh
            $anotherBox = Box::find($box->id);
            if ($anotherBox) {
                $anotherBox->refresh();
                $anotherParams = $anotherBox->facebook_api_params;
            } else {
                $anotherParams = null;
            }

            // Log for debugging
            Log::info('Sync Forms called', [
                'box_id' => $box->id ?? 'not set',
                'box_slug' => $request->route('box_slug') ?? 'not set',
                'from_model_attribute' => $facebookParams,
                'from_fresh_model' => $freshParams,
                'from_another_model' => $anotherParams,
                'model_type' => gettype($facebookParams),
            ]);

            // Use the first available non-null params
            $params = $facebookParams ?? $freshParams ?? $anotherParams ?? null;

            // Check if Facebook is connected
            $hasParams = !empty($params) && is_array($params);
            $hasTokenKey = $hasParams && isset($params['access_token']);
            $tokenValue = $hasTokenKey ? $params['access_token'] : null;
            $tokenEmpty = empty($tokenValue);

            if (!$hasParams || !$hasTokenKey || $tokenEmpty) {
                // Get another fresh instance using model
                $debugBox = Box::find($box->id);
                $debugParams = $debugBox ? $debugBox->facebook_api_params : null;

                Log::warning('Facebook not connected - all methods checked', [
                    'box_id' => $box->id,
                    'from_model_attribute' => [
                        'has_params' => !empty($facebookParams),
                        'has_token' => !empty($facebookParams) && isset($facebookParams['access_token']),
                    ],
                    'from_fresh_model' => [
                        'has_params' => !empty($freshParams),
                        'has_token' => !empty($freshParams) && isset($freshParams['access_token']),
                    ],
                    'from_another_model' => [
                        'has_params' => !empty($anotherParams),
                        'has_token' => !empty($anotherParams) && isset($anotherParams['access_token']),
                    ],
                    'debug_model_value' => $debugParams,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Facebook not connected. Please connect Facebook first from the Integrations page. Click "Configure" on the Facebook Connect section.',
                    'debug' => [
                        'box_id' => $box->id,
                        'box_slug' => $box->slug,
                        'has_facebook_api_params' => $hasParams,
                        'has_access_token_key' => $hasTokenKey,
                        'access_token_empty' => $tokenEmpty,
                        'suggestion' => 'Please go to Settings > Integrations and click "Configure" on Facebook Connect to connect your account.',
                        'check_database' => 'Check if facebook_api_params column exists and has data for box_id: ' . $box->id,
                    ],
                ], 400);
            }

            // If we got params from alternative source, update the current box instance
            if ($params !== $facebookParams) {
                $box->facebook_api_params = $params;
                $source = 'unknown';
                if ($params === $freshParams) {
                    $source = 'fresh_model';
                } elseif ($params === $anotherParams) {
                    $source = 'another_model';
                }
                Log::info('Updated box instance with params from alternative source', [
                    'box_id' => $box->id,
                'source' => $source,
                ]);
            }

            $whatsapp = new WhatsApp($box);
            $result = $whatsapp->getPages($box);

            if ($result && isset($result['success']) && $result['success']) {
                // Count forms after sync - filtered by box_id
                $formsCount = FacebookForm::where('box_id', $box->id)->count();
                $pagesCount = FacebookPage::where('box_id', $box->id)->count();

                Log::info('Sync completed successfully', [
                    'box_id' => $box->id,
                    'pages_count' => $pagesCount,
                    'forms_count' => $formsCount,
                ]);

                // Recalculate lead counts after syncing forms
                $this->recalculateFormLeadCounts($box);

                return response()->json([
                    'success' => true,
                    'message' => 'Forms synced successfully',
                    'data' => [
                        'box_id' => $box->id,
                        'pages_count' => $pagesCount,
                        'forms_count' => $formsCount,
                    ],
                ]);
            } else {
                Log::error('Sync failed', [
                    'box_id' => $box->id,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to sync forms from Facebook',
                ], 400);
            }
        } catch (\Exception $e) {
            $boxId = isset($box) && $box ? $box->id : 'not set';
            Log::error('Sync exception', [
                'box_id' => $boxId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error syncing forms: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync leads from all connected Facebook forms
     */
    public function syncLeads(Request $request): JsonResponse
    {
        try {
            $box = $this->getBox($request);

            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            $box->refresh();
            $params = $box->facebook_api_params;

            if (empty($params) || !isset($params['access_token'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facebook not connected.',
                ], 400);
            }
            
            /*
            // Check if there are any mapped forms
            $mappedFormsCount = FacebookForm::where('box_id', $box->id)->where('is_mapped', 'Yes')->count();
            if ($mappedFormsCount === 0) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'no_mapping',
                    'message' => "You haven't mapped any Facebook forms yet. Please click the 'Map Field' (link icon) for at least one form in the table below to enable lead syncing.",
                ], 400);
            }
            */

            // Dispatch the job to the queue
            \App\Jobs\SyncFacebookLeadsJob::dispatch($box);

            return response()->json([
                'success' => true,
                'message' => 'Leads sync has been started in the background. It may take a few minutes to complete.',
            ]);

        } catch (\Exception $e) {
            Log::error('Leads sync dispatch exception', [
                'box_id' => isset($box) ? $box->id : 'not set',
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while starting leads sync: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display Facebook leads page
     */
    public function facebookLeads(Request $request)
    {
        $box = $this->getBox($request);
        if (!$box) {
            return redirect()->back()->with('error', 'Box not found');
        }

        return view('settings.facebook-leads', compact('box'));
    }

    /**
     * Get Facebook leads for DataTable
     */
    public function facebookLeadsList(Request $request): JsonResponse
    {
        try {
            $box = $this->getBox($request);
            if (!$box) {
                return response()->json(['error' => 'Box not found'], 404);
            }

            $stats = [
                'total_leads' => FacebookLead::where('box_id', $box->id)->count(),
                'processed_leads' => FacebookLead::where('box_id', $box->id)->where('status', 'Processed')->count(),
                'pending_leads' => FacebookLead::where('box_id', $box->id)->where('status', 'Pending')->count(),
            ];

            $query = FacebookLead::where('box_id', $box->id)
                ->with(['page'])
                ->orderBy('created_time', 'desc');

            $query->getQuery()->orders = null;

            return DataTables::of($query)
                ->addColumn('select', function ($lead) {
                    return '<input type="checkbox" class="form-check-input lead-checkbox" value="' . $lead->id . '">';
                })
                ->addColumn('form_name', function ($lead) use ($box) {
                    $form = FacebookForm::where('form_id', $lead->form_id)
                        ->where('box_id', $box->id)
                        ->first();
                    return $form ? $form->name : $lead->form_id;
                })
                ->editColumn('status', function ($lead) {
                    $class = $lead->status === 'Processed' ? 'badge bg-success' : 'badge bg-warning text-dark';
                    return '<span class="' . $class . '">' . $lead->status . '</span>';
                })
                ->addColumn('lead_details', function ($lead) {
                    $data = $lead->data;
                    if (isset($data['field_data'])) {
                        $details = '';
                        foreach ($data['field_data'] as $field) {
                            $val = $field['values'][0] ?? '-';
                            $details .= '<strong>' . htmlspecialchars($field['name']) . ':</strong> ' . htmlspecialchars($val) . '<br>';
                        }
                        return $details;
                    }
                    return '-';
                })
                ->editColumn('created_time', function ($lead) {
                    return $lead->created_time ? $lead->created_time->format('d-M-Y H:i') : '-';
                })
                ->rawColumns(['select', 'status', 'lead_details'])
                ->with([
                    'success' => true,
                    'stats' => $stats
                ])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('facebookLeadsList error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Connect webhook for a form/page
     */
    public function connectWebhook(Request $request, $box_slug, $id): JsonResponse
    {
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        $form = FacebookForm::with('page')
            ->where('box_id', $box->id)
            ->find($id);

        if (empty($form) || !$form->page) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request or page not found',
            ], 404);
        }

        $whatsapp = new WhatsApp($box);
        $result = $whatsapp->registerApiWebhook($form->page);

        if ($result['success']) {
            // Refresh the page model to get updated webhook status
            $form->page->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Webhook connected successfully',
                'data' => [
                    'webhook_enabled' => $form->page->webhook_enabled,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to connect webhook',
                'data' => [
                    'webhook_enabled' => $form->page->webhook_enabled,
                ],
            ], 400);
        }
    }

    /**
     * Check webhook status for a specific page
     */
    public function checkWebhookStatus(Request $request, $box_slug, $id): JsonResponse
    {
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        $page = FacebookPage::where('box_id', $box->id)->find($id);

        if (empty($page)) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        $whatsapp = new WhatsApp($box);
        $result = $whatsapp->checkWebhookStatus($page);

        // Refresh the page model to get updated webhook status
        $page->refresh();

        return response()->json([
            'success' => $result['success'],
            'subscribed' => $result['subscribed'] ?? false,
            'webhook_enabled' => $page->webhook_enabled,
            'message' => $result['subscribed'] ? 'Webhook is connected' : ($result['error'] ?? 'Webhook is not connected'),
            'data' => $result['data'] ?? [],
        ]);
    }

    /**
     * Check webhook status for all pages
     */
    public function checkAllWebhooks(Request $request): JsonResponse
    {
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        $pages = FacebookPage::where('box_id', $box->id)->get();
        $results = [];
        $whatsapp = new WhatsApp($box);

        foreach ($pages as $page) {
            $result = $whatsapp->checkWebhookStatus($page);
            $page->refresh();

            $results[] = [
                'page_id' => $page->id,
                'page_name' => $page->name,
                'webhook_enabled' => $page->webhook_enabled,
                'subscribed' => $result['subscribed'] ?? false,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook status checked for all pages',
            'data' => $results,
        ]);
    }

    /**
     * Check Facebook connection status
     */
    public function checkConnectionStatus(Request $request): JsonResponse
    {
        Log::info('checkConnectionStatus called');
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        // Refresh box to ensure we have latest data
        $box->refresh();

        $hasParams = !empty($box->facebook_api_params);
        $hasTokenInParams = $hasParams && isset($box->facebook_api_params['access_token']) && !empty($box->facebook_api_params['access_token']);
        $hasTokenInColumn = !empty($box->facebook_access_token);
        
        $isConnected = $hasTokenInParams || $hasTokenInColumn;
        
        $tokenValue = $hasTokenInParams ? $box->facebook_api_params['access_token'] : $box->facebook_access_token;

        Log::info('Checking Facebook connection status', [
            'box_id' => $box->id,
            'has_params' => $hasParams,
            'has_token_in_params' => $hasTokenInParams,
            'has_token_in_column' => $hasTokenInColumn,
            'is_connected' => $isConnected,
            'token_length' => $tokenValue ? strlen($tokenValue) : 0,
        ]);

        return response()->json([
            'success' => true,
            'connected' => $isConnected,
            'message' => $isConnected ? 'Facebook is connected' : 'Facebook is not connected',
            'data' => [
                'box_id' => $box->id,
                'box_slug' => $box->slug,
                'has_facebook_api_params' => $hasParams,
                'has_access_token' => $isConnected,
                'token_length' => $tokenValue ? strlen($tokenValue) : 0,
            ],
        ]);
    }

    /**
     * Get saved Facebook Business Integration details (from database)
     */
    public function getSavedIntegrationDetails(Request $request): JsonResponse
    {
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        // Refresh box to ensure we have latest data
        $box->refresh();

        $integration = $box->facebook_api_params['business_integration'] ?? null;
        $accessToken = $box->facebook_api_params['access_token'] ?? null;

        return response()->json([
            'success' => true,
            'connected' => !empty($accessToken),
            'has_integration_details' => !empty($integration),
            'data' => [
                'access_token' => $accessToken ? '***' . substr($accessToken, -4) : null, // Show only last 4 chars for security
                'business_integration' => $integration,
                'connected_at' => $box->facebook_api_params['connected_at'] ?? null,
                'integration_updated_at' => $box->facebook_api_params['business_integration_updated_at'] ?? null,
            ],
        ]);
    }

    /**
     * Get and save Facebook Business Integration details
     */
    public function getBusinessIntegration(Request $request): JsonResponse
    {
        $box = $this->getBox($request);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Box not found',
            ], 404);
        }

        // Refresh box to ensure we have latest data
        $box->refresh();

        if (empty($box->facebook_api_params) || !isset($box->facebook_api_params['access_token'])) {
            return response()->json([
                'success' => false,
                'message' => 'Facebook not connected. Please connect Facebook first.',
            ], 400);
        }

        $whatsapp = new WhatsApp($box);
        $result = $whatsapp->getBusinessIntegration($box);

        if ($result['success']) {
            // Refresh box to get updated data
            $box->refresh();

            $integration = $box->facebook_api_params['business_integration'] ?? null;

            return response()->json([
                'success' => true,
                'message' => 'Business integration details saved successfully',
                'data' => [
                    'integration' => $result['data'],
                    'saved_details' => [
                        'id' => $integration['id'] ?? null,
                        'name' => $integration['name'] ?? 'BizzCRM',
                        'app_id' => $integration['app_id'] ?? null,
                        'created_time' => $integration['created_time'] ?? null,
                        'updated_at' => $box->facebook_api_params['business_integration_updated_at'] ?? null,
                    ],
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to fetch business integration details',
            ], 400);
        }
    }

    /**
     * Webhook verification (GET)
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

        // Log to file
        error_log(
            date("Y-m-d H:i:s") . " Webhook Validation: " . json_encode($logData) . "\n",
            3,
            storage_path() . "/logs/facebook_validator.log"
        );

        // Log to Laravel log
        Log::info('🔔 Facebook Webhook Validation Request', $logData);

        // Console output (for debugging)
        echo "\n🔔 Facebook Webhook Validation Request:\n";
        echo "   Timestamp: " . $logData['timestamp'] . "\n";
        echo "   URL: " . $logData['url'] . "\n";
        echo "   IP: " . $logData['ip'] . "\n";
        echo "   Query Params: " . json_encode($logData['query_params'], JSON_PRETTY_PRINT) . "\n\n";

        if (isset($request->hub_mode) && $request->hub_mode == "subscribe") {
            $verifyToken = $request->hub_verify_token;
            $expectedToken = "BizzTest5161";

            Log::info('Webhook Verification Check', [
                'hub_mode' => $request->hub_mode,
                'verify_token_received' => $verifyToken,
                'expected_token' => $expectedToken,
                'token_match' => $verifyToken === $expectedToken,
            ]);

            echo "   Hub Mode: " . $request->hub_mode . "\n";
            echo "   Verify Token Received: " . ($verifyToken ?? 'not set') . "\n";
            echo "   Expected Token: " . $expectedToken . "\n";
            echo "   Token Match: " . ($verifyToken === $expectedToken ? 'YES ✅' : 'NO ❌') . "\n\n";

            if ($verifyToken === $expectedToken) {
                $challenge = $request->hub_challenge;
                Log::info('✅ Webhook Verification SUCCESS', [
                    'challenge' => $challenge,
                    'hub_mode' => $request->hub_mode,
                ]);

                // Console output
                echo "✅ Facebook Webhook Verified Successfully!\n";
                echo "   Challenge: " . $challenge . "\n\n";

                return $challenge;
            } else {
                Log::warning('❌ Webhook Verification FAILED - Token Mismatch', [
                    'received_token' => $verifyToken,
                    'expected_token' => $expectedToken,
                ]);

                // Console output
                echo "❌ Facebook Webhook Verification Failed - Invalid Token\n\n";
            }
        } else {
            Log::warning('❌ Webhook Validation Failed - Invalid Request', [
                'hub_mode' => $request->hub_mode ?? 'not set',
                'has_verify_token' => isset($request->hub_verify_token),
            ]);

            // Console output
            echo "❌ Facebook Webhook Validation Failed - Invalid Request\n";
            echo "   Hub Mode: " . ($request->hub_mode ?? 'not set') . "\n\n";
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid request',
        ], 403);
    }

    /**
     * Webhook handler (POST)
     */
    public function webhook(Request $request)
    {
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

        // Log to file
        error_log(
            date("Y-m-d H:i:s") . " Webhook POST: " . json_encode($logData) . "\n",
            3,
            storage_path() . "/logs/facebook_webhooknew.log"
        );

        // Log to Laravel log
        Log::info('🔔 Facebook Webhook POST Request', $logData);

        // Console output
        echo "\n🔔 Facebook Webhook Received:\n";
        echo "   Timestamp: " . $logData['timestamp'] . "\n";
        echo "   Object: " . $logData['object_type'] . "\n";
        echo "   Entry Count: " . $logData['entry_count'] . "\n";
        echo "   IP: " . $logData['ip'] . "\n\n";

        if (isset($data['object'])) {
            if ($data['object'] == "page" && isset($data['entry'])) {
                $totalNewLeadsFound = 0;
                $processedLeads = 0;
                $skippedUnmapped = 0;
                $errors = [];

                foreach ($data['entry'] as $entryIndex => $entry) {
                    Log::info("Processing webhook entry #{$entryIndex}", [
                        'entry_id' => $entry['id'] ?? 'unknown',
                        'time' => $entry['time'] ?? null,
                        'has_changes' => isset($entry['changes']),
                        'changes_count' => isset($entry['changes']) ? count($entry['changes']) : 0,
                    ]);

                    echo "   Processing Entry #{$entryIndex}\n";
                    echo "      Entry ID: " . ($entry['id'] ?? 'unknown') . "\n";
                    echo "      Changes Count: " . (isset($entry['changes']) ? count($entry['changes']) : 0) . "\n";

                    if (isset($entry['changes'])) {
                        foreach ($entry['changes'] as $changeIndex => $change) {
                            Log::info("Processing change #{$changeIndex}", [
                                'field' => $change['field'] ?? 'unknown',
                                'value_keys' => isset($change['value']) ? array_keys($change['value']) : [],
                            ]);

                            echo "      Change #{$changeIndex}: Field = " . ($change['field'] ?? 'unknown') . "\n";

                            if ($change['field'] == "leadgen") {
                                $totalNewLeadsFound++;
                                $values = $change['value'];

                                Log::info('🔔 Leadgen Webhook Event', [
                                    'page_id' => $values['page_id'] ?? 'unknown',
                                    'form_id' => $values['form_id'] ?? 'unknown',
                                    'leadgen_id' => $values['leadgen_id'] ?? 'unknown',
                                    'ad_id' => $values['ad_id'] ?? null,
                                    'adgroup_id' => $values['adgroup_id'] ?? null,
                                    'created_time' => $values['created_time'] ?? null,
                                ]);

                                echo "      📥 New Facebook Lead Received:\n";
                                echo "         Page ID: " . ($values['page_id'] ?? 'unknown') . "\n";
                                echo "         Form ID: " . ($values['form_id'] ?? 'unknown') . "\n";
                                echo "         Leadgen ID: " . ($values['leadgen_id'] ?? 'unknown') . "\n";

                                $page = FacebookPage::with('box')
                                    ->where('page_id', $values['page_id'])
                                    ->first();

                                if ($page) {
                                    Log::info('✅ Found Facebook Page in database', [
                                        'page_id' => $page->id,
                                        'facebook_page_id' => $page->page_id,
                                        'page_name' => $page->name,
                                        'box_id' => $page->box_id,
                                        'box_slug' => $page->box->slug ?? 'unknown',
                                    ]);

                                    echo "         ✅ Found Page: " . ($page->name ?? 'N/A') . " (Box: " . ($page->box->slug ?? 'N/A') . ")\n";

                                    try {
                                        // Check if lead already exists
                                        $existingLead = FacebookLead::where('leadgen_id', $values['leadgen_id'])->first();

                                        if ($existingLead) {
                                            Log::warning('⚠️ Lead already exists, skipping', [
                                                'leadgen_id' => $values['leadgen_id'],
                                                'existing_lead_id' => $existingLead->id,
                                            ]);
                                            echo "         ⚠️ Lead already exists, skipping\n";
                                            continue;
                                        }

                                        $lead = new FacebookLead;
                                        $lead->box_id = $page->box_id;
                                        $lead->facebook_page_id = $page->id;
                                        $lead->adgroup_id = $values['adgroup_id'] ?? null;
                                        $lead->ad_id = $values['ad_id'] ?? null;
                                        $lead->created_time = isset($values['created_time']) ? date('Y-m-d H:i:s', strtotime($values['created_time'])) : null;
                                        $lead->leadgen_id = $values['leadgen_id'] ?? null;
                                        $lead->page_id = $values['page_id'] ?? null;
                                        $lead->form_id = $values['form_id'] ?? null;
                                        $lead->status = 'Pending';

                                        $saved = $lead->save();

                                        if ($saved) {
                                            Log::info('✅ Lead saved to database', [
                                                'lead_id' => $lead->id,
                                                'leadgen_id' => $lead->leadgen_id,
                                                'box_id' => $lead->box_id,
                                            ]);

                                            echo "         ✅ Lead saved to database (ID: {$lead->id})\n";

                                            // Fetch full lead data
                                            try {
                                                $whatsapp = new WhatsApp($page->box);
                                                $lead_data = $whatsapp->getLead($page->box, $lead->leadgen_id);

                                                if ($lead_data) {
                                                    $lead->data = $lead_data;
                                                    $lead->save();

                                                    Log::info('✅ Lead data fetched and saved', [
                                                        'lead_id' => $lead->id,
                                                        'data_fields_count' => is_array($lead_data) ? count($lead_data['field_data'] ?? []) : 0,
                                                    ]);

                                                    echo "         ✅ Lead data fetched successfully\n";

                                                    // Real-time import to CRM leads table if form is mapped
                                                    $form = FacebookForm::where('form_id', $lead->form_id)
                                                        ->where('box_id', $page->box_id)
                                                        ->first();
                                                    if ($form && $form->is_mapped === 'Yes') {
                                                        $mapping = $form->mapping;
                                                        $internalMapping = $mapping['internal'] ?? [];

                                                        if (!empty($internalMapping)) {
                                                            $field_data = $lead_data['field_data'] ?? [];
                                                            $flatData = [];
                                                            foreach ($field_data as $fd) {
                                                                $flatData[$fd['name']] = $fd['values'][0] ?? null;
                                                            }

                                                            // Prepare mapper for saveLead: fb_field_key => crm_field_key
                                                            $mapper = $internalMapping;

                                                            // Enrich flatData for custom fields and standard fields
                                                            foreach ($internalMapping as $fbKey => $crmKey) {
                                                                if ($fbKey && $crmKey && !isset($flatData[$crmKey])) {
                                                                    $flatData[$crmKey] = $flatData[$fbKey] ?? null;
                                                                }
                                                            }

                                                            $leadsController = new \App\Http\Controllers\Leads\LeadsController();
                                                            $filterData = [
                                                                'lead_source_id' => $page->box->facebook_api_params['lead_source_id'] ?? null,
                                                            ];
                                                            $submit_time = date("Y-m-d H:i:s", strtotime($lead->created_time));

                                                            $crmLead = $leadsController->saveLead(
                                                                $page->box,
                                                                $flatData,
                                                                $filterData,
                                                                $mapper,
                                                                $submit_time,
                                                                $page->box->superadmin_id,
                                                                'Facebook Webhook'
                                                            );

                                                            if ($crmLead) {
                                                                $lead->status = 'Processed';
                                                                $lead->save();
                                                                $processedLeads++;
                                                                Log::info('✅ Lead imported to CRM leads table', ['crm_lead_id' => $crmLead->id]);
                                                                echo "         ✅ Lead imported to CRM (ID: {$crmLead->id})\n";
                                                            }
                                                        } else {
                                                            $skippedUnmapped++;
                                                        }
                                                    }
                                                } else {
                                                    Log::warning('⚠️ Could not fetch lead data', [
                                                        'lead_id' => $lead->id,
                                                        'leadgen_id' => $lead->leadgen_id,
                                                    ]);
                                                    echo "         ⚠️ Could not fetch lead data\n";
                                                }
                                            } catch (\Exception $e) {
                                                Log::error('❌ Error fetching lead data', [
                                                    'lead_id' => $lead->id,
                                                    'leadgen_id' => $lead->leadgen_id,
                                                    'error' => $e->getMessage(),
                                                ]);
                                                echo "         ❌ Error fetching lead data: " . $e->getMessage() . "\n";
                                                $errors[] = 'Error fetching lead data: ' . $e->getMessage();
                                            }
                                        } else {
                                            Log::error('❌ Failed to save lead to database', [
                                                'leadgen_id' => $values['leadgen_id'],
                                            ]);
                                            echo "         ❌ Failed to save lead to database\n";
                                            $errors[] = 'Failed to save lead';
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('❌ Error processing lead', [
                                            'leadgen_id' => $values['leadgen_id'] ?? 'unknown',
                                            'error' => $e->getMessage(),
                                            'trace' => $e->getTraceAsString(),
                                        ]);
                                        echo "         ❌ Error processing lead: " . $e->getMessage() . "\n";
                                        $errors[] = 'Error processing lead: ' . $e->getMessage();
                                    }
                                } else {
                                    Log::warning('⚠️ Facebook Page not found in database', [
                                        'page_id' => $values['page_id'] ?? 'unknown',
                                    ]);
                                    echo "         ⚠️ Facebook Page not found in database\n";
                                }
                            }
                        }
                    }
                }

                $summary = [
                    'leads_processed' => $leadsProcessed,
                    'leads_created' => $leadsCreated,
                    'errors' => $errors,
                ];

                Log::info('✅ Webhook Processing Complete', $summary);
                echo "\n✅ Webhook Processing Complete:\n";
                echo "   Leads Processed: {$leadsProcessed}\n";
                echo "   Leads Created: {$leadsCreated}\n";
                if (!empty($errors)) {
                    echo "   Errors: " . count($errors) . "\n";
                    foreach ($errors as $error) {
                        echo "      - {$error}\n";
                    }
                }
                echo "\n";

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processed successfully',
                    'data' => $summary,
                ]);
            } else {
                Log::warning('⚠️ Webhook object is not "page" or missing entry', [
                    'object' => $data['object'] ?? 'unknown',
                    'has_entry' => isset($data['entry']),
                ]);
                echo "   ⚠️ Webhook object is not 'page' or missing entry\n";
            }
        } else {
            Log::warning('⚠️ Webhook missing object field', [
                'data_keys' => array_keys($data ?? []),
            ]);
            echo "   ⚠️ Webhook missing object field\n";
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid request',
        ], 400);
    }

    /**
     * Redirect to Facebook OAuth
     */
    public function connect(Request $request, $boxSlug = null)
    {
        Log::info('FacebookController::connect called', [
            'params' => $request->all(),
            'route_slug' => $boxSlug,
            'url' => $request->fullUrl()
        ]);

        $appId = config('services.facebook.client_id');
        $type = $request->input('type', 'facebook');
        
        // BACKEND FIX: Always force the whitelisted redirect URI regardless of what the frontend sends
        $redirectUri = ($type === 'whatsapp') ? config('services.whatsapp.redirect') : config('services.facebook.redirect');
        
        if (!$redirectUri) {
            $host = $request->getHost();
            $protocol = $request->isSecure() ? 'https://' : 'http://';
            $redirectUri = $protocol . $host . '/auth/facebook/callback';
        }
        
        $type = $request->input('type', 'facebook');
        
        if ($type === 'whatsapp') {
            $scope = 'whatsapp_business_management,whatsapp_business_messaging';
        } else {
            $scope = 'public_profile,email,pages_show_list,pages_read_engagement,leads_retrieval';
        }
        
        // Get current URL for state
        $currentUrl = $request->input('current_url', url('/'));
        
        // Use route param if available, otherwise try input
        if (!$boxSlug) {
            $boxSlug = $request->input('box_slug', '');
        }
        
        // Build state with display=popup and box_slug and type
        $state = $currentUrl . '&display=popup&box_slug=' . $boxSlug . '&type=' . $type;

        // Build OAuth URL parameters
        $params = [
            'client_id'     => $appId,
            'redirect_uri'  => $redirectUri,
            'scope'         => $scope,
            'response_type' => 'code',
            'auth_type'     => 'reauthenticate', // Force re-authentication to allow switching accounts
            'display'       => 'popup',
            'state'         => $state,
        ];

        if ($type === 'whatsapp') {
            $configId = config('services.whatsapp.config_id');
            
            if ($configId) {
                $extras = [
                    'featureType' => 'whatsapp_business_app_onboarding',
                    'sessionInfoVersion' => '3',
                    'version' => 'v4',
                    'features' => [['name' => 'app_only_install']],
                    'config_id' => $configId,
                ];
                
                if (config('services.whatsapp.business_id')) {
                    $extras['setup'] = ['business_id' => config('services.whatsapp.business_id')];
                }
                
                $params['extras'] = json_encode($extras);
            }
        }

        // Build full URL - use v22.0 to match API calls
        $url = "https://www.facebook.com/v22.0/dialog/oauth?" . http_build_query($params);

        return redirect($url);
    }

    /**
     * Recalculate lead counts for all forms in a box
     */
    public function recalculateFormLeadCounts($box)
    {
        try {
            $forms = FacebookForm::where('box_id', $box->id)->get();
            $updatedCount = 0;

            foreach ($forms as $form) {
                $totalLeads = \App\Models\FacebookLead::where('form_id', $form->form_id)
                    ->where('box_id', $box->id)
                    ->count();
                
                if ($form->total_leads != $totalLeads) {
                    $form->update(['total_leads' => $totalLeads]);
                    $updatedCount++;
                }
            }

            Log::info("Recalculated lead counts for box {$box->id}. Updated {$updatedCount} forms.");
            return true;
        } catch (\Exception $e) {
            Log::error("Error recalculating lead counts: " . $e->getMessage());
            return false;
        }
    }

    /**
     * AJAX handler for recalculating counts
     */
    public function recalculateCounts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $box = $this->getBox($request);
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $result = $this->recalculateFormLeadCounts($box);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'All lead counts have been successfully recalculated.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to recalculate some counts.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete Facebook leads
     */
    public function deleteLeads(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $box = $this->getBox($request);
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $ids = $request->input('ids');
            if (empty($ids) || !is_array($ids)) {
                return response()->json(['success' => false, 'message' => 'No leads selected'], 400);
            }

            \App\Models\FacebookLead::where('box_id', $box->id)
                ->whereIn('id', $ids)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' leads deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting Facebook leads: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disconnect Facebook Integration
     */
    public function disconnect(Request $request)
    {
        try {
            $box = $this->getBox($request);
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            Log::info("🛡️ [Facebook] Disconnect Triggered for Box ID: {$box->id}");

            // 0. Revoke Facebook Token via Graph API
            $hasParams = !empty($box->facebook_api_params);
            $hasTokenInParams = $hasParams && isset($box->facebook_api_params['access_token']) && !empty($box->facebook_api_params['access_token']);
            $accessToken = $hasTokenInParams ? $box->facebook_api_params['access_token'] : $box->facebook_access_token;

            if ($accessToken) {
                try {
                    // Use DELETE on /me/permissions to revoke the app's access
                    $response = Http::delete("https://graph.facebook.com/v22.0/me/permissions", [
                        'access_token' => $accessToken
                    ]);
                    
                    if ($response->successful()) {
                        Log::info("✅ [Facebook] Token revoked successfully for Box ID: {$box->id}");
                    } else {
                        Log::warning("⚠️ [Facebook] Token revocation returned non-success: " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("❌ [Facebook] Error revoking token: " . $e->getMessage());
                }
            }

            // 1. Clear Facebook specific credentials in the Box model
            $box->facebook_access_token = null;
            $box->facebook_api_params = null;
            $box->save();

            // 2. Remove related Facebook data with audit tracking
            FacebookLead::where('box_id', $box->id)->update(['deleted_by' => auth()->id()]);
            FacebookLead::where('box_id', $box->id)->delete();

            FacebookForm::where('box_id', $box->id)->update(['deleted_by' => auth()->id()]);
            FacebookForm::where('box_id', $box->id)->delete();

            FacebookPage::where('box_id', $box->id)->update(['deleted_by' => auth()->id()]);
            FacebookPage::where('box_id', $box->id)->delete();
            
            \App\Models\FacebookAccount::where('box_id', $box->id)->update(['deleted_by' => auth()->id()]);
            \App\Models\FacebookAccount::where('box_id', $box->id)->delete();
            
            // 3. Update BoxCrmIntegration record instead of deleting it
            $facebookIntegration = CrmIntegration::where('system_name', 'facebook')->first();
            if ($facebookIntegration) {
                BoxCrmIntegration::where('box_id', $box->id)
                    ->where('lead_api_id', $facebookIntegration->id)
                    ->update([
                        'status' => 'Disabled',
                        'connection_status' => 'Disconnected',
                        'token' => null,
                        'params' => null,
                        'error_log' => null,
                        'last_checked_date' => null
                    ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facebook integration disconnected successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Facebook integration disconnected successfully.');
        } catch (\Exception $e) {
            Log::error("❌ [Facebook] Disconnect failed: " . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error disconnecting Facebook: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error disconnecting Facebook: ' . $e->getMessage());
        }
    }
}
