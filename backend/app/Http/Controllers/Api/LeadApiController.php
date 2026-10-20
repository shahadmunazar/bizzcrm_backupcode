<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Box, Lead, BoxUser};
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{SystemField, BoxCustomField, ContactStage, LeadSubStatus, User, Activity, LeadAssignmentHistory};
use Illuminate\Support\Facades\{Log, Validator, DB, Schema};
use Carbon\Carbon;

class LeadApiController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }
    /**
     * Get leads for the logged-in user in the given box
     */
    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();

        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        // Get the user's role in this box
        $boxUser = BoxUser::where('box_id', $box->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$boxUser) {
            return response()->json(['success' => false, 'message' => 'User does not belong to this box'], 403);
        }

        // Use service to get paginated leads
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);
        $params = $request->all();
        $leads = $this->leadService->getLeads($box, $user, $boxUser, $search, $perPage, $params);

        return response()->json([
            'success' => true,
            'data' => $leads
        ]);
    }

    /**
     * Create or update a lead
     */
    public function store(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        if (!$boxUser) return response()->json(['success' => false, 'message' => 'Access denied'], 403);

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
        $boxId = $box->id;
        $tableName = 'data_' . $boxId;

        // Check if this is an update (has id) or create
        $isUpdate = $request->has('id') && !empty($request->input('id'));

        // Debug: Log assigned_to value from request
        Log::info(' Lead Store API - assigned_to debug:', [
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

        $uniqueField = $box->lead_required_field ?? 'email';
        if ($uniqueField === 'email') {
            $standardRules['email'] = str_replace('nullable', 'required', $standardRules['email']);
            if (!str_contains($standardRules['email'], 'required')) {
                $standardRules['email'] = 'required|' . $standardRules['email'];
            }
        } elseif ($uniqueField === 'phone') {
            $standardRules['phone'] = str_replace('nullable', 'required', $standardRules['phone']);
            if (!str_contains($standardRules['phone'], 'required')) {
                $standardRules['phone'] = 'required|' . $standardRules['phone'];
            }
        } elseif ($uniqueField === 'both') {
            $standardRules['email'] = str_replace('nullable', 'required', $standardRules['email']);
            if (!str_contains($standardRules['email'], 'required')) {
                $standardRules['email'] = 'required|' . $standardRules['email'];
            }
            $standardRules['phone'] = str_replace('nullable', 'required', $standardRules['phone']);
            if (!str_contains($standardRules['phone'], 'required')) {
                $standardRules['phone'] = 'required|' . $standardRules['phone'];
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


        $allowDuplicate = $box->allow_duplicate_leads ?? 0;

        $isDuplicate = 0;
        $existingLead = null;
        $email = $standardData['email'] ?? null;
        $secEmail = $standardData['sec_email'] ?? null;
        $phone = $standardData['phone'] ?? null;
        $secPhone = $standardData['sec_phone'] ?? null;

        if (($allowDuplicate == 1 || $isUpdate) && ($email || $phone || $secEmail || $secPhone)) {
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
                    } else {
                        // Default fallback / Combined Check
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
        // If we found a duplicate and allow_duplicate_leads is enabled
        // we flag it to add an activity log to the parent record.
        if (!$isUpdate && $existingLead && $allowDuplicate == 1) {
            $isMergedDuplicate = true;
        }

        $now = Carbon::now();
        if ($isUpdate && !empty($standardData['id'])) {
            $lead = Lead::findOrFail($standardData['id']);

            // Ensure lead belongs to this box
            if ($lead->box_id != $boxId) {
                return response()->json(['success' => false, 'message' => 'Lead not found in this box'], 404);
            }

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

            // Auto-track Last Lead Status
            $newStatusId = $standardData['lead_status'] ?? $standardData['lead_status_id'] ?? null;
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
                Activity::create([
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
                'name' => $standardData['lead_name'] ?? 'New Lead', // Ensure a fallback
                'last_name' => $standardData['last_name'] ?? null,
                'sec_email' => $standardData['sec_email'] ?? null,
                'company_name' => $standardData['company_name'] ?? null,
                'designation' => $standardData['designation'] ?? null,
                'email' => $standardData['email'] ?? null,
                'phone' => $standardData['phone'] ?? null,
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

            $lead->refresh();

            if ($isDuplicate) {
                \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $authId, 'duplicate');
            } elseif ($lead->assigned_to) {
                \App\Jobs\SendNewLeadNotificationJob::dispatch($lead->id, $lead->assigned_to);
            }

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

        $filteredCustomData = collect($customData)->except(['lead_id', 'updated_at'])->filter(function ($value) {
            return !is_null($value) && $value !== '';
        });

        if ($filteredCustomData->isNotEmpty()) {
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

    /**
     * Update an existing lead
     */
    public function update(Request $request, $box_slug, $id)
    {
        // The update method uses the same logic as store
        // It checks for 'id' in the request to determine if it's an update
        $request->merge(['id' => $id]);
        return $this->store($request, $box_slug);
    }

    /**
     * Delete a lead
     */
    public function destroy(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $user = Auth::user();
        $boxUser = BoxUser::where('box_id', $box->id)->where('user_id', $user->id)->first();
        if (!$boxUser) return response()->json(['success' => false, 'message' => 'Access denied'], 403);

        $lead = Lead::where('box_id', $box->id)->find($id);
        if (!$lead) return response()->json(['success' => false, 'message' => 'Lead not found'], 404);

        // Usually only admin/superadmin can delete leads, or the owner
        if (!in_array($boxUser->staff_role, ['admin', 'superadmin'])) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to delete leads'], 403);
        }

        // Use service to delete lead
        $this->leadService->deleteLead($lead);

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully'
        ]);
    }

    /**
     * Get options for dropdowns (lead sources, statuses, and users for assignment)
     */
    
    /**
     * Get dynamic form fields structure (System fields and Custom fields)
     */
    public function getFormFields(Request $request, $box_slug)
    {
        try {
            $box = \App\Models\Box::where('slug', $box_slug)->first();
            if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

            $boxUser = \App\Models\BoxUser::where('box_id', $box->id)->where('user_id', $user->id)->first();
            if (!$boxUser) return response()->json(['success' => false, 'message' => 'Access denied'], 403);

            $fields = \App\Models\SystemField::where('object', 'leads')
                ->where('box_id', $box->id)
                ->where('is_visible', true)
                ->orderBy('sequence')
                ->get();

            $customFields = \App\Models\BoxCustomField::where('box_id', $box->id)
                ->where('object', 'leads')
                ->where('status', 'Enabled')
                ->orderBy('ordering')
                ->get();

            $mergedFields = collect();
            foreach ($fields as $field) {
                $field->is_custom = false;
                if (($field->field !== 'assigned_to' && (strtolower($field->name) === 'owner' || strtolower($field->name) === 'lead owner')) || strtolower($field->name) === 'created on') continue;
                if (isset($field->is_visible) && $field->is_visible == 0) continue;
                if ($customFields->contains('col_name', $field->field)) continue;
                $mergedFields->push($field);
            }

            foreach ($customFields as $customField) {
                if (isset($customField->status) && $customField->status === 'Disabled') continue;
                $customField->is_custom = true;
                $customField->sequence = $customField->ordering;
                $mergedFields->push($customField);
            }

            $mergedFields = $mergedFields->sortBy('sequence');

            return response()->json([
                'success' => true,
                'status' => true,
                'data' => $mergedFields->values()
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error loading dynamic form fields API: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load form fields.'], 500);
        }
    }

    /**
     * Get options
     */
    public function getFormOptions(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

        $boxUser = BoxUser::where('box_id', $box->id)->where('user_id', $user->id)->first();
        if (!$boxUser) return response()->json(['success' => false, 'message' => 'Access denied'], 403);

        // Use service to get dropdown options
        $options = $this->leadService->getFormOptions($box->id);

        return response()->json([
            'success' => true,
            'data' => $options
        ]);
    }

    /**
     * Get details of a single lead
     */
    public function show(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

        $boxUser = BoxUser::where('box_id', $box->id)->where('user_id', $user->id)->first();
        if (!$boxUser) return response()->json(['success' => false, 'message' => 'Access denied'], 403);

        $lead = Lead::where('box_id', $box->id)->with([
            'country',
            'state',
            'city',
            'leadSource',
            'leadStatus',
            'assignedTo',
            'industryRelation'
        ])->find($id);

        if (!$lead) return response()->json(['success' => false, 'message' => 'Lead not found'], 404);

        // Access checks
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canAccess = $isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('leads', 'view', 'list') || $lead->assigned_to == $user->id;
        
        if (!$canAccess) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this lead'
            ], 403);
        }

        $tableName = 'data_' . $box->id;
        $customData = null;
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            $columnsToSelect = array_diff($columns, ['id', 'lead_id', 'created_at', 'updated_at']);

            if (!empty($columnsToSelect)) {
                $customData = DB::table($tableName)
                    ->where('lead_id', $lead->id)
                    ->select($columnsToSelect)
                    ->first();
            }
        }

        return response()->json([
            'success' => true,
            'status' => true,
            'data' => [
                'lead' => $lead,
                'custom_data' => $customData,
            ]
        ]);
    }

    /**
     * Get all countries
     */
    public function getCountries(Request $request, $box_slug)
    {
        $countries = \App\Models\Country::select('id', 'name')->get();
        return response()->json(['success' => true, 'data' => $countries]);
    }

    /**
     * Get states by country
     */
    public function getStates(Request $request, $box_slug, $country_id)
    {
        $states = \App\Models\State::where('country_id', $country_id)->select('id', 'name')->get();
        return response()->json(['success' => true, 'data' => $states]);
    }

    /**
     * Get cities by state
     */
    public function getCities(Request $request, $box_slug, $state_id)
    {
        $cities = \App\Models\City::where('state_id', $state_id)->select('id', 'name')->get();
        return response()->json(['success' => true, 'data' => $cities]);
    }

    /**
     * Get team members and hierarchical tree for leads scoping
     */
    public function getTeamMembers(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $boxUser = BoxUser::where('box_id', $box->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$boxUser) {
            return response()->json(['success' => false, 'message' => 'User does not belong to this box'], 403);
        }

        $result = $this->leadService->getTeamHierarchy($box, $user, $boxUser);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'root_box_user_id' => $result['root_box_user_id'],
            'data' => $result['data'],
            'tree' => $result['tree']
        ]);
    }
}
