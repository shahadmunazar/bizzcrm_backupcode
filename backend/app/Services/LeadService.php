<?php
namespace App\Services;
use App\Models\Lead;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LeadService
{
    /**
     * Save a lead from various sources (WebHook, API, Facebook Sync, etc.)
     *
     * @param object $box
     * @param array $requestData
     * @param array $filterData
     * @param array $mapper
     * @param string|null $created_at
     * @param int|null $added_by
     * @param string $source_name
     * @return Lead
     */
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

        if ($allowDuplicate == 1 && ($email || $phone || $secEmail || $secPhone)) {
            $existingLead = Lead::where('box_id', $boxId)->where('duplicate_leads', 0)
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
                        // Combined fallback
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

    /**
     * Get paginated leads based on user role and search query
     */
    public function getLeads($box, $user, $boxUser, $search, $perPage = 15, $params = [])
    {
        $isCustomQuery = false;

        if (isset($params['todays_follow_up']) && ($params['todays_follow_up'] == 'true' || $params['todays_follow_up'] == 1)) {
            $params['reminder_filter'] = 'today';
            $query = \App\Http\Controllers\Leads\LeadsController::buildReminderLeadsQuery($box->id, $params);
            $isCustomQuery = true;
        } elseif (isset($params['pending_follow_up']) && ($params['pending_follow_up'] == 'true' || $params['pending_follow_up'] == 1)) {
            $params['reminder_filter'] = 'pending';
            $query = \App\Http\Controllers\Leads\LeadsController::buildReminderLeadsQuery($box->id, $params);
            $isCustomQuery = true;
        } elseif (isset($params['upcoming_follow_up']) && ($params['upcoming_follow_up'] == 'true' || $params['upcoming_follow_up'] == 1)) {
            $params['reminder_filter'] = 'upcoming';
            $query = \App\Http\Controllers\Leads\LeadsController::buildReminderLeadsQuery($box->id, $params);
            $isCustomQuery = true;
        } elseif (isset($params['today_dsr']) && ($params['today_dsr'] == 'true' || $params['today_dsr'] == 1)) {
            $params['reminder_filter'] = 'today';
            $query = \App\Http\Controllers\Leads\LeadsController::buildDSRLeadsQuery($box->id, $params);
            $isCustomQuery = true;
        } elseif (isset($params['this_month_dsr']) && ($params['this_month_dsr'] == 'true' || $params['this_month_dsr'] == 1)) {
            $params['reminder_filter'] = 'this_month';
            $query = \App\Http\Controllers\Leads\LeadsController::buildDSRLeadsQuery($box->id, $params);
            $isCustomQuery = true;
        } elseif (isset($params['all_dsr']) && ($params['all_dsr'] == 'true' || $params['all_dsr'] == 1)) {
            $params['reminder_filter'] = 'all';
            $query = \App\Http\Controllers\Leads\LeadsController::buildDSRLeadsQuery($box->id, $params);
            $isCustomQuery = true;
        } elseif (isset($params['all_activity']) && ($params['all_activity'] == 'true' || $params['all_activity'] == 1)) {
            $query = \App\Http\Controllers\Leads\LeadsController::buildAllActivityLeadsQuery($box->id, $params);
            $isCustomQuery = true;
        } else {
            $query = Lead::where('box_id', $box->id)
                ->with(['assignedTo', 'leadStatus', 'leadSubStatus']);

            // Check for 'all' parameter to include trashed records
            if (isset($params['all']) && ($params['all'] == 'true' || $params['all'] == 1)) {
                if (\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listall')) {
                    $query->withTrashed();
                } else {
                    $query->where('duplicate_leads', 0);
                }
            } elseif (isset($params['trashed']) && ($params['trashed'] == 'true' || $params['trashed'] == 1)) {
                if (\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listtrash')) {
                    $query->onlyTrashed();
                } else {
                    $query->whereRaw('1=0');
                }
            } else {
                if (isset($params['duplicate']) && ($params['duplicate'] == 'true' || $params['duplicate'] == 1)) {
                    if (\App\Helpers\PermissionHelper::hasModulePermission('leads', 'read', 'listduplicate')) {
                        $query->where('duplicate_leads', 1);
                    } else {
                        $query->whereRaw('1=0');
                    }
                } else {
                    $query->where('duplicate_leads', 0);
                }
            }

            // Apply Starred Filter
            if (isset($params['starred']) && ($params['starred'] == 'true' || $params['starred'] == 1)) {
                $query->where('is_starred', 1);
            }
        }

        if (!in_array($boxUser->staff_role, ['admin', 'superadmin'])) {
            if ($isCustomQuery) {
                $query->where('leads.assigned_to', $user->id);
            } else {
                $query->where('assigned_to', $user->id);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('leads.name', 'like', "%{$search}%")
                  ->orWhere('leads.email', 'like', "%{$search}%")
                  ->orWhere('leads.phone', 'like', "%{$search}%")
                  ->orWhere('leads.company', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new lead from API
     */
    public function createLead(array $data, $box, $user, $boxUser)
    {
        $data['box_id'] = $box->id;
        $data['added_by'] = $user->id;
        $data['last_updated_by'] = $user->id;
        
        if (!in_array($boxUser->staff_role, ['admin', 'superadmin']) && empty($data['assigned_to'])) {
            $data['assigned_to'] = $user->id;
        }

        $leadColumns = Schema::getColumnListing('leads');
        $leadData = array_intersect_key($data, array_flip($leadColumns));

        $lead = Lead::create($leadData);

        // Custom Fields
        $tableName = 'data_' . $box->id;
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            $customData = array_intersect_key($data, array_flip($columns));

            if (!empty($customData)) {
                unset($customData['id'], $customData['lead_id'], $customData['created_at'], $customData['updated_at']);
                $customData['lead_id'] = $lead->id;
                $customData['updated_at'] = now();
                DB::table($tableName)->updateOrInsert(
                    ['lead_id' => $lead->id],
                    $customData
                );
            }
        }

        return $lead;
    }

    /**
     * Update an existing lead from API
     */
    public function updateLead(Lead $lead, array $data, $user)
    {
        $data['last_updated_by'] = $user->id;

        $leadColumns = Schema::getColumnListing('leads');
        $leadData = array_intersect_key($data, array_flip($leadColumns));

        $lead->update($leadData);

        $tableName = 'data_' . $lead->box_id;
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            $customData = array_intersect_key($data, array_flip($columns));

            if (!empty($customData)) {
                unset($customData['id'], $customData['lead_id'], $customData['created_at'], $customData['updated_at']);
                $customData['lead_id'] = $lead->id;
                $customData['updated_at'] = now();
                DB::table($tableName)->updateOrInsert(
                    ['lead_id' => $lead->id],
                    $customData
                );
            }
        }

        return $lead;
    }

    /**
     * Delete a lead
     */
    public function deleteLead(Lead $lead)
    {
        return $lead->delete();
    }

    /**
     * Get form options (sources, statuses, assignees, etc.)
     */
    public function getFormOptions($boxId)
    {
        $sources = \App\Models\CrmSource::where('box_id', $boxId)
            ->get();

        $statuses = \App\Models\ContactStage::where('box_id', $boxId)
            ->get();
            
        $subStatuses = \App\Models\LeadSubStatus::where('box_id', $boxId)
            ->get();
            
        $industries = \App\Models\Industry::where('box_id', $boxId)
            ->get();

        $assignees = \App\Models\BoxUser::where('box_id', $boxId)
            ->with('user:id,name')
            ->where('status', '!=', 'Disabled')
            ->get()
            ->map(function ($boxUser) {
                if ($boxUser->user) {
                    return [
                        'id' => $boxUser->user->id,
                        'name' => $boxUser->user->name,
                        'role' => $boxUser->staff_role,
                    ];
                }
                return null;
            })
            ->filter()
            ->values();

        $departments = \App\Models\Department::where('box_id', $boxId)
            ->get();

        $designations = \App\Models\Team::where('box_id', $boxId)
            ->get();

        return [
            'sources' => $sources,
            'statuses' => $statuses,
            'sub_statuses' => $subStatuses,
            'industries' => $industries,
            'assignees' => $assignees,
            'departments' => $departments,
            'designations' => $designations,
        ];
    }
}
