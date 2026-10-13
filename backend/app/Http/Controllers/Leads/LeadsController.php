<?php
namespace App\Http\Controllers\Leads;
use App\Exports\LeadsExport;
use App\Http\Controllers\Controller;
use App\Jobs\ExportLeadsJob;
use App\Models\{Activity, Box, BoxCustomField, BoxUser, Call, City, ContactStage, Country, CrmSource, DivisionArea, Industry, Lead, LeadAssignmentHistory, LeadSubStatus, MyReport, State, SystemField, TableColumnPreference, User};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth, DB, Log, Schema, Storage, Validator};
use Yajra\DataTables\Facades\DataTables;

class LeadsController extends Controller
{

    public function getWorkingLocationsList(Request $request)
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['data' => []]);
        }
        $boxId = $boxUser->box_id;

        $query = DivisionArea::where('box_id', $boxId)
            ->with([
                'division.company:id,bill_name',
                'state:id,name',
                'city:id,name',
            ])
            ->select('division_areas.*'); // Ensure primary key and fields are selected

        return DataTables::of($query)
            ->addColumn('added_on', function ($loc) {
                return $loc->created_at ? $loc->created_at->format('d-M-Y') : null;
            })
            ->editColumn('status', function ($loc) {
                return $loc->status;
            })
            ->addColumn('city', function ($loc) {
                return $loc->city->name ?? '';
            })
            ->addColumn('state', function ($loc) {
                return $loc->state->name ?? '';
            })
            ->addColumn('party_name', function ($loc) {
                $lead = $loc->lead;
                if ($lead) {
                    if ($lead->trashed()) {
                        return '<span class="text-danger fw-bold">Deleted</span>';
                    }
                    $name = trim($lead->name . ' ' . $lead->last_name);
                    $url = route('LeadsDetailsPage', ['box_slug' => request()->route('box_slug'), 'lead_id' => $lead->id]);
                    return '<a href="' . $url . '" class="text-primary fw-bold text-decoration-underline">' . htmlspecialchars($name) . '</a>';
                }
                return '<span class="text-danger fw-bold">Deleted</span>';
            })
            ->addColumn('company', function ($loc) {
                return $loc->division && $loc->division->company ? $loc->division->company->bill_name : '';
            })
            ->addColumn('assigned', function ($loc) {
                $lead = $loc->lead;
                $assignedUser = $lead ? \App\Models\User::find($lead->assigned_to) : null;
                return $assignedUser ? $assignedUser->name : 'Unassigned';
            })
            ->addColumn('member_since', function ($loc) {
                $lead = $loc->lead;
                return $lead && $lead->created_at ? $lead->created_at->format('d-M-Y') : '--';
            })
            ->addColumn('matureddate', function ($loc) {
                $lead = $loc->lead;
                return $lead && $lead->matured_date ? \Illuminate\Support\Carbon::parse($lead->matured_date)->format('d-M-Y') : '--';
            })
            ->addColumn('action', function ($loc) {
                return '<div class="action-icon d-inline-flex">
                    <a href="#" class="delete-working-location-btn" data-id="' . $loc->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                        <i class="ti ti-trash"></i>
                    </a>
                </div>';
            })
            ->rawColumns(['action', 'party_name'])
            ->make(true);
    }



    public function ReminderList()
    {
        return $this->generateReportView('reminder');
    }

    public function TodaysFollowUp()
    {
        request()->merge(['filter' => 'today']);
        return $this->generateReportView('reminder');
    }

    public function PendingFollowUps()
    {
        request()->merge(['filter' => 'pending']);
        return $this->generateReportView('reminder');
    }

    public function UpcomingFollowUps()
    {
        request()->merge(['filter' => 'upcoming']);
        return $this->generateReportView('reminder');
    }

    public function TodayDSR()
    {
        request()->merge(['filter' => 'today']);
        return $this->generateReportView('dsr');
    }

    public function ThisMonthDSR()
    {
        request()->merge(['filter' => 'this_month']);
        return $this->generateReportView('dsr');
    }

    public function AllDSR()
    {
        request()->merge(['filter' => 'all']);
        return $this->generateReportView('dsr');
    }

    public function AllActivityLeads()
    {
        request()->merge(['filter' => 'all']);
        return $this->generateReportView('all_activity_leads');
    }

    protected function generateReportView($reportType)
    {
        try {
            $userId = Auth::id();

            // Resolve Box Context strictly via User ID (ignoring route slug)
            $boxUser = BoxUser::where('user_id', $userId)->first();

            if (!$boxUser) {
                return redirect()->route('login')->with('error', 'No account found.');
            }

            $boxId = $boxUser->box_id;
            $boxSlug = $boxUser->box->slug ?? '';

            // Fetch System Fields
            $fields = SystemField::where('object', 'leads')
                ->where('box_id', $boxId)
                ->where('is_visible', true)
                ->orderBy('sequence')
                ->get();

            $leadSources = CrmSource::where('box_id', $boxId)->select('id', 'name')->get();

            $industries = Industry::where('box_id', $boxId)->select('id', 'name')->get();

            $leadsStatus = ContactStage::where('box_id', $boxId)->select('id', 'name', 'reminder_mandatory')->get();

            $countries = Country::select('id', 'name')->get();

            $allActivityNames = \App\Models\Call::where('box_id', $boxId)
                ->where('status', 1)
                ->pluck('name')
                ->implode(', ');

            $owners = BoxUser::where('box_id', $boxId)->with('user:id,name')->get()->map(function ($boxUser) {
                if ($boxUser->user) {
                    $boxUser->user->department_id = $boxUser->department;
                    $boxUser->user->designation_id = $boxUser->designation;
                    return $boxUser->user;
                }
                return null;
            })->filter();

            $customFields = BoxCustomField::where('box_id', $boxId)
                ->where('object', 'leads')
                ->where('status', 'Enabled')
                ->orderBy('ordering')
                ->get();

            $currentFilter = request()->input('filter', 'today');

            $leadsCount = Lead::where('box_id', $boxId)->count();
            if ($reportType == 'all_activity_leads') {
                $leadsCount = Activity::where('box_id', $boxId)->count();
            } elseif ($reportType == 'reminder') {
                $leadsQuery = self::buildReminderLeadsQuery($boxId, ['reminder_filter' => $currentFilter]);
                $leadsCount = $leadsQuery->count();
            }

            $activeReport = null;
            if (request()->has('report_id')) {
                $activeReport = MyReport::where('id', request()->report_id)
                    ->where('box_id', $boxId)
                    ->first();
            }

            $viewName = 'frontend.leads.alldsr';
            $pageTitle = "All DSR";
            $presetFilters = [];

            if ($reportType == 'reminder') {
                if (request()->has('start_date') && request()->has('end_date')) {
                    $pageTitle = "Filtered Reminder List";
                    $currentFilter = 'all'; // Prevent 'today' filter logic from conflicting
                } elseif ($currentFilter == 'today') {
                    $pageTitle = "Today's Follow Up";
                    $presetFilters[] = ['field' => 'upcoming_activity_date', 'operator' => 'date_is', 'value' => 'today'];
                } elseif ($currentFilter == 'pending') {
                    $pageTitle = "Pending Follow Ups";
                    $presetFilters[] = ['field' => 'upcoming_activity_date', 'operator' => 'date_is', 'value' => 'less_than_today'];
                } elseif ($currentFilter == 'upcoming') {
                    $pageTitle = "Upcoming Follow Ups";
                    $presetFilters[] = ['field' => 'upcoming_activity_date', 'operator' => 'date_is', 'value' => 'greater_than_today'];
                } else {
                    $pageTitle = "Reminder List";
                }
            } elseif ($reportType == 'dsr') {
                if (request()->has('start_date') && request()->has('end_date')) {
                    $pageTitle = "Filtered DSR";
                    $currentFilter = 'all';
                    $presetFilters[] = ['field' => 'activity_setting_name', 'operator' => 'equals', 'value' => 'all', 'label' => $allActivityNames];
                } elseif ($currentFilter == 'this_month') {
                    $pageTitle = "This Month DSR";
                    $presetFilters[] = ['field' => 'activity_setting_name', 'operator' => 'equals', 'value' => 'all', 'label' => $allActivityNames];
                    $presetFilters[] = ['field' => 'last_activity_date', 'operator' => 'date_is', 'value' => 'this_month'];
                } elseif ($currentFilter == 'all') {
                    $pageTitle = "All DSR";
                    $presetFilters[] = ['field' => 'activity_setting_name', 'operator' => 'equals', 'value' => 'all', 'label' => $allActivityNames];
                } else {
                    $pageTitle = "Today DSR";
                    $presetFilters[] = ['field' => 'activity_setting_name', 'operator' => 'equals', 'value' => 'all', 'label' => $allActivityNames];
                    $presetFilters[] = ['field' => 'last_activity_date', 'operator' => 'date_is', 'value' => 'today'];
                }
            } elseif ($reportType == 'all_activity_leads') {
                $viewName = 'frontend.leads.allactivityleads';
                $pageTitle = "All Activity Leads";
            }

            if (request()->has('status')) {
                $presetFilters[] = [
                    'field' => 'status',
                    'operator' => 'equals',
                    'value' => request('status')
                ];
            }

            if (request()->has('task_timing')) {
                $presetFilters[] = [
                    'field' => 'task_timing',
                    'operator' => 'equals',
                    'value' => request('task_timing')
                ];
            }

            return view($viewName, compact(
                'fields',
                'leadSources',
                'industries',
                'countries',
                'owners',
                'leadsStatus',
                'customFields',
                'leadsCount',
                'activeReport',
                'currentFilter',
                'pageTitle',
                'reportType',
                'presetFilters',
                'boxSlug'
            ));
        } catch (\Throwable $e) {
            Log::error("Error loading {$reportType} List: " . $e->getMessage());
            return $e->getMessage();
        }
    }
    public function store(Request $request)
    {

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $action = ($request->has('id') && !empty($request->input('id'))) ? 'update' : 'create';
        $canAccess = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('leads', $action, 'list');
        if (!$canAccess) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to ' . $action . ' leads.'
            ], 403);
        }

        $authId = Auth::id();
        $boxId = $boxUser->box_id;
        $tableName = 'data_' . $boxId;

        // Check if this is an update (has id) or create
        $isUpdate = $request->has('id') && !empty($request->input('id'));

        // Debug: Log assigned_to value from request
        Log::info(' Lead Store - assigned_to debug:', [
            'raw_assigned_to' => $request->input('assigned_to'),
            'has_assigned_to' => $request->has('assigned_to'),
            'raw_owner_id' => $request->input('owner_id'),
            'has_owner_id' => $request->has('owner_id'),
            'all_request' => $request->all()
        ]);

        // Alias 'name' to 'lead_name' if 'lead_name' is missing (fix for saving issue)
        if ($request->has('name') && !$request->has('lead_name')) {
            $request->merge(['lead_name' => $request->input('name')]);
        }

        // Backward compatibility: If owner_id is sent, map it to assigned_to
        if ($request->has('owner_id') && !$request->has('assigned_to')) {
            $request->merge(['assigned_to' => $request->input('owner_id')]);
            Log::info('⚠️ Mapped owner_id to assigned_to for backward compatibility');
        }

        // Sanitize numeric and date fields - convert empty strings to null
        $numericFields = ['value', 'assigned_to', 'country_id', 'state_id', 'city_id'];
        foreach ($numericFields as $field) {
            if ($request->has($field) && $request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        // Sanitize date fields
        if ($request->has('matured_date') && $request->input('matured_date') === '') {
            $request->merge(['matured_date' => null]);
        }

        $standardRules = [
            'id' => 'nullable|exists:leads,id',
            'lead_name' => 'nullable|string|max:255', // Base fallback
            'email' => 'nullable|email|max:255',
            'sec_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'sec_phone' => 'nullable|string|max:20',
            'phone_type' => 'nullable|string',
            'lead_type' => 'nullable|string|in:Direct Lead,Buy Lead,Call Lead,Catalogue View',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:100',
            'value' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'lead_status' => 'nullable',
            'lead_status_id' => 'nullable',
            'source' => 'nullable',
            'lead_source_id' => 'nullable',
            'industry' => 'nullable',
            'assigned_to' => 'nullable',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'location' => 'nullable|string',
            'tags' => 'nullable|string',
            'description' => 'nullable|string',
            'matured_date' => 'nullable|date',
            'visibility' => 'nullable|string',
            'status' => 'nullable|string',
            'last_name' => 'nullable|string|max:255',
            'query' => 'nullable|string',
            'gstin_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'lead_sub_status_id' => 'nullable|integer',
        ];

        // Fetch dynamic system field requirements
        $systemFields = SystemField::where('box_id', $boxId)->where('object', 'leads')->get();
        foreach ($systemFields as $field) {
            $fieldName = $field->field;
            if (isset($standardRules[$fieldName])) {
                $rulesString = $standardRules[$fieldName];
                if ($field->is_required) {
                    $rulesString = str_replace('nullable', 'required', $rulesString);
                    if (!str_contains($rulesString, 'required')) {
                        $rulesString = 'required|' . $rulesString;
                    }
                } else {
                    $rulesString = str_replace('required', 'nullable', $rulesString);
                }
                $standardRules[$fieldName] = $rulesString;
            }
        }
        $customFields = BoxCustomField::where('box_id', $boxId)
            ->where('object', 'leads')
            ->get();

        $customRules = [];
        foreach ($customFields as $field) {
            $key = $field->col_name;
            $rules = $field->required ? ['required'] : ['nullable'];
            switch ($field->type) {
                case 'text':
                    $rules[] = 'string|max:255';
                    break;
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'date':
                    $rules[] = 'date';
                    break;
                case 'select':
                case 'radio':
                    if (!empty($field->params['options'])) {
                        $values = array_column($field->params['options'], 'value');
                        $rules[] = 'in:' . implode(',', $values);
                    }
                    break;
                default:
                    $rules[] = 'string|max:255';
            }

            $customRules[$key] = implode('|', $rules);
        }

        $validator = Validator::make($request->all(), array_merge($standardRules, $customRules));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $standardData = array_intersect_key($validated, $standardRules);
        $customData = array_intersect_key($validated, $customRules);

        $box = $boxUser->box;
        $uniqueField = $box->lead_required_field ?? 'email';
        $allowDuplicate = $box->allow_duplicate_leads ?? 0;

        $isDuplicate = 0;
        $existingLead = null;
        $email = $standardData['email'] ?? null;
        $secEmail = $standardData['sec_email'] ?? null;
        $phone = $standardData['phone'] ?? null;
        $secPhone = $standardData['sec_phone'] ?? null;

        if (($allowDuplicate == 0 || $isUpdate) && ($email || $phone || $secEmail || $secPhone)) {
            $duplicateQuery = Lead::where('box_id', $boxId)->where('duplicate_leads', 0)
                ->where(function ($q) use ($email, $phone, $secEmail, $secPhone, $uniqueField) {
                    if ($uniqueField === 'email') {
                        $q->where(function ($inner) use ($email, $secEmail) {
                            if ($email) {
                                $inner->where('email', $email)->orWhere('sec_email', $email);
                            }
                            if ($secEmail) {
                                $inner->orWhere('email', $secEmail)->orWhere('sec_email', $secEmail);
                            }
                        });
                    } elseif ($uniqueField === 'phone') {
                        $q->where(function ($inner) use ($phone, $secPhone) {
                            if ($phone) {
                                $inner->where('phone', $phone)->orWhere('sec_phone', $phone)->orWhere('mobile', $phone);
                            }
                            if ($secPhone) {
                                $inner->orWhere('phone', $secPhone)->orWhere('sec_phone', $secPhone)->orWhere('mobile', $secPhone);
                            }
                        });
                    } elseif ($uniqueField === 'both') {
                        $q->where(function ($inner) use ($email, $phone, $secEmail, $secPhone) {
                            $inner->where(function ($innerEmail) use ($email, $secEmail) {
                                if ($email) {
                                    $innerEmail->where('email', $email)->orWhere('sec_email', $email);
                                }
                                if ($secEmail) {
                                    $innerEmail->orWhere('email', $secEmail)->orWhere('sec_email', $secEmail);
                                }
                            })->where(function ($innerPhone) use ($phone, $secPhone) {
                                if ($phone) {
                                    $innerPhone->where('phone', $phone)->orWhere('sec_phone', $phone)->orWhere('mobile', $phone);
                                }
                                if ($secPhone) {
                                    $innerPhone->orWhere('phone', $secPhone)->orWhere('sec_phone', $secPhone)->orWhere('mobile', $secPhone);
                                }
                            });
                        });
                    } else {
                        // Default fallback / Combined Check (OR condition)
                        $q->where(function ($inner) use ($email, $phone, $secEmail, $secPhone) {
                            $isFirst = true;
                            if ($email) {
                                $inner->where('email', $email)->orWhere('sec_email', $email);
                                $isFirst = false;
                            }
                            if ($secEmail) {
                                if ($isFirst)
                                    $inner->where('email', $secEmail)->orWhere('sec_email', $secEmail);
                                else
                                    $inner->orWhere('email', $secEmail)->orWhere('sec_email', $secEmail);
                                $isFirst = false;
                            }
                            if ($phone) {
                                if ($isFirst)
                                    $inner->where('phone', $phone)->orWhere('sec_phone', $phone)->orWhere('mobile', $phone);
                                else
                                    $inner->orWhere('phone', $phone)->orWhere('sec_phone', $phone)->orWhere('mobile', $phone);
                                $isFirst = false;
                            }
                            if ($secPhone) {
                                if ($isFirst)
                                    $inner->where('phone', $secPhone)->orWhere('sec_phone', $secPhone)->orWhere('mobile', $secPhone);
                                else
                                    $inner->orWhere('phone', $secPhone)->orWhere('sec_phone', $secPhone)->orWhere('mobile', $secPhone);
                            }
                        });
                    }
                });

            if ($isUpdate && !empty($standardData['id'])) {
                $duplicateQuery->where('id', '!=', $standardData['id']);
            }

            $existingLead = $duplicateQuery->orderBy('id', 'asc')->first();
            if ($existingLead) {
                $isDuplicate = 1;
            }
        }

        $isMergedDuplicate = false;
        // If we found a duplicate and allow_duplicate_leads is disabled (meaning duplicate check is enabled)
        // we flag it to add an activity log to the parent record.
        if (!$isUpdate && $existingLead && $allowDuplicate == 0) {
            $isMergedDuplicate = true;
        }

        $now = Carbon::now();
        if ($isUpdate && !empty($standardData['id'])) {
            $lead = Lead::findOrFail($standardData['id']);



            $updateData = [
                'last_updated_by' => $authId,
                'updated_at' => $now,
            ];

            // Auto-track Last Assigned To
            $newAssignedToInput = $standardData['assigned_to'] ?? null;
            $newAssignedTo = is_array($newAssignedToInput) ? ($newAssignedToInput[0] ?? null) : $newAssignedToInput;

            if ($lead->assigned_to != $newAssignedTo) {
                $updateData['assigned_to'] = $newAssignedTo;
                $updateData['last_assigned_to'] = $lead->assigned_to;
                $updateData['change_assigned_to_time'] = $now;
            } else {
                $updateData['assigned_to'] = $newAssignedTo;
            }

            $newStatusId = $standardData['lead_status'] ?? $standardData['lead_status_id'] ?? null;
            $newSubStatusId = $standardData['lead_sub_status_id'] ?? null;

            // Auto-track Last Lead Status
            if ($lead->lead_status_id != $newStatusId) {
                $updateData['lead_status_id'] = $newStatusId;
                $updateData['last_lead_status'] = $lead->lead_status_id;
                $updateData['change_lead_status_time'] = $now;
            } else {
                $updateData['lead_status_id'] = $newStatusId;
            }

            $updateData += [
                'name' => $standardData['lead_name'],
                'last_name' => $standardData['last_name'] ?? null,
                'sec_email' => $standardData['sec_email'] ?? null,
                'company_name' => $standardData['company_name'] ?? null,
                'designation' => $standardData['designation'] ?? null,
                'email' => $standardData['email'] ?? null,
                'phone' => $standardData['phone'] ?? null,
                'phone_type' => $standardData['phone_type'] ?? null,
                'lead_type' => $standardData['lead_type'] ?? 'lead',
                'lead_source_id' => $standardData['source'] ?? $standardData['lead_source_id'] ?? null,
                'country_id' => $standardData['country_id'] ?? null,
                'state_id' => $standardData['state_id'] ?? null,
                'city_id' => $standardData['city_id'] ?? null,
                'address' => $standardData['address'] ?? null,
                'postal_code' => $standardData['postal_code'] ?? null,
                'location' => $standardData['location'] ?? null,
                // 'lead_status_id' handled above
                // 'assigned_to' handled above
                'industry' => $standardData['industry'] ?? null,
                'tags' => $standardData['tags'] ?? null,
                'matured_date' => $standardData['matured_date'] ?? null,
                'visibility' => $standardData['visibility'] ?? null,
                'status' => $standardData['status'] ?? null,
                'query' => $standardData['query'] ?? null,
                'gstin_number' => $standardData['gstin_number'] ?? null,
                'pan_number' => $standardData['pan_number'] ?? null,
                'duplicate_leads' => $isDuplicate,
                'lead_sub_status_id' => $standardData['lead_sub_status_id'] ?? null,
            ];

            // Only update amount if value is provided
            if (isset($standardData['value']) && $standardData['value'] !== null && $standardData['value'] !== '') {
                $updateData['amount'] = $standardData['value'];
            } elseif (isset($standardData['amount']) && $standardData['amount'] !== null && $standardData['amount'] !== '') {
                $updateData['amount'] = $standardData['amount'];
            }

            // Only update currency if provided
            if (isset($standardData['currency']) && $standardData['currency'] !== null && $standardData['currency'] !== '') {
                $updateData['currency'] = $standardData['currency'];
            }

            $oldStatusId = $lead->lead_status_id;
            $oldSubStatusId = $lead->lead_sub_status_id;
            $oldAssignedTo = $lead->assigned_to;

            $lead->update($updateData);

            // Log changes in Activity
            $changes = $lead->getChanges();
            $activityParams = [];
            $descriptionParts = [];
            $hasChanges = !empty($changes);

            // 1. Status Change
            if ($oldStatusId != $lead->lead_status_id) {
                $newStatus = ContactStage::find($lead->lead_status_id);
                $activityParams['lead_status_name'] = $newStatus->name ?? 'Unknown';
                $activityParams['lead_status_id'] = $lead->lead_status_id;
                $descriptionParts[] = "Lead Status changed to " . ($newStatus->name ?? 'Unknown');
            }

            // 2. Sub Status Change
            if ($oldSubStatusId != $lead->lead_sub_status_id) {
                $newSubStatus = LeadSubStatus::find($lead->lead_sub_status_id);
                $activityParams['lead_sub_status_name'] = $newSubStatus->name ?? 'Unknown';
                $activityParams['lead_sub_status_id'] = $lead->lead_sub_status_id;
                $descriptionParts[] = "Lead Sub Status changed to " . ($newSubStatus->name ?? 'Unknown');
            }

            // 3. Assignment Change (Transfer)
            if ($oldAssignedTo != $lead->assigned_to) {
                $oldUser = User::find($oldAssignedTo);
                $newUser = User::find($lead->assigned_to);
                $activityParams['previous_assignee_name'] = $oldUser->name ?? 'Unassigned';
                $activityParams['current_assignee_name'] = $newUser->name ?? 'Unassigned';
                $descriptionParts[] = "Assigned to " . ($newUser->name ?? 'Unassigned');

                // Send Transfer Notification
                if ($newUser) {
                    \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $newUser->id, 'transfer', $authId);
                }
            }

            // 4. Other General Changes
            // Exclude fields we already handled or that shouldn't trigger a log
            $ignoredFields = [
                'updated_at',
                'last_updated_by',
                'lead_status_id',
                'lead_sub_status_id',
                'assigned_to',
                'change_assigned_to_time',
                'change_lead_status_time',
                'duplicate_leads'
            ];

            $otherChanges = array_diff_key($changes, array_flip($ignoredFields));

            if (!empty($otherChanges)) {
                $changedFieldNames = array_map(function ($field) {
                    return ucwords(str_replace('_', ' ', $field));
                }, array_keys($otherChanges));

                $descriptionParts[] = "Updated: " . implode(', ', $changedFieldNames);
            }

            if (!empty($descriptionParts)) {
                \App\Models\Activity::create([
                    'box_id' => $boxId,
                    'activity_id' => $lead->id,
                    'type' => 'history',
                    'subject' => 'Lead Updated',
                    'description' => implode("\n", $descriptionParts),
                    'activity_date' => now(),
                    'added_by' => $authId,
                    'last_updated_by' => $authId,
                    'status' => 'Close',
                    'completed_date' => now(),
                    'params' => json_encode($activityParams),
                ]);
            }
            

            // Handle Multi-assignment and History
            $assignedToInput = $request->input('assigned_to');
            $newAssigneeIds = [];
            if (is_array($assignedToInput)) {
                $newAssigneeIds = array_filter($assignedToInput);
            } elseif (!empty($assignedToInput)) {
                $newAssigneeIds = [$assignedToInput];
            }

            $currentAssigneeIds = $lead->assignees()->pluck('users.id')->toArray();
            $toAdd = array_diff($newAssigneeIds, $currentAssigneeIds);
            $toRemove = array_diff($currentAssigneeIds, $newAssigneeIds);

            foreach ($toAdd as $userId) {
                $lead->assignees()->attach($userId, ['assigned_by' => $authId]);
                LeadAssignmentHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => $userId,
                    'assigned_by' => $authId,
                    'action' => 'assigned',
                ]);
            }

            foreach ($toRemove as $userId) {
                $lead->assignees()->detach($userId);
                LeadAssignmentHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => $userId,
                    'assigned_by' => $authId,
                    'action' => 'removed',
                ]);
            }

        } else {
            $lead = Lead::create([
                'box_id' => $boxId,
                'name' => $standardData['lead_name'],
                'last_name' => $standardData['last_name'] ?? null,
                'sec_email' => $standardData['sec_email'] ?? null,
                'company_name' => $standardData['company_name'] ?? null,
                'designation' => $standardData['designation'] ?? null,
                'email' => $standardData['email'] ?? null,
                'phone' => $standardData['phone'],
                'phone_type' => $standardData['phone_type'] ?? null,
                'lead_type' => $standardData['lead_type'] ?? 'lead',
                'amount' => $standardData['value'] ?? $standardData['amount'] ?? null,
                'currency' => $standardData['currency'] ?? null,
                'is_paid' => 1,
                'lead_source_id' => $standardData['source'] ?? $standardData['lead_source_id'] ?? null,
                'country_id' => $standardData['country_id'] ?? null,
                'state_id' => $standardData['state_id'] ?? null,
                'city_id' => $standardData['city_id'] ?? null,
                'address' => $standardData['address'] ?? null,
                'postal_code' => $standardData['postal_code'] ?? null,
                'location' => $standardData['location'] ?? null,
                'lead_status_id' => $standardData['lead_status'] ?? $standardData['lead_status_id'] ?? null,
                'assigned_to' => is_array($request->assigned_to) ? ($request->assigned_to[0] ?? null) : ($standardData['assigned_to'] ?? null),
                'industry' => $standardData['industry'] ?? null,
                'tags' => $standardData['tags'] ?? null,
                'matured_date' => $standardData['matured_date'] ?? null,
                'visibility' => $standardData['visibility'] ?? null,
                'status' => $standardData['status'] ?? null,
                'query' => $standardData['query'] ?? null,
                'gstin_number' => $standardData['gstin_number'] ?? null,
                'pan_number' => $standardData['pan_number'] ?? null,
                'added_by' => $authId,
                'last_updated_by' => $authId,
                'created_at' => $now,
                'updated_at' => $now,
                'duplicate_leads' => $isDuplicate,
                'duplicate_of_id' => $isDuplicate && $existingLead ? $existingLead->id : null,
                'lead_sub_status_id' => $standardData['lead_sub_status_id'] ?? null,
                'change_assigned_to_time' => $now,
                'change_lead_status_time' => $now,
            ]);

            // Handle Multi-assignment for new leads
            $assignedToInput = $request->input('assigned_to');
            $newAssigneeIds = [];
            if (is_array($assignedToInput)) {
                $newAssigneeIds = array_filter($assignedToInput);
            } elseif (!empty($assignedToInput)) {
                $newAssigneeIds = [$assignedToInput];
            }

            foreach ($newAssigneeIds as $userId) {
                $lead->assignees()->attach($userId, ['assigned_by' => $authId]);
                LeadAssignmentHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => $userId,
                    'assigned_by' => $authId,
                    'action' => 'assigned',
                ]);
            }

            // Refresh to ensure we have defaults for activity logging
            $lead->refresh();

            // Notify Creator if Duplicate

            if ($isDuplicate) {
                \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $authId, 'duplicate');
            } elseif ($lead->assigned_to) {
                // Send Notification to assignee
                \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $lead->assigned_to);
            }

            // Create Activity Log for New Lead
            $activityParams = [];
            $descriptionParts = ['New Lead Created'];

            if (!empty($request->query_data)) {
                $descriptionParts[] = $request->input('query');
            }

            if ($lead->lead_status_id) {
                $status = ContactStage::find($lead->lead_status_id);
                $activityParams['lead_status_name'] = $status->name ?? 'Unknown';
                $activityParams['lead_status_id'] = $lead->lead_status_id;
                $descriptionParts[] = "Lead Status set to " . ($status->name ?? 'Unknown');
            }
            if ($lead->lead_sub_status_id) {
                $subStatus = LeadSubStatus::find($lead->lead_sub_status_id);
                $activityParams['lead_sub_status_name'] = $subStatus->name ?? 'Unknown';
                $activityParams['lead_sub_status_id'] = $lead->lead_sub_status_id;
                $descriptionParts[] = "Lead Sub Status set to " . ($subStatus->name ?? 'Unknown');
            }
            if ($lead->assigned_to) {
                $assignee = User::find($lead->assigned_to);
                $activityParams['current_assignee_name'] = $assignee->name ?? 'Unassigned';
                $descriptionParts[] = "Assigned to " . ($assignee->name ?? 'Unassigned');
            }

            Activity::create([
                'box_id' => $boxId,
                'activity_id' => $lead->id,
                'type' => 'history',
                'subject' => 'Lead Created',
                'description' => implode("\n", $descriptionParts),
                'activity_date' => now(),
                'added_by' => $authId,
                'last_updated_by' => $authId,
                'status' => 'Close',
                'completed_date' => now(),
                'params' => json_encode($activityParams),
            ]);

            if ($isMergedDuplicate) {
                // Log onto existing master lead!
                $allInput = $request->except(['_token', 'id']);
                $mergedSummary = "New duplicate entry received and created (New Lead ID: {$lead->id}):\n";
                foreach ($allInput as $key => $value) {
                    if (!empty($value) && !is_array($value)) {
                        $label = ucwords(str_replace('_', ' ', $key));
                        $mergedSummary .= "• {$label}: {$value}\n";
                    }
                }

                Activity::create([
                    'box_id' => $boxId,
                    'activity_id' => $existingLead->id,
                    'type' => 'history',
                    'subject' => 'Duplicate Lead Received',
                    'description' => $mergedSummary,
                    'activity_date' => now(),
                    'added_by' => $authId,
                    'last_updated_by' => $authId,
                    'status' => 'Close',
                    'completed_date' => now(),
                ]);
            }
        }

        $customData['lead_id'] = $lead->id;
        $customData['updated_at'] = $now;

        // Remove lead_id and updated_at for the null check
        $filteredCustomData = collect($customData)->except(['lead_id', 'updated_at'])->filter(function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Proceed only if at least one non-null custom field exists
        if ($filteredCustomData->isNotEmpty()) {
            //  Update or Insert
            if (Schema::hasTable($tableName)) {
                $existingCustom = DB::table($tableName)->where('lead_id', $lead->id)->first();
                if ($existingCustom) {
                    DB::table($tableName)->where('lead_id', $lead->id)->update($customData);
                } else {
                    $customData['created_at'] = $now;
                    DB::table($tableName)->insert($customData);
                }
            }
        }
        // Reload the lead with relationships for response
        $lead->refresh();
        $lead->load(
            'country',
            'state',
            'city',
            'leadSource',
            'leadStatus',
            'assignedTo',
            'industryRelation'
        );

        // Get updated custom data - get all columns from custom_data table
        $updatedCustomData = [];
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            $columnsToSelect = array_diff($columns, ['id', 'lead_id', 'created_at', 'updated_at']);

            if (!empty($columnsToSelect)) {
                $updatedCustomData = DB::table($tableName)
                    ->where('lead_id', $lead->id)
                    ->select($columnsToSelect)
                    ->first();
            }
        }

        $duplicateInfo = null;
        if ($isMergedDuplicate && $existingLead) {
            $duplicateInfo = [
                'name' => trim(($existingLead->name ?? '') . ' ' . ($existingLead->last_name ?? '')),
                'assigned_to' => optional(\App\Models\User::find($existingLead->assigned_to))->name ?? 'Unassigned',
                'lead_status' => optional(\App\Models\ContactStage::find($existingLead->lead_status_id))->name ?? 'Unknown',
            ];
        }

        return response()->json([
            'status' => true,
            'message' => !empty($standardData['id']) ? 'Lead updated successfully.' : 'Lead created successfully.',
            'lead_id' => $lead->id,
            'is_duplicate' => $isMergedDuplicate,
            'duplicate_info' => $duplicateInfo,
            'data' => [
                'lead' => $lead,
                'custom_data' => $updatedCustomData,
            ],
        ], 200);
    }

    public function update(Request $request)
    {
        // The update method uses the same logic as store
        // It checks for 'id' in the request to determine if it's an update
        return $this->store($request);
    }

    // Laravel Controller (already almost correct, just improve this logic a bit)
    public function getDependentData(Request $request)
    {
        $parentField = $request->input('parent_field');
        $parentId = $request->input('parent_id');

        $data = [];

        if ($parentField === 'country_id') {
            $data = State::where('country_id', $parentId)->select('id', 'name')->get();
        } elseif ($parentField === 'state_id') {
            $data = City::where('state_id', $parentId)->select('id', 'name')->get();
        }

        return response()->json(['data' => $data]);
    }

    public static function buildLeadsQuery($boxId, $params, $eagerLoadCustom = false)
    {
        // Inject User Preferences
        try {
            $preferenceService = app(\App\Services\PreferenceService::class);
            $userPreferences = $preferenceService->getModulePreferences('leads');

            if (empty($params['lead_scope']) && isset($userPreferences['default_scope'])) {
                $params['lead_scope'] = $userPreferences['default_scope'];
            }

            if (empty($params['advanced_filter']) && isset($userPreferences['default_filter'])) {
                $params['advanced_filter'] = $userPreferences['default_filter'];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to load lead preferences: ' . $e->getMessage());
        }

        $customFields = BoxCustomField::where('box_id', $boxId)
            ->where('object', 'leads')
            ->pluck('col_name')
            ->toArray();
        $tableName = 'data_' . $boxId;
        $hasCustomTable = Schema::hasTable($tableName) && !empty($customFields);

        $leadsQuery = Lead::where('leads.box_id', $boxId)
            ->with([
                'country:id,name',
                'state:id,name',
                'city:id,name',
                'assignedTo:id,name',
                'leadStatus:id,name,color',
                'leadSource:id,name,box_id',
                'industryRelation:id,name,box_id',
                'updatedBy:id,name',
                'lastAssignedTo:id,name',
                'lastLeadStatus:id,name,color',
                'leadSubStatus:id,name',
                'assignees:id,name',
                'assignmentHistories.user:id,name'
            ])
            ->orderBy('leads.id', 'desc');

        $columns = Schema::getColumnListing('leads');
        $prefixedColumns = array_map(function ($col) {
            return 'leads.' . $col;
        }, $columns);

        $userId = \Illuminate\Support\Facades\Auth::id();
        
        // Exclude the physical is_starred column and replace with dynamic check
        if (($key = array_search('leads.is_starred', $prefixedColumns)) !== false) {
            unset($prefixedColumns[$key]);
        }
        $prefixedColumns[] = \Illuminate\Support\Facades\DB::raw("EXISTS (SELECT 1 FROM lead_stars WHERE lead_stars.lead_id = leads.id AND lead_stars.user_id = $userId) as is_starred");
        $leadsQuery->select($prefixedColumns);


        // OPTIONAL: Join custom table if requested (for Bulk Job) to prevent N+1
        $customTableJoined = false;

        if ($eagerLoadCustom && $hasCustomTable) {
            $leadsQuery->leftJoin($tableName, 'leads.id', '=', $tableName . '.lead_id');
            foreach ($customFields as $field) {
                $leadsQuery->addSelect($tableName . '.' . $field . ' as custom_' . $field);
            }
            $customTableJoined = true;
        }

        // Apply scope filter (Individual vs Team vs All)
        $userId = Auth::id();
        if (isset($params['lead_scope'])) {
            $scope = $params['lead_scope'];
            if ($scope === 'individual') {
                $leadsQuery->where('leads.assigned_to', $userId);
            } elseif ($scope === 'team') {
                // If specific team members are selected, filter by them
                if (isset($params['team_member_ids']) && !empty($params['team_member_ids'])) {
                    $leadsQuery->whereIn('leads.assigned_to', $params['team_member_ids']);
                } else {
                    $boxUser = BoxUser::where('user_id', $userId)->where('box_id', $boxId)->first();
                    $isGlobalAdmin = Auth::user() && Auth::user()->hasRole(['admin', 'superadmin'], 'web');
                    $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
                    
                    if (!$isGlobalAdmin && !$isBoxAdmin && $boxUser) {
                        $subordinateIds = BoxUser::getAllSubordinateIds($boxId, $userId, $boxUser);
                        $subordinateUserIds = BoxUser::whereIn('id', $subordinateIds)->pluck('user_id')->toArray();
                        $allRelevantUserIds = array_merge([$userId], $subordinateUserIds);
                        $leadsQuery->whereIn('leads.assigned_to', $allRelevantUserIds);
                    }
                    // If they ARE an admin, we simply don't apply an assigned_to filter, showing all leads.
                }
            } elseif ($scope === 'all') {
                $boxUser = BoxUser::where('user_id', $userId)->where('box_id', $boxId)->first();
                $isGlobalAdmin = Auth::user() && Auth::user()->hasRole(['admin', 'superadmin'], 'web');
                $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
                
                if (!$isGlobalAdmin && !$isBoxAdmin) {
                    // Not an admin? Fall back to team scope logic to prevent unauthorized access to all leads
                    if ($boxUser) {
                        $subordinateIds = BoxUser::getAllSubordinateIds($boxId, $userId, $boxUser);
                        $subordinateUserIds = BoxUser::whereIn('id', $subordinateIds)->pluck('user_id')->toArray();
                        $allRelevantUserIds = array_merge([$userId], $subordinateUserIds);
                        $leadsQuery->whereIn('leads.assigned_to', $allRelevantUserIds);
                    } else {
                        $leadsQuery->where('leads.assigned_to', $userId);
                    }
                }
                // If they ARE an admin, we don't apply an assigned_to filter, showing all leads.
            }
        }

        // Check for 'all' parameter to include trashed records
        if (isset($params['all']) && $params['all'] == 'true') {
            if (\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listall')) {
                $leadsQuery->withTrashed();
            } else {
                // If no permission for all leads, fallback to regular non-duplicate leads
                $leadsQuery->where('duplicate_leads', 0);
            }
        } elseif (isset($params['trashed']) && $params['trashed'] == 'true') {
            if (\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listtrash')) {
                $leadsQuery->onlyTrashed();
            } else {
                // If no permission for trash, show nothing
                $leadsQuery->whereRaw('1=0');
            }
        } else {
            // If bulk_upload_id is present, show both duplicates and active leads from that upload
            if (isset($params['bulk_upload_id'])) {
                $leadsQuery->where('leads.bulk_upload_id', $params['bulk_upload_id']);
                // Use whereIn instead of default duplicate filter to show everything
                // but we DON'T apply the default duplicate_leads where's here
            } else {
                // Standard flow: check request for duplicate view
                if (isset($params['duplicate']) && $params['duplicate'] == 'true') {
                    if (\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listduplicate')) {
                        $leadsQuery->where('duplicate_leads', 1);
                    } else {
                        // If no permission for duplicate, show nothing
                        $leadsQuery->whereRaw('1=0');
                    }
                } else {
                    // Default view: Show only NON-duplicates
                    $leadsQuery->where('duplicate_leads', 0);
                }
            }
        }

        // Apply Starred Filter
        if (isset($params['starred']) && $params['starred'] == 'true') {
            $leadsQuery->whereHas('stars', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Apply Date Range Filter
        if (isset($params['date_range']) && !empty($params['date_range'])) {
            $dates = explode(' - ', $params['date_range']);
            if (count($dates) == 2) {
                try {
                    $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();
                    $leadsQuery->whereBetween('leads.created_at', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Invalid date format in leads date filter: ' . $params['date_range']);
                }
            }
        }

        // Apply Advanced Filter
        if (isset($params['advanced_filter']) && !empty($params['advanced_filter'])) {
            $filters = $params['advanced_filter'];
            if (isset($filters['field'])) {
                $filters = [$filters];
            }

            $relationsMap = [
                'industry' => 'industryRelation',
                'country_id' => 'country',
                'state_id' => 'state',
                'city_id' => 'city',
            ];

            $optionalRelations = [
                'lead_source_id' => 'leadSource',
                'lead_status' => 'leadStatus',
                'lead_status_id' => 'leadStatus',
                'assigned_to' => 'assignedTo',
                'owner' => 'assignedTo',
                'last_assigned_to' => 'lastAssignedTo',
                'last_lead_status' => 'lastLeadStatus',
                'assigned_history' => 'assignmentHistories',
            ];

            $columnMap = [
                'lead_name' => 'name',
                'value' => 'amount',
                'lead_status' => 'lead_status_id',
                'lead_status_id' => 'lead_status_id',
                'lead_source_id' => 'lead_source_id',
                'assigned_to' => 'assigned_to',
                'owner' => 'assigned_to',
            ];

            foreach ($filters as $filter) {
                if (empty($filter['field']))
                    continue;

                $field = $filter['field'];
                $operator = $filter['operator'] ?? 'contains';
                $value = $filter['value'] ?? '';

                if (str_starts_with($field, 'custom_')) {
                    if ($hasCustomTable) {
                        $realField = str_replace('custom_', '', $field);

                        // DYNAMIC JOIN: Only join if not already joined
                        if (!$customTableJoined) {
                            $leadsQuery->leftJoin($tableName, 'leads.id', '=', $tableName . '.lead_id');
                            $customTableJoined = true;
                        }

                        $dbField = $tableName . '.' . $realField;
                        self::applyCondition($leadsQuery, $dbField, $operator, $value);
                    }
                } elseif (array_key_exists($field, $relationsMap)) {
                    $relation = $relationsMap[$field];
                    $func = function ($q) use ($operator, $value) {
                        self::applyCondition($q, 'name', $operator, $value);
                    };
                    if ($operator == 'empty') {
                        $leadsQuery->doesntHave($relation);
                    } elseif ($operator == 'not_empty') {
                        $leadsQuery->whereHas($relation);
                    } else {
                        $leadsQuery->whereHas($relation, $func);
                    }
                } elseif (array_key_exists($field, $optionalRelations) && in_array($operator, ['contains', 'not_contains'])) {
                    $relation = $optionalRelations[$field];
                    if ($field === 'assigned_history') {
                        $leadsQuery->whereHas('assignmentHistories', function ($q) use ($operator, $value) {
                            if (is_numeric($value)) {
                                self::applyCondition($q, 'user_id', $operator, $value);
                            } else {
                                $q->whereHas(
                                    'user',
                                    function ($qu) use ($operator, $value) {
                                        self::applyCondition($qu, 'name', $operator, $value);
                                    }
                                );
                            }
                        });
                    } else {
                        $func = function ($q) use ($operator, $value) {
                            self::applyCondition($q, 'name', $operator, $value);
                        };
                        $leadsQuery->whereHas($relation, $func);
                    }
                } else {
                    $dbField = $columnMap[$field] ?? $field;
                    self::applyCondition($leadsQuery, 'leads.' . $dbField, $operator, $value);
                }
            }
        }

        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            
            // Limit global search to these specific columns for performance
            $searchableColumns = ['id', 'name', 'last_name', 'email', 'phone', 'mobile', 'company_name', 'gstin_number', 'pan_number'];
            
            $leadsQuery->where(function ($q) use ($searchValue, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere('leads.' . $column, 'like', "%{$searchValue}%");
                }
            });
        }

        return $leadsQuery;
    }

    public static function getDataTable($leadsQuery, $boxId)
    {

        $customFields = BoxCustomField::where('box_id', $boxId)
            ->where('object', 'leads')
            ->where('status', 'Enabled')
            ->get();

        $tableName = 'data_' . $boxId;
        $hasCustomTable = Schema::hasTable($tableName);

        // Remove any hardcoded orderBy from the base query so DataTables sorting takes full precedence
        $leadsQuery->getQuery()->orders = null;

        return DataTables::of($leadsQuery)
            ->addColumn('last_receipt_amount', function ($lead) {
                $receipt = \App\Models\Accounts\Receipt::where('lead_id', $lead->id)
                    ->whereNull('deleted_at')
                    ->latest('created_at')
                    ->first(['amount']);
                return $receipt ? (string)$receipt->amount : null;
            })
            ->addColumn('last_receipt_date', function ($lead) {
                $receipt = \App\Models\Accounts\Receipt::where('lead_id', $lead->id)
                    ->whereNull('deleted_at')
                    ->latest('created_at')
                    ->first(['receipt_date']);
                return $receipt && $receipt->receipt_date ? \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d H:i:s') : null;
            })
            ->addColumn('last_activity_date', function ($lead) {
                $activity = \App\Models\Activity::where('activity_id', $lead->id)
                    ->where('activity_date', '<', \Carbon\Carbon::now())
                    ->orderBy('activity_date', 'desc')
                    ->first(['activity_date']);
                return $activity && $activity->activity_date ? \Carbon\Carbon::parse($activity->activity_date)->format('Y-m-d H:i:s') : null;
            })
            ->addColumn('receipts_sum_amount', function ($lead) {
                $sum = \App\Models\Accounts\Receipt::where('lead_id', $lead->id)
                    ->whereNull('deleted_at')
                    ->sum('amount');
                return $sum > 0 ? (string)$sum : null;
            })
            ->addColumn('division_areas_count', function ($lead) {
                return \App\Models\DivisionArea::where('lead_id', $lead->id)->count();
            })
            // Use 'addIndexColumn' if you want client side indexing, but DataTables does it.
            // Disable 'rowId' if not needed to save bytes, but we might use it.
            ->addColumn('custom_data', function ($lead) use ($customFields, $hasCustomTable, $tableName) {
                // Return empty object immediately if no custom fields
                if ($customFields->isEmpty()) {
                    return (object) [];
                }

                // 1. Optimized Path: Check if Eager Loaded attributes exist
                // We check the first field to see if the alias 'custom_X' is loaded
                $firstField = $customFields->first();
                $testKey = 'custom_' . $firstField->col_name;

                if (array_key_exists($testKey, $lead->getAttributes())) {
                    $data = [];
                    foreach ($customFields as $field) {
                        $key = 'custom_' . $field->col_name;
                        // use direct property access which is fastest
                        $data[$field->col_name] = $lead->$key ?? null;
                    }
                    return (object) $data;
                }

                // 2. Fallback Path: Direct DB Query (Only if table exists)
                // We use DB::table which is lightweight. 
                if ($hasCustomTable) {
                    $cols = $customFields->pluck('col_name')->toArray();
                    // Add logic to avoid query if no columns?
                    if (empty($cols))
                        return (object) [];

                    $result = DB::table($tableName)->where('lead_id', $lead->id)->first($cols);
                    return $result ?: (object) [];
                }
                return (object) [];
            })
            ->addColumn('source', function ($lead) {
                return $lead->leadSource ? $lead->leadSource->name : null;
            })
            ->editColumn('industry', function ($lead) {
                return $lead->industryRelation ? $lead->industryRelation->name : null;
            })
            ->addColumn('lead_status', function ($lead) {
                return $lead->leadStatus ? $lead->leadStatus->name : null;
            })
            ->addColumn('lead_status_color', function ($lead) {
                return $lead->leadStatus ? $lead->leadStatus->color : null;
            })
            ->addColumn('lead_sub_status', function ($lead) {
                return $lead->leadSubStatus ? $lead->leadSubStatus->name : null;
            })
            ->addColumn('assignees', function ($lead) {
                return $lead->assignees->values();
            })
            ->editColumn('assigned_to', function ($lead) {
                return $lead->assignedTo ? $lead->assignedTo->name : null;
            })
            ->addColumn('assigned_history', function ($lead) {
                return $lead->assignmentHistories->map(
                    function ($h) {
                        return $h->user ? $h->user->name : null;
                    }
                )->filter()->unique()->values();
            })
            ->editColumn('last_assigned_to', function ($lead) {
                return $lead->lastAssignedTo ? $lead->lastAssignedTo->name : null;
            })
            ->editColumn('last_lead_status', function ($lead) {
                return $lead->lastLeadStatus ? $lead->lastLeadStatus->name : null;
            })
            ->editColumn('last_lead_status_color', function ($lead) {
                return $lead->lastLeadStatus ? $lead->lastLeadStatus->color : null;
            })
            ->editColumn('created_at', function ($lead) {
                return $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : '';
            })
            ->editColumn('last_updated_by', function ($lead) {
                return $lead->updatedBy ? $lead->updatedBy->name : $lead->last_updated_by;
            })
            ->editColumn('country_id', function ($lead) {
                return $lead->country ? $lead->country->name : null;
            })
            ->editColumn('state_id', function ($lead) {
                return $lead->state ? $lead->state->name : null;
            })
            ->editColumn('city_id', function ($lead) {
                return $lead->city ? $lead->city->name : null;
            })
            ->filterColumn('assigned_to', function ($query, $keyword) {
                $query->whereHas(
                    'assignedTo',
                    function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    }
                );
            })
            ->filterColumn('lead_status', function ($query, $keyword) {
                $query->whereHas(
                    'leadStatus',
                    function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    }
                );
            })
            ->filterColumn('source', function ($query, $keyword) {
                $query->whereHas(
                    'leadSource',
                    function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    }
                );
            })
            ->filterColumn('industry', function ($query, $keyword) {
                $query->whereHas(
                    'industryRelation',
                    function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    }
                );
            })
            ->orderColumn('action', function ($query, $order) {
                $query->orderBy('leads.id', $order);
            })
            ->filterColumn('last_assigned_to', function ($query, $keyword) {
                $query->whereHas(
                    'lastAssignedTo',
                    function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    }
                );
            })
            ->filterColumn('last_lead_status', function ($query, $keyword) {
                $query->whereHas(
                    'lastLeadStatus',
                    function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    }
                );
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $query->where('leads.phone', 'like', "%{$keyword}%");
            })
            ->filterColumn('email', function ($query, $keyword) {
                $query->where('leads.email', 'like', "%{$keyword}%");
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('leads.name', 'like', "%{$keyword}%");
            })
            // Ignore computed columns in global search to prevent PDOException
            ->filterColumn('checkbox', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('star', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('action', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('lead_status_color', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('last_lead_status_color', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('last_receipt_amount', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('receipts_sum_amount', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('last_receipt_date', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('division_areas_count', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('custom_data', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('reminder_description', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('upcoming_activity_date', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('activity_type_name', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('activity_mode', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('activity_status', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('last_activity_date', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('last_activity_description', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('activity_description', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('activity_date_field', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('activity_added_by_name', function ($query, $keyword) {
                // Do nothing
            })
            ->filterColumn('activity_completed_date', function ($query, $keyword) {
                // Do nothing
            })
            // Raw columns allowed
            ->rawColumns(['checkbox', 'star', 'lead_name', 'lead_id'])
            ->with([
                'total_amount' => (clone $leadsQuery)->sum('amount'),
                'total_receipts_amount' => \DB::table('receipts')
                    ->whereIn('lead_id', (clone $leadsQuery)->select('leads.id'))
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0
            ]);

        // Note: make(true) is called in the controller to handle everything.
    }

    public function toggleStar(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|integer|exists:leads,id',
        ]);

        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return response()->json(['status' => false, 'message' => 'Box user not found'], 403);
        }

        $lead = Lead::where('id', $request->lead_id)->where('box_id', $boxUser->box_id)->first();
        if (!$lead) {
            return response()->json(['status' => false, 'message' => 'Lead not found'], 404);
        }

        $star = \App\Models\LeadStar::where('user_id', $userId)->where('lead_id', $lead->id)->first();

        if ($star) {
            $star->delete();
            return response()->json([
                'status' => true,
                'message' => 'Lead star removed',
                'is_starred' => false
            ]);
        } else {
            \App\Models\LeadStar::create([
                'user_id' => $userId,
                'lead_id' => $lead->id,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Lead starred successfully',
                'is_starred' => true
            ]);
        }
    }

    public function getLeads(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = \App\Models\Box::where('slug', $boxSlug)->first();
                $boxId = $box ? $box->id : null;
            } else {
                $boxId = BoxUser::where('user_id', $userId)->value('box_id');
            }

            Log::info('Leads Fetch Started', ['user_id' => $userId, 'box_id' => $boxId]);

            if (!$boxId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box ID not found for current user.'
                ], 403);
            }

            // CLIENT-SIDE BATCH MODE SUPPORT
            // If batch_mode is requested, we assume the frontend is looping through pages.
            // We return a lightweight JSON of data to avoid DataTables overhead.
            if ($request->has('batch_mode')) {
                // Ensure limit is reasonable
                $limit = $request->input('length', 100);
                $start = $request->input('start', 0);

                // Build Query with Eager Loading (true) for efficiency
                $leadsQuery = self::buildLeadsQuery($boxId, $request->all(), true);

                $leadsQuery->skip($start)->take($limit);
                $leads = $leadsQuery->get();

                $customFields = BoxCustomField::where('box_id', $boxId)
                    ->where('object', 'leads')
                    ->get();

                $transformed = [];
                /** @var \App\Models\Lead[] $leads */
                foreach ($leads as $lead) {
                    $row = $lead->toArray();
                    $row['source'] = $lead->leadSource ? $lead->leadSource->name : null;
                    $row['lead_status'] = $lead->leadStatus ? $lead->leadStatus->name : null;
                    $row['lead_status_color'] = $lead->leadStatus ? $lead->leadStatus->color : null;
                    $row['assigned_to'] = $lead->assignedTo ? $lead->assignedTo->name : null;
                    $row['last_assigned_to'] = $lead->lastAssignedTo ? $lead->lastAssignedTo->name : null;
                    $row['last_lead_status'] = $lead->lastLeadStatus ? $lead->lastLeadStatus->name : null;
                    $row['last_lead_status_color'] = $lead->lastLeadStatus ? $lead->lastLeadStatus->color : null;
                    $row['industry'] = $lead->industryRelation ? $lead->industryRelation->name : null;
                    $row['last_updated_by'] = $lead->updatedBy ? $lead->updatedBy->name : ($lead->last_updated_by ?? null);
                    $row['country_id'] = $lead->country ? $lead->country->name : null;
                    $row['state_id'] = $lead->state ? $lead->state->name : null;
                    $row['city_id'] = $lead->city ? $lead->city->name : null;

                    $cData = [];
                    foreach ($customFields as $field) {
                        $key = 'custom_' . $field->col_name;
                        $cData[$field->col_name] = $lead->$key ?? null;
                    }
                    $row['custom_data'] = (object) $cData;
                    $row['custom_data'] = (object) $cData;
                    $transformed[] = $row;
                }

                // Ensure no stray output interferes with JSON
                if (ob_get_length())
                    ob_clean();

                return response()->json([
                    'status' => 'success',
                    'data' => $transformed
                ], 200, [], JSON_INVALID_UTF8_IGNORE);
            }

            // Resource optimization: DataTables operations can be memory intensive even for small pages
            // due to query building, eager loading, and complex custom column processing.
            ini_set('memory_limit', '1024M');
            set_time_limit(300);

            // Optimization: Use eager loading (third param true) to prevent N+1 queries 
            // even in standard DataTables mode. This significantly improves performance 
            // for boxes with many custom fields and prevents JSON/Timeout errors.
            $leadsQuery = self::buildLeadsQuery($boxId, $request->all(), true);

            $dataTable = self::getDataTable($leadsQuery, $boxId);

            // Explicitly clear any existing output buffer to prevent "Invalid JSON" warnings
            // caused by stray warnings or spaces added by other scripts/files.
            if (ob_get_length())
                ob_clean();

            $response = $dataTable->make(true);

            // Check for potential encoding errors - use JSON_INVALID_UTF8_IGNORE for safety
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $response->setEncodingOptions($response->getEncodingOptions() | JSON_INVALID_UTF8_IGNORE);
            }

            return $response;

        } catch (\Throwable $th) {
            Log::error('DataTables Fetch Error: ' . $th->getMessage(), [
                'exception' => $th,
                'request' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Error fetching leads: ' . $th->getMessage(),
                'error' => $th->getTraceAsString(),
            ], 500);
        }
    }

    public function getLeadsAll(Request $request)
    {
        $request->merge(['all' => 'true']);
        return $this->getLeads($request);
    }

    public function getLeadsTrash(Request $request)
    {
        $request->merge(['trashed' => 'true']);
        return $this->getLeads($request);
    }

    public function getLeadsDuplicate(Request $request)
    {
        $request->merge(['duplicate' => 'true']);
        return $this->getLeads($request);
    }

    public function getReminderLeads(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = \App\Models\Box::where('slug', $boxSlug)->first();
                $boxId = $box ? $box->id : null;
            } else {
                $boxId = BoxUser::where('user_id', $userId)->value('box_id');
            }
            if (!$boxId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            // Using separate builder for Reminder List
            $leadsQuery = self::buildReminderLeadsQuery($boxId, $request->all());

            // Use standard DataTable response generator
            $dataTable = self::getDataTable($leadsQuery, $boxId);
            return $dataTable->make(true);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching reminder leads.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public static function buildReminderLeadsQuery($boxId, $params)
    {
        // Start with base leads query (same relationships as standard list)
        $leadsQuery = Lead::withTrashed()
            ->where('leads.box_id', $boxId)
            ->with([
                'country:id,name',
                'state:id,name',
                'city:id,name',
                'assignedTo:id,name',
                'leadStatus:id,name,color',
                'leadSource:id,name,box_id',
                'industryRelation:id,name,box_id',
                'updatedBy:id,name',
                'lastAssignedTo:id,name',
                'lastLeadStatus:id,name,color',
                'leadSubStatus:id,name',
                'assignees:id,name',
                'assignmentHistories.user:id,name'
            ])
            ->select('leads.*');


        // JOIN with Activities Table
        // We join to filter leads based on their scheduled activities
        $leadsQuery->join('activities', 'leads.id', '=', 'activities.activity_id')
            ->where('activities.box_id', $boxId)
            ->whereNull('activities.deleted_at')
            ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note'])
            ->where('activities.status', 'Open');

        $filter = $params['reminder_filter'] ?? 'today';
        $today = Carbon::now()->format('Y-m-d');

        $leadsQuery->leftJoin('activity_types', 'activities.activity_type', '=', 'activity_types.id');

        // Select Reminder Description, Date and Status
        $leadsQuery->addSelect([
            'activities.description as reminder_description',
            'activities.activity_date as upcoming_activity_date',
            'activity_types.activity_type as activity_type_name',
            'activities.type as activity_mode',
            'activities.status as activity_status'
        ]);



        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $startDate = $params['start_date'] . ' 00:00:00';
            $endDate = $params['end_date'] . ' 23:59:59';
            $leadsQuery->whereBetween('activities.activity_date', [$startDate, $endDate]);
            // If status is passed (e.g. from the dashboard), apply it. Otherwise use the default 'Open' which is already applied on line 1615.
        } else {
            switch ($filter) {
                case 'pending':
                    $leadsQuery->where('activities.activity_date', '<', $today . ' 00:00:00')
                        ->orderBy('activities.activity_date', 'desc');
                    break;
                case 'today':
                    $leadsQuery->whereBetween('activities.activity_date', [$today . ' 00:00:00', $today . ' 23:59:59'])
                               ->where('activities.status', 'Open');
                    break;
            case 'this_month':
                // This Month: From start of month to end of month
                $leadsQuery->whereBetween('activities.activity_date', [
                    Carbon::now()->startOfMonth()->toDateString() . ' 00:00:00',
                    Carbon::now()->endOfMonth()->toDateString() . ' 23:59:59'
                ])
                    ->orderBy('activities.activity_date', 'asc');
                break;
            case 'upcoming':
                // Upcoming: Date > Today
                $leadsQuery->where('activities.activity_date', '>', $today . ' 23:59:59')
                    ->orderBy('activities.activity_date', 'asc');
                break;
            case 'all':
                // All: No date filter and no status filter
                $leadsQuery->orderBy('activities.activity_date', 'desc');
                break;
            default:
                // Default to Upcoming if unknown
                $leadsQuery->where('activities.activity_date', '>', $today . ' 23:59:59');
                break;
        }
        }

        // Apply Advanced Filter
        if (isset($params['advanced_filter']) && !empty($params['advanced_filter'])) {
            $filters = $params['advanced_filter'];
            if (isset($filters['field'])) {
                $filters = [$filters];
            }

            $activityDateFilters = [];
            $remainingFilters = [];

            foreach ($filters as $filter) {
                if (empty($filter['field'])) continue;
                if ($filter['field'] === 'upcoming_activity_date' || $filter['field'] === 'last_activity_date') {
                    $activityDateFilters[] = $filter;
                } else {
                    $remainingFilters[] = $filter;
                }
            }

            if (!empty($activityDateFilters)) {
                $leadsQuery->where(function ($q) use ($activityDateFilters) {
                    foreach ($activityDateFilters as $index => $filter) {
                        $field = $filter['field'];
                        $operator = $filter['operator'] ?? 'contains';
                        $value = $filter['value'] ?? '';

                        $q->{$index == 0 ? 'where' : 'orWhere'}(function ($subQ) use ($field, $operator, $value) {
                            if ($field === 'upcoming_activity_date') {
                                self::applyCondition($subQ, 'activities.activity_date', $operator, $value);
                                $subQ->where('activities.status', 'Open');
                            } elseif ($field === 'last_activity_date') {
                                self::applyCondition($subQ, 'activities.activity_date', $operator, $value);
                                $subQ->whereIn('activities.status', ['Close', 'Completed', 'Closed']);
                            }
                        });
                    }
                });
            }

            foreach ($remainingFilters as $filter) {
                $field = $filter['field'];
                $operator = $filter['operator'] ?? 'contains';
                $value = $filter['value'] ?? '';

                // Specifically handle 'status' or 'activity_status' for the joined activities table
                if ($field === 'status' || $field === 'activity_status') {
                    if ($operator === 'equals' && !is_array($value) && (strtolower($value) === 'closed' || strtolower($value) === 'close')) {
                        $leadsQuery->whereIn('activities.status', ['Close', 'Completed', 'Closed']);
                    } else {
                        self::applyCondition($leadsQuery, 'activities.status', $operator, $value);
                    }
                } elseif ($field === 'completed_date') {
                    self::applyCondition($leadsQuery, 'activities.completed_date', $operator, $value);
                } elseif ($field === 'activity_setting_name' || $field === 'activity_setting') {
                    if (!is_array($value) && (strtolower($value) === 'all' || $value === '')) {
                        // Do not apply any condition, show all
                    } else {
                        self::applyCondition($leadsQuery, 'activity_types.activity_setting_id', $operator, $value);
                    }
                } elseif ($field === 'activity_type_name' || $field === 'activity_type') {
                    if (!is_array($value) && (strtolower($value) === 'all' || $value === '')) {
                        // Do not apply any condition, show all
                    } elseif (is_numeric($value)) {
                        self::applyCondition($leadsQuery, 'activities.activity_type', $operator, $value);
                    } else {
                        self::applyCondition($leadsQuery, 'activity_types.activity_type', $operator, $value);
                    }
                } else {
                    // Map common lead fields to their DB counterparts
                    $columnMap = [
                        'lead_name' => 'name',
                        'value' => 'amount',
                        'lead_status' => 'lead_status_id',
                        'lead_status_id' => 'lead_status_id',
                        'lead_source_id' => 'lead_source_id',
                        'assigned_to' => 'assigned_to',
                        'owner' => 'assigned_to',
                    ];
                    $dbField = $columnMap[$field] ?? $field;
                    self::applyCondition($leadsQuery, 'leads.' . $dbField, $operator, $value);
                }
            }
        }

        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('leads');
            $leadsQuery->where(function ($q) use ($searchValue, $columns) {
                foreach ($columns as $column) {
                    if (!in_array($column, ['id', 'box_id', 'assigned_to', 'added_by', 'last_updated_by', 'created_at', 'updated_at', 'deleted_at'])) {
                        $q->orWhere('leads.' . $column, 'like', "%{$searchValue}%");
                    }
                }
            });
        }

        // distinct() removed for performance optimization

        return $leadsQuery;
    }

    public function getDSRLeads(Request $request)
    {
        try {
            $boxId = BoxUser::where('user_id', Auth::id())
                ->value('box_id');
            if (!$boxId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            // Using separate builder for DSR (Daily Sales Report)
            $leadsQuery = self::buildDSRLeadsQuery($boxId, $request->all());

            \Illuminate\Support\Facades\Log::info("getDSRLeads payload:", $request->all());

            // Use standard DataTable response generator
            $dataTable = self::getDataTable($leadsQuery, $boxId);
            return $dataTable->make(true);

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error("getDSRLeads error: " . $th->getMessage());
            \Illuminate\Support\Facades\Log::error($th->getTraceAsString());
            return response()->json([
                'status' => false,
                'message' => 'Error fetching DSR leads.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public static function buildDSRLeadsQuery($boxId, $params)
    {
        // Start with base leads query
        $leadsQuery = Lead::withTrashed()
            ->where('leads.box_id', $boxId)
            ->with([
                'country:id,name',
                'state:id,name',
                'city:id,name',
                'assignedTo:id,name',
                'leadStatus:id,name,color',
                'leadSource:id,name,box_id',
                'industryRelation:id,name,box_id',
                'updatedBy:id,name',
                'lastAssignedTo:id,name',
                'lastLeadStatus:id,name,color',
                'leadSubStatus:id,name',
                'assignees:id,name',
                'assignmentHistories.user:id,name'
            ])
            ->select('leads.*');

        // Removed withSum and addSelect subqueries for performance.
        // DataTables executes these for every row during the COUNT(*) query, causing massive slowdowns.


        // JOIN with Activities Table
        // For DSR, we want the specific activity details that drove this report
        // Filter: Activity type and Activity lead status must NOT be null
        $leadsQuery->join('activities', 'leads.id', '=', 'activities.activity_id')
            ->where('activities.box_id', $boxId)
            ->whereNull('activities.deleted_at')
            ->whereIn('activities.type', ['task', 'call', 'email', 'meeting', 'whatsapp', 'note'])
            ->where(function ($query) {
                $query->where('activities.status', 'Open')
                      ->orWhere(function ($q) {
                          $q->whereIn('activities.status', ['Close', 'Completed', 'Closed']);
                      });
            });

        // Try to join activity_types to get the name, assuming activity_type column in activities is an ID
        $leadsQuery->leftJoin('activity_types', 'activities.activity_type', '=', 'activity_types.id');

        $filter = $params['reminder_filter'] ?? 'today';
        $today = Carbon::now()->format('Y-m-d');

        // Select specific DSR fields
        $leadsQuery->addSelect([
            'activities.description as reminder_description', // The description request
            'activities.activity_date as upcoming_activity_date', // The date
            'activities.completed_date as activity_completed_date', // Activity Done Date
            'activity_types.activity_type as activity_type_name', // The activity type name
            'activities.type as activity_mode', // e.g. Call, Meeting if stored here
            'activities.status as activity_status' // The status
        ]);

        // Add Last Activity Date and Description Subqueries
        // Removed for performance. If needed, these should be added via eager loading or DataTable addColumn.

        
        // 3. Process Advanced Filters (from our dynamic filter system)
        $includeFailed = false;
        if (!empty($params['advanced_filter']) && is_array($params['advanced_filter'])) {
            $activityDateFilters = [];
            $remainingFilters = [];

            foreach ($params['advanced_filter'] as $f) {
                if (empty($f['field'])) continue;
                if ($f['field'] === 'upcoming_activity_date' || $f['field'] === 'last_activity_date') {
                    $activityDateFilters[] = $f;
                } else {
                    $remainingFilters[] = $f;
                }
            }

            if (!empty($activityDateFilters)) {
                $leadsQuery->where(function ($q) use ($activityDateFilters) {
                    foreach ($activityDateFilters as $index => $filter) {
                        $field = $filter['field'];
                        $operator = $filter['operator'] ?? 'contains';
                        $value = $filter['value'] ?? '';

                        $q->{$index == 0 ? 'where' : 'orWhere'}(function ($subQ) use ($field, $operator, $value) {
                            if ($field === 'upcoming_activity_date') {
                                self::applyCondition($subQ, 'activities.activity_date', $operator, $value);
                                $subQ->where('activities.status', 'Open');
                            } elseif ($field === 'last_activity_date') {
                                self::applyCondition($subQ, 'activities.activity_date', $operator, $value);
                                $subQ->whereIn('activities.status', ['Close', 'Completed', 'Closed']);
                            }
                        });
                    }
                });
            }

            foreach ($remainingFilters as $f) {
                $field = $f['field'];
                $operator = $f['operator'] ?? 'contains';
                $value = $f['value'] ?? null;

                // Specifically handle 'status' or 'activity_status' for the joined activities table
                if ($field === 'status' || $field === 'activity_status') {
                    if ($operator === 'equals' && !is_array($value) && (strtolower($value) === 'closed' || strtolower($value) === 'close')) {
                        $leadsQuery->whereIn('activities.status', ['Close', 'Completed', 'Closed']);
                    } else {
                        self::applyCondition($leadsQuery, 'activities.status', $operator, $value);
                    }
                } elseif ($field === 'completed_date') {
                    self::applyCondition($leadsQuery, 'activities.completed_date', $operator, $value);
                } elseif ($field === 'activity_setting_name' || $field === 'activity_setting') {
                    if (!is_array($value) && (strtolower($value) === 'all' || $value === '')) {
                        // Do not apply any condition, show all
                    } else {
                        self::applyCondition($leadsQuery, 'activity_types.activity_setting_id', $operator, $value);
                    }
                } elseif ($field === 'activity_type_name' || $field === 'activity_type') {
                    if (!is_array($value) && (strtolower($value) === 'all' || $value === '')) {
                        // Do not apply any condition, show all
                    } elseif (is_numeric($value)) {
                        self::applyCondition($leadsQuery, 'activities.activity_type', $operator, $value);
                    } else {
                        self::applyCondition($leadsQuery, 'activity_types.activity_type', $operator, $value);
                    }
                } elseif ($field === 'last_activity_description') {
                    if (!is_array($value) && (strtolower($value) == 'call or meeting' || strtolower($value) == 'call' || strtolower($value) == 'meeting')) {
                        $leadsQuery->whereIn('activities.type', ['call', 'meeting']);
                    } else {
                        self::applyCondition($leadsQuery, 'activities.description', $operator, $value);
                    }
                } elseif ($field === 'task_timing') {
                    $box = \App\Models\Box::find($boxId);
                    $lastTaskCalcTime = $box->last_task_calculation_time ?? '00:30';
                    if (substr_count($lastTaskCalcTime, ':') == 1) {
                        $lastTaskCalcTime .= ':00';
                    }
                    if ($value === 'late') {
                        $leadsQuery->whereNotNull('activities.closed_by')
                                   ->whereRaw('COALESCE(activities.completed_date, activities.updated_at) > ADDTIME(activities.activity_date, TIME(?))', [$lastTaskCalcTime]);
                    } elseif ($value === 'on_time') {
                        $leadsQuery->whereNotNull('activities.closed_by')
                                   ->whereRaw('COALESCE(activities.completed_date, activities.updated_at) <= ADDTIME(activities.activity_date, TIME(?))', [$lastTaskCalcTime]);
                    } elseif ($value === 'failed') {
                        $includeFailed = true;
                        $leadsQuery->whereNull('activities.closed_by');
                    }
                } else {
                    // Map common lead fields to their DB counterparts
                    $columnMap = [
                        'lead_name' => 'name',
                        'value' => 'amount',
                        'lead_status' => 'lead_status_id',
                        'lead_status_id' => 'lead_status_id',
                        'lead_source_id' => 'lead_source_id',
                        'assigned_to' => 'assigned_to',
                        'owner' => 'assigned_to',
                    ];
                    $dbField = $columnMap[$field] ?? $field;
                    self::applyCondition($leadsQuery, 'leads.' . $dbField, $operator, $value);
                }
            }
        }

        switch ($filter) {
            case 'pending':
                // Pending: Date < Today AND Status != Completed
                $leadsQuery->where('activities.activity_date', '<', $today . ' 00:00:00')
                    ->where('activities.status', 'Open');
                break;
            case 'today':
                // Today: Date == Today (Completed Date)
                $leadsQuery->whereNotNull('activities.completed_date')
                           ->whereBetween('activities.completed_date', [$today . ' 00:00:00', $today . ' 23:59:59']);
                break;
            case 'this_month':
                // This Month: From start of month to end of month (Completed Date)
                $leadsQuery->whereNotNull('activities.completed_date')
                           ->whereBetween('activities.completed_date', [
                    Carbon::now()->startOfMonth()->format('Y-m-d'),
                    Carbon::now()->endOfMonth()->format('Y-m-d')
                ])
                    ->orderBy('activities.completed_date', 'asc');
                break;
            case 'upcoming':
                // Upcoming: Date > Today
                $leadsQuery->where('activities.activity_date', '>', $today . ' 23:59:59')
                    ->orderBy('activities.activity_date', 'asc');
                break;
            case 'all':
                $leadsQuery->orderBy('activities.activity_date', 'desc');
                break;
            default:
                // Default to Today for DSR usually (Completed Date)
                $leadsQuery->whereNotNull('activities.completed_date')
                           ->whereBetween('activities.completed_date', [$today . ' 00:00:00', $today . ' 23:59:59']);
                break;
        }
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $leadsQuery->where(function ($q) use ($searchValue) {
                $q->orWhere('leads.name', 'like', "%{$searchValue}%")
                  ->orWhere('leads.phone', 'like', "%{$searchValue}%")
                  ->orWhere('leads.email', 'like', "%{$searchValue}%")
                  ->orWhere('leads.company_name', 'like', "%{$searchValue}%")
                  ->orWhere('activities.description', 'like', "%{$searchValue}%");
            });
        }
        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $startDate = $params['start_date'] . ' 00:00:00';
            $endDate = $params['end_date'] . ' 23:59:59';
            $leadsQuery->whereBetween('activities.completed_date', [$startDate, $endDate]);
        }

        if (!$includeFailed) {
            $leadsQuery->where(function ($q) {
                $q->where('activities.status', 'Open')
                  ->orWhereNotNull('activities.closed_by');
            });
        }

        // distinct() removed for performance optimization
        return $leadsQuery;
    }



    public function getActivityLeads(Request $request)
    {
        try {
            $boxId = BoxUser::where('user_id', Auth::id())->value('box_id');
            if (!$boxId) {
                return response()->json(['status' => false, 'message' => 'Box not found.'], 404);
            }

            $leadsQuery = self::buildAllActivityLeadsQuery($boxId, $request->all());
            $dataTable = self::getDataTable($leadsQuery, $boxId);

            return $dataTable->addColumn('activity_added_by', function ($lead) {
                return $lead->activity_added_by_name ?? '--';
            })->make(true);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching activity leads.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function buildAllActivityLeadsQuery($boxId, $params)
    {
        $leadsQuery = Lead::where('leads.box_id', $boxId)
            ->with([
                'assignedTo:id,name',
                'leadStatus:id,name,color',
                'leadSource:id,name',
                'leadSubStatus:id,name',
                'country:id,name',
                'state:id,name',
                'city:id,name'
            ])
            ->select('leads.*');



        $leadsQuery->join('activities', 'leads.id', '=', 'activities.activity_id')
            ->leftJoin('activity_types', 'activities.activity_type', '=', 'activity_types.id')
            ->leftJoin('users', 'activities.added_by', '=', 'users.id')
            ->where('activities.box_id', $boxId)
            ->whereNull('activities.deleted_at');

        $leadsQuery->addSelect([
            'activities.description as activity_description',
            'activities.activity_date as activity_date_field',
            'activity_types.activity_type as activity_type_name',
            'users.name as activity_added_by_name'
        ]);

        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('leads');
            $leadsQuery->where(function ($q) use ($searchValue, $columns) {
                foreach ($columns as $column) {
                    if (!in_array($column, ['id', 'box_id', 'assigned_to', 'added_by', 'last_updated_by', 'created_at', 'updated_at', 'deleted_at'])) {
                        $q->orWhere('leads.' . $column, 'like', "%{$searchValue}%");
                    }
                }
                $q->orWhere('activities.description', 'like', "%{$searchValue}%");
            });
        }

        $leadsQuery->orderBy('activities.created_at', 'desc');

        return $leadsQuery;
    }

    public function LeadsPageDetails(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
            }

            $box_slug = $request->route('box_slug');

            // Try to find the box user associated with the slug if provided, otherwise fallback to first available
            $boxUserQuery = BoxUser::where('user_id', $userId);
            if ($box_slug) {
                $box = \App\Models\Box::where('slug', $box_slug)->first();
                if ($box) {
                    $boxUserQuery->where('box_id', $box->id);
                }
            }
            $boxUser = $boxUserQuery->first();

            if (!$boxUser || !$boxUser->box_id) {
                return redirect()->back()->with('error', 'Box not found for current user or you do not have permission to access this box.');
            }

            // Auto-sync system fields for this box if any are missing
            \App\Console\Commands\SyncMissingSystemFields::syncForBox($boxUser->box_id);

            // Fetch dynamic fields for leads
            $fields = SystemField::where('object', 'leads')
                ->where('box_id', $boxUser->box_id)
                ->where('is_visible', true)
                ->orderBy('sequence')
                ->get();

            $leadSources = \App\Models\CrmSource::where('box_id', $boxUser->box_id)
                ->select('id', 'name')
                ->get();
            $industries = \App\Models\Industry::where('box_id', $boxUser->box_id)
                ->select('id', 'name')
                ->get();
            $leadsStatus = \App\Models\ContactStage::where('box_id', $boxUser->box_id)
                ->select('id', 'name', 'reminder_mandatory')
                ->get();
            $countries = \App\Models\Country::select('id', 'name')->get();
            $owners = BoxUser::where('box_id', $boxUser->box_id)->with('user:id,name')->get()->map(function ($boxUser) {
                if ($boxUser->user) {
                    $boxUser->user->department_id = $boxUser->department;
                    $boxUser->user->designation_id = $boxUser->designation;
                    return $boxUser->user;
                }
                return null;
            })->filter();

            $customFields = BoxCustomField::where('box_id', $boxUser->box_id)
                ->where('object', 'leads')
                ->where('status', 'Enabled')
                ->orderBy('ordering')
                ->get();

            $activitySettings = \App\Models\Call::where('box_id', $boxUser->box_id)
                ->where('status', 1)
                ->select('id', 'name as title')
                ->get();

            $activityTypes = \App\Models\ActivityType::where('box_id', $boxUser->box_id)->where('status', 'Active')->get();
            $activityStatus = \App\Models\ActivityStatus::where('box_id', $boxUser->box_id)->where('status', 'Active')->get();

            $leadId = $request->input('lead_id');
            $bulkUploadId = $request->input('bulk_upload_id');
            $lead = null;
            $prevLeadId = null;
            $nextLeadId = null;
            $paginationText = "1 of 1";

            if ($leadId) {
                // Find lead including trashed ones. This handles viewing details from Trash.
                $lead = \App\Models\Lead::withTrashed()->where('id', $leadId)
                    ->where('box_id', $boxUser->box_id)
                    ->with('assignees', 'leadStatus', 'leadSource', 'country', 'state', 'city', 'leadSubStatus', 'statusHistories')
                    ->first();

                if (!$lead) {
                    return redirect()->back()->with('error', 'Lead not found or you do not have permission to view it.');
                }

                if ($bulkUploadId) {
                    $allLeadIds = \App\Models\Lead::withTrashed()
                        ->where('box_id', $boxUser->box_id)
                        ->where('bulk_upload_id', $bulkUploadId)
                        ->orderBy('id', 'desc')
                        ->pluck('id')
                        ->toArray();

                    $total = count($allLeadIds);
                    if ($total > 0) {
                        $currentIndex = array_search($leadId, $allLeadIds);
                        if ($currentIndex !== false) {
                            $paginationText = ($currentIndex + 1) . " of " . $total;
                            if ($currentIndex > 0) {
                                $prevLeadId = $allLeadIds[$currentIndex - 1]; // Newer
                            }
                            if ($currentIndex < $total - 1) {
                                $nextLeadId = $allLeadIds[$currentIndex + 1]; // Older
                            }
                        }
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Lead ID is required.');
            }

            $assignmentHistory = \App\Models\LeadAssignmentHistory::where('lead_id', $leadId)
                ->with(['user', 'assignedBy'])
                ->orderBy('created_at', 'desc')
                ->get();

            $boxUsers = \App\Models\BoxUser::where('box_id', $boxUser->box_id)
                ->with(['departmentRelation', 'designationRelation'])
                ->get()
                ->keyBy('user_id');

            $connectedEmails = \App\Models\GmailInbox::where('box_id', $boxUser->box_id)
                ->where('user_id', $userId)
                ->where('status', 'Enabled')
                ->get();

            $whatsAppTemplates = \App\Models\WhatsAppTemplate::where('box_id', $boxUser->box_id)
                ->where('whats_app_status', 'APPROVED')
                ->get();

            return view('frontend.leads.leadsdetails', compact(
                'lead',
                'leadId',
                'bulkUploadId',
                'prevLeadId',
                'nextLeadId',
                'paginationText',
                'fields',
                'leadSources',
                'industries',
                'countries',
                'owners',
                'leadsStatus',
                'customFields',
                'activityTypes',
                'activityStatus',
                'activitySettings',
                'assignmentHistory',
                'boxUsers',
                'connectedEmails',
                'whatsAppTemplates'
            ))->with('users', $owners)->with('leadStatus', $leadsStatus);
        } catch (\Exception $e) {
            Log::error('Error in LeadsPageDetails: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading lead details: ' . $e->getMessage());
        }
    }

    public function leadscompanydetails()
    {
        return view('frontend.leads.companydetails');
    }
    public function GetLeadsDetails(Request $request)
    {
        try {
            $lead_id = $request->input('lead_id');

            $boxUser = BoxUser::where('user_id', Auth::id())->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.',
                ], 404);
            }
            $boxId = $boxUser->box_id;

            // Check if lead exists and belongs to the box
            $lead = Lead::withTrashed()->where('id', $lead_id)
                ->where('box_id', $boxId)
                ->with('country', 'state', 'city', 'leadSource', 'leadStatus', 'assignedTo', 'updatedBy', 'leadSubStatus', 'assignees', 'statusHistories', 'divisionAreas.city', 'divisionAreas.state', 'divisionAreas.division', 'divisionAreas.division.company', 'latestReceipt')
                ->withSum('receipts', 'amount')
                ->withCount('divisionAreas')
                ->first();

            if (!$lead) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lead not found or you do not have access to it.',
                ], 404);
            }

            // Get industry name if industry is numeric (ID)
            if (isset($lead->industry) && is_numeric($lead->industry)) {
                $industry = \App\Models\Industry::where('id', $lead->industry)
                    ->where('box_id', $boxId)
                    ->first();
                if ($industry) {
                    $lead->industry_name = $industry->name;
                }
            }

            // Get all historical assignees from history table
            $historyAssigneeIds = LeadAssignmentHistory::where('lead_id', $lead_id)->pluck('user_id')->toArray();
            $currentAssigneeIds = $lead->assignees->pluck('id')->toArray();
            $allAssigneeIds = array_unique(array_merge($historyAssigneeIds, $currentAssigneeIds));
            
            $allAssignees = User::whereIn('id', $allAssigneeIds)->get();

            // Replace lead->assignees with the full list for the purpose of the API response
            $lead->setRelation('assignees', $allAssignees);

            // Enrich each assignee with name and image from box_user
            $lead->assignees->each(function ($member) use ($boxId) {
                $assignedBoxUser = BoxUser::where('user_id', $member->id)
                    ->where('box_id', $boxId)
                    ->with(['departmentRelation', 'designationRelation'])
                    ->first();
                if ($assignedBoxUser) {
                    $fullName = trim(($assignedBoxUser->first_name ?? '') . ' ' . ($assignedBoxUser->last_name ?? ''));
                    if ($fullName) {
                        $member->name = $fullName;
                    }
                    $member->image = $assignedBoxUser->profile_image_path ? asset($assignedBoxUser->profile_image_path) : null;
                    $member->department = $assignedBoxUser->departmentRelation->name ?? null;
                    $member->designation = $assignedBoxUser->designationRelation->name ?? null;
                }
            });

            // Get assigned_to user name and image from box_user if assigned_to exists
            if ($lead->assigned_to) {
                $assignedBoxUser = BoxUser::where('user_id', $lead->assigned_to)
                    ->where('box_id', $boxId)
                    ->first();

                if ($assignedBoxUser) {
                    $fullName = trim(($assignedBoxUser->first_name ?? '') . ' ' . ($assignedBoxUser->last_name ?? ''));
                    $lead->assigned_to_name = $fullName ?: ($lead->assignedTo?->name ?? 'User ID: ' . $lead->assigned_to);
                    if ($assignedBoxUser->profile_image_path) {
                        $lead->assigned_to_image = asset('storage/' . $assignedBoxUser->profile_image_path);
                    } else {
                        $lead->assigned_to_image = null;
                    }
                } elseif ($lead->assignedTo) {
                    $lead->assigned_to_name = $lead->assignedTo?->name;
                    $lead->assigned_to_image = null;
                } else {
                    $lead->assigned_to_name = 'User ID: ' . $lead->assigned_to;
                }
            }

            // Get updated_by user name and image from box_user if last_updated_by exists
            if ($lead->last_updated_by) {
                $updatedBoxUser = BoxUser::where('user_id', $lead->last_updated_by)
                    ->where('box_id', $boxId)
                    ->first();

                if ($updatedBoxUser) {
                    $fullName = trim(($updatedBoxUser->first_name ?? '') . ' ' . ($updatedBoxUser->last_name ?? ''));
                    $lead->updated_by_name = $fullName ?: ($lead->updatedBy?->name ?? ($lead->last_updated_by ? 'User ID: ' . $lead->last_updated_by : null));
                    if ($updatedBoxUser->profile_image_path) {
                        $lead->updated_by_image = asset('storage/' . $updatedBoxUser->profile_image_path);
                    } else {
                        $lead->updated_by_image = null;
                    }
                } elseif ($lead->updatedBy) {
                    $lead->updated_by_name = $lead->updatedBy?->name;
                    $lead->updated_by_image = null;
                } else {
                    $lead->updated_by_name = 'User ID: ' . $lead->last_updated_by;
                }
            }

            $tableName = 'data_' . $boxId;

            $customFields = BoxCustomField::where('box_id', $boxId)
                ->where('object', 'leads')
                ->where('status', 'Enabled')
                ->pluck('col_name')
                ->toArray();

            // Add standard custom fields that are always saved to custom_data
            $standardCustomFields = ['tags', 'query', 'visibility', 'status', 'last_name', 'currency', 'phone_type', 'industry'];
            $allCustomFields = array_unique(array_merge($customFields, $standardCustomFields));

            $customData = [];
            if (Schema::hasTable($tableName)) {
                // Get all columns except lead_id, created_at, updated_at
                $columns = Schema::getColumnListing($tableName);
                $columnsToSelect = array_diff($columns, ['id', 'lead_id', 'created_at', 'updated_at']);

                if (!empty($columnsToSelect)) {
                    $customData = DB::table($tableName)
                        ->where('lead_id', $lead_id)
                        ->select($columnsToSelect)
                        ->first();
                }
            }

            // Format created_at and updated_at using Carbon
            $lead->formatted_created_at = Carbon::parse($lead->created_at)
                ->timezone('Asia/Kolkata')
                ->format('d M Y, h:i A');

            $lead->formatted_updated_at = Carbon::parse($lead->updated_at)
                ->timezone('Asia/Kolkata')
                ->format('d M Y, h:i A');

            // Fetch all Lead Stages for the dynamic pipeline
            $pipelineStages = \App\Models\ContactStage::where('box_id', $boxId)
                ->orderBy('id', 'asc') // Assuming ID order reflects the pipeline steps
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'lead' => $lead,
                    'custom_data' => $customData,
                    'pipeline_stages' => $pipelineStages,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch lead details.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getActivityTypeForActivity(Request $request)
    {
        try {
            $boxUser = BoxUser::where('user_id', Auth::id())->first();
            $boxId = $boxUser->box_id;

            $calls = Call::where('box_id', $boxId)
                ->with(['activityTypes.activityStatuses'])
                ->get()
                ->map(function ($call) {
                    $activityTypes = $call->activityTypes->map(
                        function ($type) {
                            return [
                                'id' => $type->id,
                                'activity_setting_id' => $type->activity_setting_id,
                                'box_id' => $type->box_id,
                                'activity_type' => $type->activity_type,
                                'status' => $type->status,
                                'created_at' => $type->created_at,
                                'updated_at' => $type->updated_at,
                                'activity_statuses' => $type->activityStatuses->map(
                                    function ($status) {
                                        return [
                                            'id' => $status->id,
                                            'activity_status' => $status->activity_status,
                                            'status' => $status->status,
                                            'created_at' => $status->created_at,
                                            'updated_at' => $status->updated_at,
                                        ];
                                    }
                                ),
                            ];
                        }
                    );

                    return [
                        'id' => $call->id,
                        'box_id' => $call->box_id,
                        'name' => $call->name,
                        'status' => $call->status,
                        'created_by' => $call->created_by,
                        'updated_by' => $call->updated_by,
                        'deleted_by' => $call->deleted_by,
                        'created_at' => $call->created_at,
                        'updated_at' => $call->updated_at,
                        'activity_types' => $activityTypes,
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => $calls,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch activity data.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getColumnPreferences(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxUser = BoxUser::where('user_id', $userId)->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            $boxId = $boxUser->box_id;

            // Check if user has any preferences, if not, initialize defaults
            $existingPreferences = TableColumnPreference::where('box_id', $boxId)
                ->where('user_id', $userId)
                ->where('table_name', 'leads')
                ->count();

            if ($existingPreferences == 0) {
                $this->initializeDefaultColumnPreferences($boxId, $userId);
            }

            // Sync to ensure all system fields and custom columns are in preferences
            $this->syncCustomColumns($boxId, $userId);

            $preferences = TableColumnPreference::where('box_id', $boxId)
                ->where('user_id', $userId)
                ->where('table_name', 'leads')
                ->orderBy('order', 'asc')
                ->get()
                ->filter(function ($pref) {
                    return $pref->column_name !== 'last_name';
                })
                ->map(function ($pref) {
                    $displayName = $pref->display_name;
                    if ($pref->column_name === 'created_at') $displayName = 'Created On';
                    if ($pref->column_name === 'updated_at') $displayName = 'Updated On';
                    if ($pref->column_name === 'activity_type_name') $displayName = 'Activity Type';
                    if ($pref->column_name === 'sec_email') $displayName = 'Sec Email';
                    if ($pref->column_name === 'phone_type') $displayName = 'Phone Type';
                    if ($pref->column_name === 'lead_type') $displayName = 'Lead Type';
                    if ($pref->column_name === 'company_name') $displayName = 'Company Name';
                    if ($pref->column_name === 'lead_sub_status_id') $displayName = 'Lead Sub Status';
                    if ($pref->column_name === 'postal_code') $displayName = 'Postal Code';
                    if ($pref->column_name === 'assigned_to') $displayName = 'Assigned To';

                    return [
                        'id' => $pref->id,
                        'box_id' => $pref->box_id,
                        'user_id' => $pref->user_id,
                        'table_name' => $pref->table_name,
                        'column_name' => $pref->column_name,
                        'display_name' => $displayName,
                        'order' => $pref->order,
                        'visible' => (bool) $pref->visible,
                        'updated_by' => $pref->updated_by,
                        'created_at' => $pref->created_at,
                        'updated_at' => $pref->updated_at,
                        'deleted_at' => $pref->deleted_at,
                    ];
                })
                ->values()
                ->toArray();

            return response()->json([
                'status' => true,
                'data' => $preferences // Return as array
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch column preferences.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function initializeDefaultColumnPreferences($boxId, $userId)
    {
        $order = 0;

        // Always add checkbox and star first
        $baseColumns = [
            ['column_name' => 'checkbox', 'title' => '', 'order' => $order++, 'visible' => true],
            ['column_name' => 'star', 'title' => '', 'order' => $order++, 'visible' => true],
        ];

        // Get all system fields for leads
        $systemFields = SystemField::where('box_id', $boxId)
            ->where('object', 'leads')
            ->orderBy('sequence', 'asc')
            ->get();

        // Default visible columns: name, email, phone, company_name, action (5 columns always visible)
        $defaultVisibleFields = ['name', 'email', 'phone', 'company_name'];

        foreach ($systemFields as $systemField) {
            $fieldName = $systemField->field;
            $isVisible = in_array($fieldName, $defaultVisibleFields);

            $baseColumns[] = [
                'column_name' => $fieldName,
                'title' => $systemField->field_name ?? $systemField->name,
                'order' => $order++,
                'visible' => $isVisible,
            ];
        }

        // Add other standard columns that might not be in system fields
        $standardColumns = [
            'phone_type',
            'industry',
            'tags',
            'query',
            'visibility',
            'status',
            'currency',
            'value',
            'created_at',
            'updated_at',
            'last_updated_by',
            'owner',
            'last_assigned_to',
            'last_lead_status',
            'gstin_number',
            'pan_number',
            'query',
            'created_from',
            'duplicate_leads',
            'change_assigned_to_time',
            'change_lead_status_time',
            'sec_phone',
            'last_activity_date',
            'last_activity_description',
            'upcoming_activity_date',
            'last_receipt_amount',
            'receipts_sum_amount',
            'activity_type_name',
            'reminder_description',
            'working_location',
            'matured_date',
            'assigned_history'
        ];

        foreach ($standardColumns as $colName) {
            // Check if already added via system fields
            $exists = collect($baseColumns)->contains(function ($col) use ($colName) {
                return $col['column_name'] === $colName;
            });

            if (!$exists) {
                $title = ucwords(str_replace('_', ' ', $colName));
                if ($colName === 'gstin_number')
                    $title = 'GSTIN';
                if ($colName === 'pan_number')
                    $title = 'PAN Number';
                if ($colName === 'created_at')
                    $title = 'Created On';
                if ($colName === 'updated_at')
                    $title = 'Updated On';
                if ($colName === 'last_updated_by')
                    $title = 'Last Updated By';
                if ($colName === 'change_assigned_to_time')
                    $title = 'Assigned Time';
                if ($colName === 'change_lead_status_time')
                    $title = 'Status Time';
                if ($colName === 'last_activity_date')
                    $title = 'Last Activity Date';
                if ($colName === 'last_activity_description')
                    $title = 'Last Activity Description';
                if ($colName === 'upcoming_activity_date')
                    $title = 'Upcoming Activity Due';
                if ($colName === 'activity_type_name')
                    $title = 'Activity Type';
                if ($colName === 'reminder_description')
                    $title = 'Activity Description';
                if ($colName === 'working_location')
                    $title = 'Working Location';
                if ($colName === 'last_receipt_amount')
                    $title = 'Last Received';
                if ($colName === 'receipts_sum_amount')
                    $title = 'Total Amount';
                if ($colName === 'last_receipt_date')
                    $title = 'Last Receipt Date';

                $baseColumns[] = [
                    'column_name' => $colName,
                    'title' => $title,
                    'order' => $order++,
                    'visible' => false,
                ];
            }
        }

        // Always add action column last
        $baseColumns[] = [
            'column_name' => 'action',
            'title' => 'Action',
            'order' => $order++,
            'visible' => true,
        ];

        // Insert all column preferences
        foreach ($baseColumns as $column) {
            TableColumnPreference::create([
                'box_id' => $boxId,
                'user_id' => $userId,
                'table_name' => 'leads',
                'column_name' => $column['column_name'],
                'display_name' => $column['title'] ?? ucwords(str_replace('_', ' ', $column['column_name'])),
                'order' => $column['order'],
                'visible' => $column['visible'],
                'updated_by' => $userId,
            ]);
        }

        // Also handle custom columns dynamically
        $this->syncCustomColumns($boxId, $userId);
    }

    private function syncCustomColumns($boxId, $userId)
    {
        // Get all custom fields
        $customFields = BoxCustomField::where('box_id', $boxId)
            ->where('object', 'leads')
            ->where('status', 'Enabled')
            ->pluck('col_name')
            ->toArray();

        // Get all system fields to check if any are missing
        $systemFields = SystemField::where('box_id', $boxId)
            ->where('object', 'leads')
            ->get();

        // Get existing column preferences
        $existingPrefs = TableColumnPreference::where('box_id', $boxId)
            ->where('user_id', $userId)
            ->where('table_name', 'leads')
            ->pluck('column_name')
            ->toArray();

        // Sync system fields
        $maxOrder = TableColumnPreference::where('box_id', $boxId)
            ->where('user_id', $userId)
            ->where('table_name', 'leads')
            ->max('order') ?? 0;

        $newOrder = $maxOrder + 1;

        foreach ($systemFields as $systemField) {
            $fieldName = $systemField->field;

            // Check if system field column already exists
            if (!in_array($fieldName, $existingPrefs)) {
                TableColumnPreference::create([
                    'box_id' => $boxId,
                    'user_id' => $userId,
                    'table_name' => 'leads',
                    'column_name' => $fieldName,
                    'display_name' => $systemField->name ?? ucwords(str_replace('_', ' ', $fieldName)),
                    'order' => $newOrder++,
                    'visible' => false, // New system fields hidden by default
                    'updated_by' => $userId,
                ]);
            }
        }

        // Sync standard system fields (hardcoded list)
        $standardColumns = [
            'phone_type',
            'industry',
            'tags',
            'query',
            'visibility',
            'status',
            'currency',
            'value',
            'created_at',
            'updated_at',
            'last_updated_by',
            'owner',
            'last_assigned_to',
            'last_lead_status',
            'gstin_number',
            'pan_number',
            'query',
            'created_from',
            'duplicate_leads',
            'change_assigned_to_time',
            'change_lead_status_time',
            'sec_phone',
            'last_activity_date',
            'last_activity_description',
            'upcoming_activity_date',
            'activity_type_name',
            'reminder_description',
            'working_location',
            'last_receipt_amount',
            'receipts_sum_amount',
            'last_receipt_date'
        ];

        foreach ($standardColumns as $colName) {
            if (!in_array($colName, $existingPrefs)) {
                $title = ucwords(str_replace('_', ' ', $colName));
                if ($colName === 'gstin_number')
                    $title = 'GSTIN';
                if ($colName === 'pan_number')
                    $title = 'PAN Number';
                if ($colName === 'created_at')
                    $title = 'Created On';
                if ($colName === 'updated_at')
                    $title = 'Updated On';
                if ($colName === 'last_updated_by')
                    $title = 'Last Updated By';
                if ($colName === 'change_assigned_to_time')
                    $title = 'Assigned Time';
                if ($colName === 'change_lead_status_time')
                    $title = 'Status Time';
                if ($colName === 'change_assigned_to_time')
                    $title = 'Assigned Time';
                if ($colName === 'change_lead_status_time')
                    $title = 'Status Time';
                if ($colName === 'sec_phone')
                    $title = 'Secondary Phone';
                if ($colName === 'last_activity_date')
                    $title = 'Last Activity Date';
                if ($colName === 'last_activity_description')
                    $title = 'Last Activity Description';
                if ($colName === 'upcoming_activity_date')
                    $title = 'Upcoming Activity Due';
                if ($colName === 'activity_type_name')
                    $title = 'Activity Type';
                if ($colName === 'reminder_description')
                    $title = 'Activity Description';
                if ($colName === 'working_location')
                    $title = 'Working Location';
                if ($colName === 'last_receipt_amount')
                    $title = 'Last Received';
                if ($colName === 'receipts_sum_amount')
                    $title = 'Total Amount';
                if ($colName === 'last_receipt_date')
                    $title = 'Last Receipt Date';

                // Default visibility true for new tracking fields to match frontend
                $isVisible = in_array($colName, [
                    'change_assigned_to_time',
                    'change_lead_status_time',
                    'last_activity_date',
                    'last_activity_description',
                    'upcoming_activity_date',
                    'activity_type_name',
                    'reminder_description',
                    'working_location'
                ]);

                TableColumnPreference::create([
                    'box_id' => $boxId,
                    'user_id' => $userId,
                    'table_name' => 'leads',
                    'column_name' => $colName,
                    'display_name' => $title,
                    'order' => $newOrder++,
                    'visible' => $isVisible,
                    'updated_by' => $userId,
                ]);
            }
        }

        // Get existing custom column preferences
        $existingCustomPrefs = TableColumnPreference::where('box_id', $boxId)
            ->where('user_id', $userId)
            ->where('table_name', 'leads')
            ->where('column_name', 'like', 'custom_%')
            ->pluck('column_name')
            ->map(function ($name) {
                return str_replace('custom_', '', $name);
            })
            ->toArray();

        // Find new custom fields that don't have preferences yet
        $newCustomFields = array_diff($customFields, $existingCustomPrefs);

        if (!empty($newCustomFields)) {
            // Get the highest order for custom columns
            $maxCustomOrder = TableColumnPreference::where('box_id', $boxId)
                ->where('user_id', $userId)
                ->where('table_name', 'leads')
                ->where('column_name', 'like', 'custom_%')
                ->max('order') ?? $newOrder;

            $customOrder = $maxCustomOrder + 1;

            // Add new custom columns to preferences
            foreach ($newCustomFields as $customField) {
                TableColumnPreference::create([
                    'box_id' => $boxId,
                    'user_id' => $userId,
                    'table_name' => 'leads',
                    'column_name' => 'custom_' . $customField,
                    'display_name' => ucwords(str_replace('_', ' ', $customField)),
                    'order' => $customOrder++,
                    'visible' => false, // New custom fields hidden by default
                    'updated_by' => $userId,
                ]);
            }
        }
    }

    public function saveColumnPreferences(Request $request)
    {
        try {
            // Convert visible strings to boolean
            $columns = $request->input('columns', []);
            foreach ($columns as $key => $column) {
                if (isset($column['visible'])) {
                    $columns[$key]['visible'] = filter_var($column['visible'], FILTER_VALIDATE_BOOLEAN);
                }
            }
            $request->merge(['columns' => $columns]);

            $request->validate([
                'columns' => 'required|array',
                'columns.*.column_name' => 'required|string',
                'columns.*.visible' => 'required|boolean',
                'columns.*.order' => 'required|integer',
            ]);

            $boxUser = BoxUser::where('user_id', Auth::id())->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            $userId = Auth::id();
            $boxId = $boxUser->box_id;

            // Sync custom columns and system fields first to ensure all are in preferences
            $this->syncCustomColumns($boxId, $userId);

            foreach ($request->columns as $column) {
                TableColumnPreference::updateOrCreate(
                    [
                        'box_id' => $boxId,
                        'user_id' => $userId,
                        'table_name' => 'leads',
                        'column_name' => $column['column_name'],
                    ],
                    [
                        'order' => (int) $column['order'],
                        'visible' => (bool) $column['visible'],
                        'updated_by' => $userId,
                    ]
                );
            }





            return response()->json([
                'status' => true,
                'message' => 'Column preferences saved successfully.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save column preferences.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all sources for filter dropdown
     */
    public function getSourcesList(Request $request)
    {
        try {
            $boxUser = BoxUser::where('user_id', Auth::id())->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            $sources = CrmSource::where('box_id', $boxUser->box_id)
                ->where('status', true)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $sources
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch sources.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all lead statuses for filter dropdown
     */
    public function getLeadStatusList(Request $request)
    {
        try {
            $boxUser = BoxUser::where('user_id', Auth::id())->first();
            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            $statuses = ContactStage::where('box_id', $boxUser->box_id)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $statuses
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch lead statuses.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all users (assigned to) for filter dropdown
     */
    public function getUsersList(Request $request)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();

            if (!$boxUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            // Get users associated with this box (allowing both 'Enabled', 'Active', or any non-disabled status)
            $query = BoxUser::where('box_id', $boxUser->box_id)
                ->where(function($q) {
                    $q->whereIn('status', ['Enabled', 'enabled', 'Active', 'active', '1'])
                      ->orWhereNull('status')
                      ->orWhere('status', '');
                });

            // Filter by designation if provided
            if ($request->filled('designation_id')) {
                $query->where('designation', $request->designation_id);
            }

            // Filter by department if provided
            if ($request->filled('department_id')) {
                $query->where('department', $request->department_id);
            }


            $userIds = $query->pluck('user_id');

            $users = \App\Models\User::whereIn('id', $userIds)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $users
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch users.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function bulkAssign(Request $request)
    {
        try {
            // Handle comma-separated string (fix for max_input_vars)
            if (is_string($request->input('lead_ids'))) {
                $ids = explode(',', $request->input('lead_ids'));
                $request->merge(['lead_ids' => $ids]);
            }

            $validator = Validator::make($request->all(), [
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'integer|exists:leads,id',
                'assigned_to' => 'required|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leadIds = $request->input('lead_ids');
            $assignedTo = $request->input('assigned_to');
            $userId = Auth::id();

            // Queue if more than 100 records
            if (count($leadIds) > 100) {
                \App\Jobs\BulkAssignLeadsJob::dispatch($leadIds, $assignedTo, $userId);

                return response()->json([
                    'status' => true,
                    'message' => 'Bulk assignment started successfully. Updates will be processed in the background.'
                ]);
            }

            // Process directly if <= 100
            $recipientUser = $assignedTo ? \App\Models\User::find($assignedTo) : null;
            $newUserName = $recipientUser ? $recipientUser->name : 'Unassigned';

            $leads = Lead::whereIn('id', $leadIds)->get();

            // Optimize: Fetch all old assignees in one query
            $oldAssigneeIds = $leads->pluck('assigned_to')->filter()->unique();
            $oldUsers = \App\Models\User::whereIn('id', $oldAssigneeIds)->pluck('name', 'id');

            $activities = [];
            $now = Carbon::now();

            /** @var \App\Models\Lead[] $leads */
            foreach ($leads as $lead) {
                $oldAssignedTo = $lead->assigned_to;
                $oldUserName = $oldUsers[$oldAssignedTo] ?? 'Unassigned';

                if ($oldAssignedTo == $assignedTo) {
                    continue;
                }

                $lead->last_assigned_to = $oldAssignedTo;
                $lead->assigned_to = $assignedTo;
                $lead->last_updated_by = $userId;
                $lead->updated_at = $now;
                $lead->change_assigned_to_time = $now;
                $lead->save();

                $activities[] = [
                    'box_id' => $lead->box_id,
                    'activity_id' => $lead->id,
                    'activity_type' => 'leads',
                    'type' => 'history',
                    'subject' => 'Lead Updated',
                    'description' => "Assigned to {$newUserName}",
                    'added_by' => $userId,
                    'last_updated_by' => $userId,
                    'status' => 'Close',
                    'completed_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'params' => json_encode([
                        'previous_assignee_name' => $oldUserName,
                        'current_assignee_name' => $newUserName
                    ]),
                ];

                if ($assignedTo) {
                    \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $assignedTo, 'transfer', $userId);
                }
            }

            if (!empty($activities)) {
                Activity::insert($activities);
            }

            return response()->json([
                'status' => true,
                'message' => 'Leads assigned successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk assign error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to process bulk assignment: ' . $e->getMessage()
            ], 500);
        }
    }
    public function bulkUpdateStatus(Request $request)
    {
        try {
            // Handle comma-separated string (fix for max_input_vars)
            if (is_string($request->input('lead_ids'))) {
                $ids = explode(',', $request->input('lead_ids'));
                $request->merge(['lead_ids' => $ids]);
            }

            $validator = Validator::make($request->all(), [
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'integer|exists:leads,id',
                'lead_status_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leadIds = $request->input('lead_ids');
            $statusId = $request->input('lead_status_id');
            $userId = Auth::id();

            if (count($leadIds) > 100) {
                // Cast to ensure strict type compatibility with Job constructor
                $leadIds = array_map('intval', $leadIds);
                $statusId = (int) $statusId;

                \App\Jobs\BulkUpdateLeadStatusJob::dispatch($leadIds, $statusId, $userId);
                return response()->json([
                    'status' => true,
                    'message' => 'Bulk status update started successfully. Updates will be processed in the background.'
                ]);
            }

            DB::transaction(function () use ($leadIds, $statusId, $userId) {
                $now = now();
                $leads = Lead::whereIn('id', $leadIds)->get(['id', 'lead_status_id', 'box_id']);
                $historyData = [];

                foreach ($leads as $lead) {
                    if ($lead->lead_status_id != $statusId) {
                        $historyData[] = [
                            'box_id' => $lead->box_id,
                            'lead_id' => $lead->id,
                            'old_status_id' => $lead->lead_status_id,
                            'new_status_id' => $statusId,
                            'changed_by' => $userId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if (!empty($historyData)) {
                    \App\Models\LeadStatusHistory::insert($historyData);
                }

                Lead::whereIn('id', $leadIds)->update([
                    'last_lead_status' => DB::raw('lead_status_id'),
                    'lead_status_id' => $statusId,
                    'last_updated_by' => $userId,
                    'updated_at' => $now,
                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Lead statuses updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk status update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to process bulk status update: ' . $e->getMessage()
            ], 500);
        }
    }


    public function bulkUpdateSource(Request $request)
    {
        try {
            // Handle comma-separated string (fix for max_input_vars)
            if (is_string($request->input('lead_ids'))) {
                $ids = explode(',', $request->input('lead_ids'));
                $request->merge(['lead_ids' => $ids]);
            }

            $validator = Validator::make($request->all(), [
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'integer|exists:leads,id',
                'lead_source_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leadIds = $request->input('lead_ids');
            $sourceId = $request->input('lead_source_id');
            $userId = Auth::id();

            if (count($leadIds) > 100) {
                // Cast to ensure strict type compatibility with Job constructor
                $leadIds = array_map('intval', $leadIds);
                $sourceId = (int) $sourceId;

                \App\Jobs\BulkUpdateLeadSourceJob::dispatch($leadIds, $sourceId, $userId);
                return response()->json([
                    'status' => true,
                    'message' => 'Bulk source update started successfully. Updates will be processed in the background.'
                ]);
            }

            Lead::whereIn('id', $leadIds)->update([
                'lead_source_id' => $sourceId,
                'last_updated_by' => $userId,
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Lead sources updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk source update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to process bulk source update: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkTrash(Request $request)
    {
        // Permission Check
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $action = 'delete';
        $canAccess = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('leads', $action, 'list');

        if (!$canAccess) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to ' . $action . ' leads.'
            ], 403);
        }

        try {
            // Handle comma-separated string (fix for max_input_vars)
            if (is_string($request->input('lead_ids'))) {
                $ids = explode(',', $request->input('lead_ids'));
                $request->merge(['lead_ids' => $ids]);
            }

            $validator = Validator::make($request->all(), [
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'integer|exists:leads,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leadIds = $request->input('lead_ids');
            
            // Validation: Check for associated invoices (receipts)
            $hasInvoices = \App\Models\Accounts\Receipt::whereIn('lead_id', $leadIds)->exists();
            if ($hasInvoices) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Cannot delete lead(s) because they have associated invoices linked to them.'
                ], 422);
            }

            $userId = Auth::id();

            // Queue if more than 100
            if (count($leadIds) > 100) {
                \App\Jobs\BulkTrashLeadsJob::dispatch($leadIds, $userId);
                return response()->json([
                    'status' => true,
                    'message' => 'Bulk trash process started successfully.'
                ]);
            }

            // Process directly if <= 100
            $leads = Lead::whereIn('id', $leadIds)->get();

            // Optimize: Resolve Trasher Name once per unique box/user combo
            // Actually, trasher is always Current User ($userId). 
            // We just need their correct name representation in each lead's box.

            $boxIds = $leads->pluck('box_id')->unique();
            $boxUsers = BoxUser::where('user_id', $userId)
                ->whereIn('box_id', $boxIds)
                ->with('user')
                ->get()
                ->keyBy('box_id');

            $currentUser = \App\Models\User::find($userId); // Fallback

            $activities = [];
            $now = Carbon::now();

            /** @var \App\Models\Lead[] $leads */
            foreach ($leads as $lead) {
                $trasherName = 'Unassigned';
                $trasherBoxUser = $boxUsers[$lead->box_id] ?? null;

                if ($trasherBoxUser) {
                    if ($trasherBoxUser->user) {
                        $trasherName = $trasherBoxUser->user->name;
                    } elseif ($trasherBoxUser->first_name) {
                        $trasherName = trim($trasherBoxUser->first_name . ' ' . $trasherBoxUser->last_name);
                    }
                } elseif ($currentUser) {
                    $trasherName = $currentUser->name;
                }

                $activities[] = [
                    'box_id' => $lead->box_id,
                    'activity_id' => $lead->id,
                    'activity_type' => 'leads',
                    'type' => 'history',
                    'subject' => "{$trasherName} Trashed the Lead",
                    'description' => "Lead moved to trash by {$trasherName}",
                    'added_by' => $userId,
                    'last_updated_by' => $userId,
                    'status' => 'Close',
                    'completed_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'params' => json_encode([]),
                ];

                $lead->last_updated_by = $userId;
                $lead->trashed_at = $now;
                $lead->save();
                $lead->delete();
            }

            if (!empty($activities)) {
                Activity::insert($activities);
            }

            return response()->json([
                'status' => true,
                'message' => 'Selected leads moved to trash.'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk trash error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to process bulk trash: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkRestore(Request $request)
    {
        try {
            // Handle comma-separated string (fix for max_input_vars)
            if (is_string($request->input('lead_ids'))) {
                $ids = explode(',', $request->input('lead_ids'));
                $request->merge(['lead_ids' => $ids]);
            }

            $validator = Validator::make($request->all(), [
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leadIds = $request->input('lead_ids');
            $userId = Auth::id();

            // Queue if more than 100
            if (count($leadIds) > 100) {
                \App\Jobs\BulkRestoreLeadsJob::dispatch($leadIds, $userId);
                return response()->json([
                    'status' => true,
                    'message' => 'Bulk restore process started successfully.'
                ]);
            }

            // Process directly if <= 100
            $leads = Lead::withTrashed()->whereIn('id', $leadIds)->get();

            // Optimize Read: Old Assignees
            $oldAssigneeIds = $leads->pluck('assigned_to')->filter()->unique();
            // Note: assigned_to in DB is typically User ID, but in some systems it maps to BoxUser. 
            // In LeadsController::bulkAssign we used User::find. So we stick to User::find here too.
            // Wait, in bulkRestore logic provided in Job, it was looking up BoxUser for oldAssignedTo?
            // "oldUser = oldAssignedTo ? \App\Models\BoxUser::find(oldAssignedTo) : null;" in BulkRestoreLeadsJob.php
            // BUT in BulkAssignLeadsJob.php it was "\App\Models\User::find(oldAssignedTo)".
            // This is an inconsistency in the existing legacy code.
            // I will follow the patterns found in the specific Jobs/Methods I am refactoring.
            // For BulkRestoreLeadsJob (Job file viewed earlier), it uses BoxUser for oldAssignedTo.
            // Let's verify what `assigned_to` column holds. Usually it's User ID.
            // If it holds BoxUser ID, then BulkAssignLeadsJob (User::find) is wrong.
            // If it holds User ID, then BulkRestoreLeadsJob (BoxUser::find) is wrong.
            // However, assuming `assigned_to` holds User ID is the standard Laravel convention. 
            // The BulkRestoreLeadsJob I viewed earlier definitely did: $oldUser = ... BoxUser::find($oldAssignedTo).
            // Let's assume the BulkRestoreLeadsJob was correct in its intent or I should stick to it to avoid breaking changes, 
            // OR I should correct it if it looks undeniably wrong.
            // Given "BoxUser::where('user_id', $userId)..." calls elsewhere, it implies BoxUser links User to Box.
            // If assigned_to is a User ID, BoxUser::find(UserID) would imply BoxUser.id == User.id, which is unlikely.
            // I'll stick to User::find() for assigned_to, as that is what BulkAssign used and what implies a User relation. 
            // The BulkRestoreLeadsJob code might have been using BoxUser::find by mistake or assumption. 
            // Actually, checking LeadsController::getLeads earlier, $lead->assigned_to is treated as User.
            // I will use User::find for consistency with BulkAssign.

            $oldUsers = \App\Models\User::whereIn('id', $oldAssigneeIds)->pluck('name', 'id');

            // Optimize Read: Restorer (Current User) BoxUsers
            $boxIds = $leads->pluck('box_id')->unique();
            $boxUsers = BoxUser::where('user_id', $userId)
                ->whereIn('box_id', $boxIds)
                ->with('user')
                ->get()
                ->keyBy('box_id');

            $activities = [];
            $now = Carbon::now();

            $currentUser = \App\Models\User::find($userId);

            /** @var \App\Models\Lead[] $leads */
            foreach ($leads as $lead) {
                $oldAssignedTo = $lead->assigned_to;
                $oldUserName = $oldUsers[$oldAssignedTo] ?? 'Unassigned';

                // Resolve restorer identity for assignment
                $restorerBoxUser = $boxUsers[$lead->box_id] ?? null;

                // Logic from Job: newAssignedTo = restorerBoxUser->id (BoxUser ID???)
                // Wait, if assigned_to is User ID, then we should use $userId.
                // But BulkRestoreLeadsJob uses "$newAssignedTo = $restorerBoxUser ? $restorerBoxUser->id : null;"
                // This implies assigned_to MIGHT be BoxUser ID.
                // Let's look at BulkAssignLeadsJob again. "$lead->assigned_to = $this->assignedTo" where assignedTo is Input "exists:users,id".
                // So BulkAssign sets it to User ID.
                // BulkRestore sets it to BoxUser ID? That's a huge data inconsistency.
                // I must check `LeadsController::store`. 
                // Since I cannot check store right now easily without using a tool, I will assume User ID is correct (BulkAssign is more common).
                // I will set newAssignedTo to $restorerBoxUser->user_id (which is $userId) OR just $userId?
                // Actually, if I restore a lead, I usually want to assign it to *myself* (User ID).
                // So I will use $userId (or restorerBoxUser->user_id).

                $newAssignedTo = $userId; // Simplest and most consistent with BulkAssign
                $newUserName = $restorerBoxUser && $restorerBoxUser->user ? $restorerBoxUser->user->name : 'Unassigned';
                if ($newUserName === 'Unassigned' && $currentUser)
                    $newUserName = $currentUser->name;


                if ($lead->trashed()) {
                    $lead->restore();
                }

                $lead->last_assigned_to = $oldAssignedTo;
                $lead->assigned_to = $userId; // Assign to restorer
                $lead->change_assigned_to_time = $now;
                $lead->last_updated_by = $userId;
                $lead->updated_at = $now;
                $lead->duplicate_leads = 0;
                $lead->trashed_at = null;
                $lead->save();

                $activities[] = [
                    'box_id' => $lead->box_id,
                    'activity_id' => $lead->id,
                    'activity_type' => 'leads',
                    'type' => 'history',
                    'subject' => "{$newUserName} Lead is restored",
                    'description' => "Lead restored from trash from {$oldUserName} to {$newUserName}",
                    'added_by' => $userId,
                    'last_updated_by' => $userId,
                    'status' => 'Close',
                    'completed_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'params' => json_encode([]),
                ];

                \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $userId, 'restored');
            }

            if (!empty($activities)) {
                Activity::insert($activities);
            }

            return response()->json([
                'status' => true,
                'message' => 'Leads restored successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk restore error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to process bulk restore: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        // Permission Check
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $action = 'delete';
        $canAccess = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('leads', $action, 'list');

        if (!$canAccess) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to ' . $action . ' leads.'], 403);
        }

        try {
            // Handle comma-separated string (fix for max_input_vars)
            if (is_string($request->input('lead_ids'))) {
                $ids = explode(',', $request->input('lead_ids'));
                $request->merge(['lead_ids' => $ids]);
            }

            $validator = Validator::make($request->all(), [
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'integer', // Not checking exists:leads,id strictly for force delete just in case, or we can check trashed too.
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leadIds = $request->input('lead_ids');
            
            // Validation: Check for associated invoices (receipts)
            $hasInvoices = \App\Models\Accounts\Receipt::whereIn('lead_id', $leadIds)->exists();
            if ($hasInvoices) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Cannot delete lead(s) because they have associated invoices linked to them.'
                ], 422);
            }

            $userId = Auth::id();

            // Queue if more than 100
            if (count($leadIds) > 100) {
                \App\Jobs\BulkDeleteLeadsJob::dispatch($leadIds, $userId);
                return response()->json([
                    'status' => true,
                    'message' => 'Bulk delete process started successfully.'
                ]);
            }

            // Process directly if <= 100
            $leads = Lead::withTrashed()->whereIn('id', $leadIds)->get();
            /** @var \App\Models\Lead[] $leads */
            foreach ($leads as $lead) {
                $lead->last_updated_by = $userId;
                $lead->save();
                // Permanent delete as requested
                $lead->forceDelete();
            }

            return response()->json([
                'status' => true,
                'message' => 'Leads deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to process bulk delete: ' . $e->getMessage()
            ], 500);
        }
    }
    public static function applyCondition($query, $field, $operator, $value)
    {
        // Handle Date Presets
        if (in_array($value, ['today', 'yesterday', 'greater_than_today', 'less_than_today', 'last_7_days', 'last_1_month', 'this_month'])) {
            $today = \Carbon\Carbon::today();
            switch ($value) {
                case 'today':
                    $query->whereBetween($field, [$today->toDateString() . ' 00:00:00', $today->toDateString() . ' 23:59:59']);
                    return;
                case 'yesterday':
                    $query->whereBetween($field, [\Carbon\Carbon::yesterday()->toDateString() . ' 00:00:00', \Carbon\Carbon::yesterday()->toDateString() . ' 23:59:59']);
                    return;
                case 'greater_than_today':
                    $query->where($field, '>', $today->toDateString() . ' 23:59:59');
                    return;
                case 'less_than_today':
                    $query->where($field, '<', $today->toDateString() . ' 00:00:00');
                    return;
                case 'last_7_days':
                    $query->whereBetween($field, [\Carbon\Carbon::today()->subDays(6), \Carbon\Carbon::now()]);
                    return;
                case 'last_1_month':
                    $query->whereBetween($field, [\Carbon\Carbon::today()->subMonth(), \Carbon\Carbon::now()]);
                    return;
                case 'this_month':
                    $query->whereBetween($field, [
                        \Carbon\Carbon::now()->startOfMonth()->toDateString() . ' 00:00:00',
                        \Carbon\Carbon::now()->endOfMonth()->toDateString() . ' 23:59:59'
                    ]);
                    return;
                case 'this_week':
                    $query->whereBetween($field, [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                    return;
            }
        }

        $idFields = [
            'id',
            'lead_id',
            'user_id',
            'lead_source_id',
            'lead_status_id',
            'assigned_to',
            'owner_id',
            'created_by',
            'updated_by',
            'box_id',
            'country_id',
            'state_id',
            'city_id',
            'last_assigned_to',
            'last_lead_status'
        ];
        $cleanField = str_replace('leads.', '', $field);

        switch ($operator) {
            case 'contains':
                if (in_array($cleanField, $idFields)) {
                    $query->where($field, $value);
                } else {
                    $query->where($field, 'like', '%' . $value . '%');
                }
                break;
            case 'not_contains':
                if (in_array($cleanField, $idFields)) {
                    $query->where($field, '!=', $value);
                } else {
                    $query->where($field, 'not like', '%' . $value . '%');
                }
                break;
            case 'date_is': // Date specific equal
                $query->whereBetween($field, [$value . ' 00:00:00', $value . ' 23:59:59']);
                break;
            case 'equals':
                // Strict equality (for IDs or exact text)
                // If Date field, use whereDate to ignore time part
                if (in_array($field, ['created_at', 'updated_at']) || str_contains($field, '_date')) {
                    $query->whereBetween($field, [$value . ' 00:00:00', $value . ' 23:59:59']);
                } else {
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    } else {
                        $query->where($field, '=', $value);
                    }
                }
                break;
            case 'greater_than':
                $query->where($field, '>', $value);
                break;
            case 'less_than':
                $query->where($field, '<', $value);
                break;
            case 'between':
                if (is_array($value) && isset($value['start'], $value['end'])) {
                    $query->whereBetween($field, [$value['start'] . ' 00:00:00', $value['end'] . ' 23:59:59']);
                }
                break;
            case 'empty':
                $query->where(function ($q) use ($field) {
                    $q->whereNull($field)->orWhere($field, '=', '');
                });
                break;
            case 'not_empty':
                $query->where(function ($q) use ($field) {
                    $q->whereNotNull($field)->where($field, '!=', '');
                });
                break;
        }
    }
    public function duplicate(Request $request)
    {
        $request->validate([
            'lead_id' => 'required',
            'assigned_to' => 'required', // User wants to choose
            'lead_source_id' => 'nullable'
        ]);

        try {
            $original = Lead::findOrFail($request->lead_id);
            $newLead = $original->replicate();
            $newLead->last_assigned_to = $original->assigned_to;
            $newLead->assigned_to = $request->assigned_to;
            $newLead->lead_source_id = $request->input('lead_source_id'); // Use input for nullable
            $newLead->created_at = now();
            $newLead->updated_at = now();
            $newLead->save();

            // Copy Custom Field Data
            $boxId = $original->box_id;
            $tableName = 'data_' . $boxId;
            if (\Illuminate\Support\Facades\Schema::hasTable($tableName)) {
                $originalData = DB::table($tableName)->where('lead_id', $original->id)->first();
                if ($originalData) {
                    $newData = (array) $originalData;
                    unset($newData['id']); // Primary Key
                    $newData['lead_id'] = $newLead->id;
                    $newData['created_at'] = now();
                    // Ensure we don't carry over updated_at if we want it fresh, or keep it.
                    // Usually customized tables might have created_at.
                    DB::table($tableName)->insert($newData);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Lead duplicated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Duplicate Lead Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to duplicate lead: ' . $e->getMessage()
            ], 500);
        }
    }
    public function exportLeads(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $action = 'export';
        $canAccess = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('leads', $action, 'list');

        if (!$canAccess) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to ' . $action . ' leads.'
            ], 403);
        }


        $boxSlug = $request->route('box_slug');
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box)
            return response()->json(['status' => false, 'message' => 'Box not found.'], 404);

        $filters = $request->input('filters', []);
        $type = $request->input('type', 'excel'); // excel or pdf

        if ($type == 'excel') {
            $fileName = 'leads_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $directory = 'exports';
            ExportLeadsJob::dispatch($directory, $fileName, $box->id, $filters, Auth::id());
            return response()->json([
                'status' => true,
                'message' => 'Export processing in background. The file will be sent to your email.'
            ]);
        }

        try {
            // PDF Logic
            $export = new LeadsExport($box->id, $filters);
            $leads = $export->query()->get();
            $headings = $export->headings();

            $pdf = Pdf::loadView('frontend.leads.leads_pdf', [
                'leads' => $leads,
                'headings' => $headings,
                'export' => $export
            ])->setPaper('a4', 'landscape');

            $fileName = 'leads_export_' . now()->format('Y_m_d_H_i_s') . '.pdf';
            $directory = 'exports';
            $path = "{$directory}/{$fileName}";

            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $pdf->save(Storage::disk('public')->path($path));

            return response()->json([
                'status' => true,
                'message' => 'PDF generated successfully.',
                'url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLeadSubStatusList(Request $request)
    {
        $leadStatusId = $request->input('lead_status_id');
        $boxUser = BoxUser::where('user_id', Auth::id())->first();
        if (!$boxUser)
            return response()->json(['status' => false, 'message' => 'User not found']);

        $subStatuses = \App\Models\LeadSubStatus::where('box_id', $boxUser->box_id)
            ->where('leads_status_id', $leadStatusId)
            ->where('status', 1)
            ->select('id', 'name')
            ->get();

        return response()->json(['status' => true, 'data' => $subStatuses]);
    }

    public function mergeDuplicates(Request $request)
    {
        try {
            // Handle comma-separated string
            if (is_string($request->input('lead_ids'))) {
                $ids = explode(',', $request->input('lead_ids'));
                $request->merge(['lead_ids' => $ids]);
            }
            $leadIds = $request->input('lead_ids');

            if (empty($leadIds) && !$request->input('target_lead_id')) {
                return response()->json(['status' => false, 'message' => 'No lead selected.']);
            }

            // Determine Target Lead
            $targetLeadId = $request->input('target_lead_id');
            if ($targetLeadId) {
                $toLead = Lead::where('id', $targetLeadId)->first();
            } else if (!empty($leadIds)) {
                // Assume the FIRST lead is the Master (Target) if no explicit target provided
                $toLead = Lead::find($leadIds[0]);
                // Remove Target from the 'to-be-merged' list if it's there
                $leadIds = array_diff($leadIds, [$leadIds[0]]);
            }

            if (!$toLead) {
                return response()->json(['status' => false, 'message' => 'Target lead not found.']);
            }

            $duplicates = collect();

            // 1. Any leads checked in the table (if target was explicit, these are ALL sources)
            if (!empty($leadIds)) {
                $checkedLeads = Lead::whereIn('id', $leadIds)
                    ->where('box_id', $toLead->box_id)
                    ->where('id', '!=', $toLead->id)
                    ->get();
                $duplicates = $duplicates->merge($checkedLeads);
            }

            // 2. MANUAL MERGE: If a specific lead ID is provided from modal
            $manualMergeId = $request->input('merge_from_lead_id');
            if ($manualMergeId) {
                $manualLead = Lead::where('box_id', $toLead->box_id)
                    ->where('id', $manualMergeId)
                    ->where('id', '!=', $toLead->id)
                    ->first();
                if ($manualLead) {
                    $duplicates->push($manualLead);
                }
            }

            // 3. AUTO MERGE: find duplicates by email/phone only if not explicit target merge
            if (!$targetLeadId) {
                $autoDuplicates = Lead::where('box_id', $toLead->box_id)
                    ->where('id', '!=', $toLead->id)
                    ->where(function ($q) use ($toLead) {
                        $hasCondition = false;
                        if ($toLead->email) {
                            $q->where('email', $toLead->email);
                            $hasCondition = true;
                        }
                        if ($toLead->phone) {
                            if ($hasCondition)
                                $q->orWhere('phone', $toLead->phone);
                            else
                                $q->where('phone', $toLead->phone);
                        }
                    })
                    ->get();
                $duplicates = $duplicates->merge($autoDuplicates);
            }

            // Merge collections unique by ID
            $duplicates = $duplicates->unique('id');

            if ($duplicates->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No duplicates found to merge.']);
            }

            $mergedCount = 0;

            foreach ($duplicates as $fromLead) {
                $logMessages = [];

                // 1. Merge Activities
                Activity::where('activity_id', $fromLead->id)
                    ->where('activity_type', 'leads')
                    ->update(['activity_id' => $toLead->id]);

                // 2. Merge Other Related Records
                $relations = [
                    \App\Models\GmailMessage::class => 'lead_id',
                    \App\Models\LeadAssignmentHistory::class => 'lead_id',
                    \App\Models\LeadStatusHistory::class => 'lead_id',
                    \App\Models\LeadWorkingLocation::class => 'lead_id',
                    \App\Models\LeadAssignee::class => 'lead_id',
                    \App\Models\WebHook::class => 'lead_id',
                    \App\Models\FacebookLead::class => 'lead_id',
                    \App\Models\DivisionArea::class => 'lead_id',
                    \App\Models\AreaLead::class => 'area_lead_id',
                    \App\Models\Accounts\Receipt::class => 'lead_id',
                ];

                foreach ($relations as $modelClass => $foreignKey) {
                    if (class_exists($modelClass)) {
                        try {
                            $modelClass::where($foreignKey, $fromLead->id)->update([$foreignKey => $toLead->id]);
                        } catch (\Exception $e) {
                            Log::warning("Failed to merge relation $modelClass: " . $e->getMessage());
                        }
                    }
                }

                // 3. Merge Phone Logic
                // Source: fromLead->phone
                // Target: toLead->phone, toLead->sec_phone
                if ($fromLead->phone) {
                    // Check if already exists in ToLead (Primary or Secondary)
                    $phoneExists = ($fromLead->phone == $toLead->phone) || ($fromLead->phone == $toLead->sec_phone);

                    if (!$phoneExists) {
                        if (empty($toLead->sec_phone)) {
                            // Update Secondary
                            $toLead->sec_phone = $fromLead->phone;
                        } else {
                            // Conflict: Secondary is taken by a different number
                            $logMessages[] = "Phone no {$fromLead->phone} is removed from system during merge";
                        }
                    }
                }

                // 4. Merge Email Logic
                // Source: fromLead->email
                // Target: toLead->email, toLead->sec_email
                if ($fromLead->email) {
                    // Check if already exists
                    $emailExists = ($fromLead->email == $toLead->email) || ($fromLead->email == $toLead->sec_email);

                    if (!$emailExists) {
                        if (empty($toLead->sec_email)) {
                            // Update Secondary
                            $toLead->sec_email = $fromLead->email;
                        } else {
                            // Conflict
                            $logMessages[] = "Email id {$fromLead->email} is removed from system during merge";
                        }
                    }
                }

                // Add Log if needed
                if (!empty($logMessages)) {
                    Activity::create([
                        'box_id' => $toLead->box_id,
                        'activity_id' => $toLead->id,
                        'activity_type' => 'leads',
                        'type' => 'note',
                        'subject' => 'Merge Conflict Log',
                        'description' => implode('. ', $logMessages),
                        'added_by' => Auth::id(),
                        'last_updated_by' => Auth::id(),
                        'activity_date' => now(),
                        'params' => [],
                    ]);
                }

                // Delete the Duplicate
                $fromLead->delete();
                $mergedCount++;
            }

            $toLead->save();

            return response()->json([
                'status' => true,
                'message' => "Successfully merged {$mergedCount} duplicates into Lead {$toLead->name}."
            ]);

        } catch (\Exception $e) {
            Log::error('Merge Duplicates Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Merge failed: ' . $e->getMessage()], 500);
        }
    }
    public function saveLead($box, $requestData, $filterData, $mapper, $created_at, $added_by, $source_name = 'WebHook')
    {
        $boxId = $box->id;
        $email = $requestData['email'] ?? null;
        $secEmail = $requestData['sec_email'] ?? null;
        $phone = $requestData['phone'] ?? null;
        $secPhone = $requestData['sec_phone'] ?? null;

        // 1. Duplicate Check (Refined)
        $uniqueField = $box->lead_required_field ?? 'email';
        $allowDuplicate = $box->allow_duplicate_leads_auto ?? 0;
        $existingLead = null;

        if ($allowDuplicate == 0 && ($email || $phone || $secEmail || $secPhone)) {
            $existingLead = Lead::where('box_id', $boxId)->where('duplicate_leads', 0)
                ->where(function ($inner) use ($email, $phone, $secEmail, $secPhone) {
                    $isFirst = true;
                    if ($email) {
                        $inner->where('email', $email)->orWhere('sec_email', $email);
                        $isFirst = false;
                    }
                    if ($secEmail) {
                        if ($isFirst)
                            $inner->where('email', $secEmail)->orWhere('sec_email', $secEmail);
                        else
                            $inner->orWhere('email', $secEmail)->orWhere('sec_email', $secEmail);
                        $isFirst = false;
                    }
                    if ($phone) {
                        if ($isFirst)
                            $inner->where('phone', $phone)->orWhere('sec_phone', $phone)->orWhere('mobile', $phone);
                        else
                            $inner->orWhere('phone', $phone)->orWhere('sec_phone', $phone)->orWhere('mobile', $phone);
                        $isFirst = false;
                    }
                    if ($secPhone) {
                        if ($isFirst)
                            $inner->where('phone', $secPhone)->orWhere('sec_phone', $secPhone)->orWhere('mobile', $secPhone);
                        else
                            $inner->orWhere('phone', $secPhone)->orWhere('sec_phone', $secPhone)->orWhere('mobile', $secPhone);
                    }
                })->orderBy('id', 'asc')->first();
        }

        $isDuplicate = $existingLead ? 1 : 0;
        $isNew = !$existingLead;

        // Create a new lead as requested regardless of duplicates
        $lead = new Lead();
        $lead->box_id = $boxId;
        $lead->added_by = $added_by;
        $lead->created_at = $created_at;
        $lead->created_from = $source_name;
        $activeTime = $created_at ?? now();
        $lead->change_lead_status_time = $activeTime;
        $lead->change_assigned_to_time = $activeTime;

        // 2. Mapping Standard Fields
        foreach ($mapper as $requestKey => $leadKey) {
            if (array_key_exists($requestKey, $requestData)) {
                $lead->{$leadKey} = $requestData[$requestKey];
            }
        }

        // 3. Keyword Filtering Logic
        $searchText = strtolower(
            ($requestData['name'] ?? $lead->name ?? '') . ' ' .
            ($requestData['last_name'] ?? $lead->last_name ?? '') . ' ' .
            ($requestData['query'] ?? $lead->query ?? '') . ' ' .
            ($requestData['subject'] ?? $lead->subject ?? '') . ' ' .
            ($requestData['product_name'] ?? $lead->product_name ?? '') . ' ' .
            ($requestData['lead_type'] ?? $lead->lead_type ?? '') . ' ' .
            ($requestData['query_type'] ?? '')
        );
        // Check for 'has_words' (Exclusion/Trash)
        $hasWords = $filterData['has_words'] ?? [];
        if (!empty($hasWords)) {
            $matchFound = false;
            foreach ($hasWords as $word) {
                if (str_contains($searchText, strtolower($word))) {
                    $matchFound = true;
                    break;
                }
            }
            if ($matchFound) {
                $lead->trashed_at = now();
                $lead->deleted_at = now();
                Log::info("Lead moved to trash as it matches restricted keywords: " . (is_array($hasWords) ? implode(', ', $hasWords) : $hasWords));
            }
        }

        // Check for 'auto_trash' (Exclusion/Trash)
        $autoTrash = $filterData['auto_trash'] ?? [];
        if (!empty($autoTrash)) {
            foreach ($autoTrash as $word) {
                $loweredWord = strtolower($word);
                if (
                    str_contains($searchText, $loweredWord) ||
                    str_contains(strtolower($requestData['lead_type'] ?? ''), $loweredWord) ||
                    str_contains(strtolower($requestData['query_type'] ?? ''), $loweredWord)
                ) {
                    $lead->trashed_at = now();
                    $lead->deleted_at = now();
                    Log::info("Lead moved to trash as it contains auto_trash keywords: " . $word);
                    break;
                }
            }
        }

        // Check for 'auto_unassigned' (Stay in Dump)
        $isAutoUnassigned = false;
        $autoUnassigned = $filterData['auto_unassigned'] ?? [];
        if (!empty($autoUnassigned)) {
            foreach ($autoUnassigned as $word) {
                if (str_contains($searchText, strtolower($word))) {
                    $isAutoUnassigned = true;
                    Log::info("Lead set to unassigned due to keyword: " . $word);
                    break;
                }
            }
        }

        // Additional Standard Fields
        $lead->last_updated_by = $added_by;
        $lead->updated_at = now();
        $lead->duplicate_leads = $isNew ? 0 : 1;
        if (!$isNew && $existingLead) {
            $lead->duplicate_of_id = $existingLead->id;
        }
        $lead->lead_source_id = $filterData['lead_source_id'] ?? null;
        $lead->lead_status_id = $filterData['lead_status_id'] ?? null;
        $lead->query = $requestData['query'] ?? $lead->query;
        $lead->company_name = $requestData['company_name'] ?? $lead->company_name;

        // 3. Assignment Logic
        if ($isAutoUnassigned) {
            $lead->assigned_to = null;
        } elseif (!$isNew && $existingLead && $existingLead->assigned_to) {
            $lead->assigned_to = $existingLead->assigned_to;
        } elseif (isset($filterData['round_robin']) && $filterData['round_robin'] == 1 && !empty($filterData['round_robin_users'])) {
            $users = (array) $filterData['round_robin_users'];

            // Handle Round Robin Shifts if enabled
            $enableShifts = ($filterData['enable_shifts'] ?? 'No') == 'Yes';
            $shifts = $filterData['shifts'] ?? [];
            $eligibleUsers = [];
            $defaultUsers = [];

            if ($enableShifts && !empty($shifts)) {
                $currentTime = now()->format('H:i');
                foreach ($users as $userId) {
                    $shift = $shifts[$userId] ?? null;
                    if ($shift) {
                        $start = $shift['start'] ?? '00:00';
                        $end = $shift['end'] ?? '23:59';

                        // Simple time check (does not handle overnight shifts yet)
                        if ($currentTime >= $start && $currentTime <= $end) {
                            $eligibleUsers[] = $userId;
                        }

                        if (!empty($shift['is_default'])) {
                            $defaultUsers[] = $userId;
                        }
                    }
                }

                // Fallback sequence: Eligible (on shift) -> Default Users -> All RR Users
                if (empty($eligibleUsers)) {
                    $eligibleUsers = !empty($defaultUsers) ? $defaultUsers : $users;
                }
            } else {
                $eligibleUsers = $users;
            }

            // Perform Round Robin among eligible users
            // 1. Get the last fresh lead assigned to ANY of the Round Robin users to maintain global sequence
            $lastAssignedLead = Lead::where('box_id', $boxId)
                ->whereIn('assigned_to', $users)
                ->where('duplicate_leads', 0)
                ->orderBy('id', 'desc')
                ->first();

            $nextUser = $eligibleUsers[0]; // Fallback to first available if no previous record
            if ($lastAssignedLead) {
                // Find where we were in the FULL pool of users
                $fullPoolIndex = array_search($lastAssignedLead->assigned_to, $users);
                if ($fullPoolIndex !== false) {
                    // Look for the next user in the full pool who is currently eligible (on shift)
                    $found = false;
                    $poolCount = count($users);
                    // Start from the next position in the pool and circle through once
                    for ($i = 1; $i <= $poolCount; $i++) {
                        $testIndex = ($fullPoolIndex + $i) % $poolCount;
                        $testUserId = $users[$testIndex];

                        if (in_array($testUserId, $eligibleUsers)) {
                            $nextUser = $testUserId;
                            $found = true;
                            break;
                        }
                    }
                }
            }
            $lead->assigned_to = $nextUser;
        } else {
            $lead->assigned_to = $filterData['assigned_to'] ?? ($lead->assigned_to ?? $added_by);
        }

        $lead->save();

        if ($isDuplicate && $allowDuplicate == 1 && $existingLead) {
            $mergedSummary = "New duplicate API/Import entry received and created (New Lead ID: {$lead->id}):\n";
            foreach ($mapper as $requestKey => $leadKey) {
                if (array_key_exists($requestKey, $requestData) && !empty($requestData[$requestKey])) {
                    $label = ucwords(str_replace('_', ' ', $requestKey));
                    $mergedSummary .= "• {$label}: {$requestData[$requestKey]}\n";
                }
            }
            \App\Models\Activity::create([
                'box_id' => $boxId,
                'activity_id' => $existingLead->id,
                'type' => 'history',
                'subject' => 'Duplicate Lead Received (API/Import)',
                'description' => $mergedSummary,
                'activity_date' => now(),
                'added_by' => $added_by,
                'last_updated_by' => $added_by,
                'status' => 'Close',
                'completed_date' => now(),
            ]);
        }

        // Record Status History
        if ($lead->lead_status_id) {
            \App\Models\LeadStatusHistory::create([
                'box_id' => $lead->box_id,
                'lead_id' => $lead->id,
                'old_status_id' => null,
                'new_status_id' => $lead->lead_status_id,
                'changed_by' => $added_by,
                'created_at' => $created_at ?? now(),
                'updated_at' => $created_at ?? now(),
            ]);
        }

        // Populate Lead Assignees and History
        if ($lead->assigned_to) {
            $lead->assignees()->attach($lead->assigned_to, ['assigned_by' => $added_by]);
            \App\Models\LeadAssignmentHistory::create([
                'lead_id' => $lead->id,
                'user_id' => $lead->assigned_to,
                'assigned_by' => $added_by,
                'action' => 'assigned',
                'remarks' => 'Assigned during ' . $source_name . ' import',
                'created_at' => $created_at ?? now(),
                'updated_at' => $created_at ?? now(),
            ]);
        }

        // 4. Custom Fields (Individual Box Tables)
        $tableName = 'data_' . $boxId;
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            $customData = array_intersect_key($requestData, array_flip($columns));

            if (!empty($customData)) {
                // Remove primary key and internal fields to avoid SQL errors during updateOrInsert
                unset($customData['id'], $customData['lead_id'], $customData['created_at'], $customData['updated_at']);

                $customData['lead_id'] = $lead->id;
                $customData['updated_at'] = now();
                DB::table($tableName)->updateOrInsert(
                    ['lead_id' => $lead->id],
                    $customData
                );
            }
        }

        // 4.5 Capture Unmapped Fields for Activity Log
        $mappedKeys = array_keys($mapper);
        $unmappedData = array_diff_key($requestData, array_flip($mappedKeys));
        // Remove standard internal keys that don't need logging as "unmapped"
        unset($unmappedData['id'], $unmappedData['box_id'], $unmappedData['lead_id'], $unmappedData['submit_time'], $unmappedData['last_lead_date'], $unmappedData['created_at'], $unmappedData['updated_at']);

        $extraInfo = "";
        if (!empty($unmappedData)) {
            $extraInfo .= "\n\n--- Additional Information ---\n";
            foreach ($unmappedData as $key => $val) {
                $lowerKey = strtolower($key);
                if ($lowerKey === 'box_id' || $lowerKey === 'lead_api_box_id') continue;
                if ($val !== null && $val !== '') {
                    if (is_array($val))
                        $val = json_encode($val);
                    $extraInfo .= "• " . ucwords(str_replace('_', ' ', $key)) . ": " . $val . "\n";
                }
            }
        }

        // Prepare subject
        $subject = $isNew
            ? 'New Lead Imported'
            : 'Lead Updated via ' . $source_name;

        // Prepare description
        $description = "Lead data processed from source: " . $source_name;

        // Merge query + extra info if exists
        if ($lead->query || $extraInfo) {
            $description .= "\n\nQuery: " . ($lead->query ?? 'No query provided.') . $extraInfo;
        }

        // Decide type (optional logic)
        $type = ($lead->query || $extraInfo) ? 'query' : 'history';

        // Create single activity
        Activity::create([
            'box_id' => $boxId,
            'activity_id' => $lead->id,
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'activity_date' => $created_at ?? now(),
            'added_by' => $added_by,
            'last_updated_by' => $added_by,
            'status' => 'Close',
            'completed_date' => $created_at ?? now(),
            'created_from' => $source_name,
        ]);

        // 6. Notification
        if ($isNew) {
            $notificationType = $lead->trashed_at ? 'trash' : 'assigned';

            if ($lead->assigned_to) {
                // Notify Assigned User
                \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $lead->assigned_to, $notificationType, null, $source_name);
            }

            // If trashed but no assignee, or if just new, notify admins
            if (!$lead->assigned_to || $lead->trashed_at) {
                $admins = \App\Models\BoxUser::where('box_id', $boxId)
                    ->whereIn('staff_role', ['Admin', 'Super Admin'])
                    ->where('user_id', '!=', $lead->assigned_to) // Don't notify twice
                    ->get();

                foreach ($admins as $admin) {
                    if ($admin->user_id) {
                        \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $admin->user_id, $notificationType, null, $source_name);
                    }
                }
            }
        }

        return $lead;
    }

    public function getTeamMembers(Request $request, $box_slug)
    {
        $userId = Auth::id();
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['status' => 'error', 'message' => 'Box not found']);
        }
        $boxId = $box->id;
        $boxUser = BoxUser::where('user_id', $userId)->where('box_id', $boxId)->first();
        if (!$boxUser) {
            return response()->json(['status' => 'success', 'data' => []]);
        }
        $subordinateIds = BoxUser::getAllSubordinateIds($boxId, $userId, $boxUser);
        $teamMembers = BoxUser::whereIn('id', $subordinateIds)
            ->with(['user:id,name,email'])
            ->get()
            ->map(function ($bu) use ($boxUser) {
                // Determine parent ID within the subordinates list
                // If the reporting manager is NOT in the list, set parent to current user (the root of this view)
                $parentId = $bu->reporting_manager_id;

                return [
                    'id' => $bu->user_id,
                    'box_user_id' => $bu->id,
                    'name' => $bu->user->name ?? 'Unknown',
                    'email' => $bu->user->email ?? '--',
                    'parent_box_user_id' => $parentId,
                    'is_root' => ($parentId === $boxUser->id || !$parentId)
                ];
            });

        return response()->json(['status' => 'success', 'data' => $teamMembers, 'root_box_user_id' => $boxUser->id]);
    }
}
