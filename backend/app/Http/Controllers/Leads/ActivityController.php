<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Models\{Activity, ActivityStatus, ActivityType, BoxUser, Box, Call, ContactStage, Lead, LeadSubStatus, User, State, City,Division, DivisionArea,AreaLead};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log, Storage, Validator};
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Jobs\SendTaskNotificationJob; // Added

class ActivityController extends Controller
{
    /**
     * Resolve the Box ID for the current request.
     * Uses route slug if provided, otherwise falls back to the authenticated user's assigned box.
     */
    private function resolveBoxId(?string $boxSlug = null): ?int
    {
        if ($boxSlug) {
            $box = Box::where('slug', $boxSlug)->value('id');
            if ($box) return $box;
        }

        return BoxUser::where('user_id', Auth::id())->value('box_id');
    }

    /**
     * Standard error response helper.
     */
    private function errorResponse(string $message, int $code = 500, ?string $errorDetails = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];
        if ($errorDetails) {
            $response['error'] = $errorDetails;
        }
        return response()->json($response, $code);
    }

    /**
     * Get activities for a specific lead.
     */
    public function getActivities(Request $request): JsonResponse
    {
        try {
            $leadId = $request->input('lead_id');
            if (!$leadId) {
                return $this->errorResponse('Lead ID is required.', 400);
            }

            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) {
                return $this->errorResponse('Box not found for the user.', 404);
            }

            // Verify lead access (exists and belongs to box). Use withTrashed() to support leads in trash.
            $hasAccess = Lead::withTrashed()->where('id', $leadId)->where('box_id', $boxId)->exists();
            if (!$hasAccess) {
                return $this->errorResponse('Lead not found or you do not have access to it.', 404);
            }

            // Fetch activities with necessary relationships
            $activities = Activity::where('activity_id', $leadId)
                ->where('box_id', $boxId)
                ->with([
                    'assignedUser:id,name',
                    'addedByUser:id,name',
                    'activityStatus:id,activity_status',
                    'activityTypeData:id,activity_type',
                    'closedByUser:id,name',
                    'leadStatus:id,name,color',
                    'leadSubStatus:id,name'
                ])
                ->orderBy('activity_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $activities]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch activities.', 500, $th->getMessage());
        }
    }

    /**
     * Get all activities for the box.
     */
    public function getAllActivities(Request $request): JsonResponse
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) {
                return $this->errorResponse('Box not found for the user.', 404);
            }

            // Fetch activities with necessary relationships
            $activitiesQuery = Activity::where('box_id', $boxId)
                ->with([
                    'assignedUser:id,name',
                    'addedByUser:id,name',
                    'activityStatus:id,activity_status',
                    'activityTypeData:id,activity_type',
                    'closedByUser:id,name',
                    'leadStatus:id,name,color',
                    'leadSubStatus:id,name'
                ])
                ->orderBy('activity_date', 'desc')
                ->orderBy('created_at', 'desc');
                
            // Optional filter by lead_id (supports single, comma-separated, or array) if provided
            $leadIdParam = $request->input('lead_id') ?? $request->input('leads_id');
            if (!empty($leadIdParam)) {
                $leadIds = is_array($leadIdParam) ? $leadIdParam : explode(',', $leadIdParam);
                $activitiesQuery->whereIn('activity_id', $leadIds);
            }

            // Optional filter by activity type (e.g., call, email, whatsapp, note)
            if ($request->filled('type')) {
                $types = is_array($request->input('type')) ? $request->input('type') : explode(',', $request->input('type'));
                $activitiesQuery->whereIn('type', $types);
            }

            // Optional filter by status (Open, Close, Failed)
            if ($request->filled('status')) {
                $statuses = is_array($request->input('status')) ? $request->input('status') : explode(',', $request->input('status'));
                $activitiesQuery->whereIn('status', $statuses);
            }

            // Optional filter by Assigned To User ID
            if ($request->filled('assigned_to')) {
                $activitiesQuery->where('assigned_to', $request->input('assigned_to'));
            }

            // Optional filter by Added By User ID
            if ($request->filled('added_by')) {
                $activitiesQuery->where('added_by', $request->input('added_by'));
            }

            // Optional filter by Activity Type ID (Sub-type setting)
            if ($request->filled('activity_type')) {
                $activitiesQuery->where('activity_type', $request->input('activity_type'));
            }

            // Optional filter by Activity Status ID (Custom dropdown statuses)
            if ($request->filled('activity_status_id')) {
                $activitiesQuery->where('activity_status_id', $request->input('activity_status_id'));
            }

            // Optional filter by Date Range (activity_date)
            if ($request->filled('date_from')) {
                $activitiesQuery->whereDate('activity_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $activitiesQuery->whereDate('activity_date', '<=', $request->input('date_to'));
            }

            // Paginate the results (e.g., 50 per page)
            $activities = $activitiesQuery->paginate(50);

            return response()->json(['success' => true, 'data' => $activities]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch activities.', 500, $th->getMessage());
        }
    }

    /**
     * Store a new activity.
     */
    public function store(Request $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'lead_id'            => 'required|integer',
            'type'               => 'required|in:call,email,meeting,whatsapp,note,general',
            'activity_type'      => 'nullable|string',
            'activity_status_id' => 'nullable|integer',
            'subject'            => 'nullable|string|max:255',
            'description'        => 'nullable|string',
            'activity_date'      => 'required|date',
            'reminder_date'      => 'nullable|date',
            'assigned_to'        => 'nullable|integer',
            'lead_status_id'     => 'nullable|integer',
            'lead_sub_status_id' => 'nullable|integer',
            'files.*'            => 'nullable|file|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $boxId = $this->resolveBoxId();
            if (!$boxId) {
                return $this->errorResponse('Box not found for the user.', 404);
            }

            $userId = Auth::id();
            $lead = Lead::where('id', $request->lead_id)->where('box_id', $boxId)->first();

            if (!$lead) {
                return $this->errorResponse('Lead not found or you do not have access to it.', 404);
            }

            // File Uploads
            $params = $request->params ?? [];
            if ($request->hasFile('files')) {
                $uploadedFiles = $this->handleFileUploads($request->file('files'));
                if (!empty($uploadedFiles)) {
                    $params['files'] = $uploadedFiles;
                }
            }

            // Prepare Data
            $assignedToId = $request->assigned_to ?? $userId;

            // Resolve activity status name for auto-updates and subject
            $statusNameLabel = 'New';
            if ($request->activity_status_id) {
                $statusObj = ActivityStatus::find($request->activity_status_id);
                if ($statusObj) $statusNameLabel = $statusObj->activity_status;
            }

            // Auto-update lead status to "Contacted" (Stage ID 1) if call is PickedUp or Answered
            if (in_array(strtolower($statusNameLabel), ['answer', 'pickedup', 'answered', 'picked up'])) {
                $contactedStatusId = ContactStage::where('box_id', $boxId)->where('name', 'Contacted')->value('id');
                if ($contactedStatusId && $lead->lead_status_id != $contactedStatusId) {
                    // Only auto-update if lead_status_id is NOT in the request (user didn't manually change it)
                    // or if it matches current status (no requested change)
                    if (!$request->filled('lead_status_id') || $request->lead_status_id == $lead->lead_status_id) {
                        $request->merge(['lead_status_id' => $contactedStatusId]);
                    }
                }
            }

            // Auto-generate Subject if missing
            $subject = $request->subject;
            /*
             * NOTE: Subject generation is disabled here to save database storage space.
             * The subject is now dynamically constructed on the frontend instead.
             * DO NOT REMOVE THIS COMMENTED CODE (kept for reference, AI/developers must not delete).
             *
            if (!$subject) {
                $userName = Auth::user()->name;
                $typeName = strtolower($request->type);

                // 1. Resolve activity sub-type name (e.g. Cold Call, Sales)
                $activityTypeLabel = '';
                if ($request->activity_type) {
                    $typeObj = ActivityType::find($request->activity_type);
                    if ($typeObj) $activityTypeLabel = $typeObj->activity_type;
                }

                // 2. Already resolved $statusNameLabel above

                // 3. Resolve assignment name
                $assignedToId = $request->assigned_to ?? $userId;
                $assignedName = ($assignedToId == $userId) ? 'Self' : (User::find($assignedToId)->name ?? 'Unknown');

                $subject = "{$userName} created a {$activityTypeLabel} task {$typeName} with status {$statusNameLabel} and assigned to {$assignedName}";

                if ($request->type === 'note') {
                    $subject = "{$userName} added a Note";
                }
            }
            */
            $assignedToId = $request->assigned_to ?? $userId;

            // Create Activity
            $activity = Activity::create([
                'box_id'             => $boxId,
                'activity_id'        => $request->lead_id,
                'activity_type'      => $request->activity_type,
                'activity_status_id' => $request->activity_status_id,
                'lead_status_id'     => $request->lead_status_id,
                'lead_sub_status_id' => $request->lead_sub_status_id,
                'label'              => $request->label,
                'subject'            => $subject,
                'description'        => $request->description ?? $request->remarks,
                'type'               => $request->type,
                'task_type'          => $request->task_type,
                'activity_date'      => $request->activity_date,
                'reminder_date'      => $request->reminder_date,
                'assigned_to'        => $assignedToId,
                'added_by'           => $userId,
                'last_updated_by'    => $userId,
                'status'             => 'Close',
                'completed_date'     => now(),
                'closed_by'          => $userId,
                'params'             => json_encode($params),
            ]);

            // Dispatch notification for the main activity
            if ($activity && $activity->assigned_to) {
                SendTaskNotificationJob::dispatch($activity->id, $activity->assigned_to);
            }

            // Create Follow-up if requested
            $followUpActivity = null;
            if ($request->boolean('followup_task')) {
                $followUpActivity = $this->createFollowUpTask($request, $boxId, $userId, $activity->activity_id);
                // Dispatch notification job for the new follow-up task
                if ($followUpActivity && $followUpActivity->assigned_to) {
                    SendTaskNotificationJob::dispatch($followUpActivity->id, $followUpActivity->assigned_to);
                }
            }

            // Update Lead Status/Assignment and collect history messages
            $leadUpdateData = [];
            
            // Separate arrays for different types of history
            $statusDescriptionParts = [];
            $statusHistoryParams = [];
            
            $assigneeDescriptionParts = [];
            $assigneeHistoryParams = [];

            if ($request->filled('lead_status_id') && $lead->lead_status_id != $request->lead_status_id) {
                $leadUpdateData['lead_status_id'] = $request->lead_status_id;
                $params['lead_status_id'] = $request->lead_status_id;
                $status = ContactStage::find($request->lead_status_id);
                if ($status) {
                    $params['lead_status_name'] = $status->name;
                    $statusHistoryParams['lead_status_name'] = $status->name;
                    $statusHistoryParams['lead_status_id'] = $request->lead_status_id;
                    $statusDescriptionParts[] = "Lead Status changed to " . $status->name;
                }
            }

            if ($request->filled('lead_sub_status_id') && $lead->lead_sub_status_id != $request->lead_sub_status_id) {
                $leadUpdateData['lead_sub_status_id'] = $request->lead_sub_status_id;
                $params['lead_sub_status_id'] = $request->lead_sub_status_id;
                $subStatus = LeadSubStatus::find($request->lead_sub_status_id);
                if ($subStatus) {
                    $params['lead_sub_status_name'] = $subStatus->name;
                    $statusHistoryParams['lead_sub_status_name'] = $subStatus->name;
                    $statusHistoryParams['lead_sub_status_id'] = $request->lead_sub_status_id;
                    $statusDescriptionParts[] = "Lead Sub Status changed to " . $subStatus->name;
                }
            }

            if ($request->filled('assigned_to') && $lead->assigned_to != $request->assigned_to) {
                $oldAssigneeId = $lead->assigned_to;
                $newAssigneeId = $request->assigned_to;
                $oldAssignee = User::find($oldAssigneeId);
                $newAssignee = User::find($newAssigneeId);

                $oldName = $oldAssignee ? $oldAssignee->name : 'Unassigned';
                $newName = $newAssignee ? $newAssignee->name : 'Unassigned';

                $params['previous_assignee_name'] = $oldName;
                $params['current_assignee_name'] = $newName;
                $assigneeHistoryParams['previous_assignee_name'] = $oldName;
                $assigneeHistoryParams['current_assignee_name'] = $newName;

                $assigneeDescriptionParts[] = "Assigned To changed from {$oldName} to {$newName}";
                $leadUpdateData['assigned_to'] = $newAssigneeId;
            }

            if ($followUpActivity) {
                $params['followup_created'] = true;
                $params['followup_date'] = $followUpActivity->activity_date->format('d M Y, h:i a');
            }

            if (!empty($leadUpdateData)) {
                $leadUpdateData['last_updated_by'] = $userId;
                $lead->update($leadUpdateData);

                // 1. Create Status Change History
                if (!empty($statusDescriptionParts)) {
                    Activity::create([
                        'box_id'          => $boxId,
                        'activity_id'     => $lead->id,
                        'type'            => 'history',
                        'subject'         => Auth::user()->name . ' updated Lead Status',
                        'description'     => implode("\n", $statusDescriptionParts),
                        'activity_date'   => now(),
                        'added_by'        => $userId,
                        'last_updated_by' => $userId,
                        'status'          => 'Close',
                        'completed_date'  => now(),
                        'params'          => json_encode($statusHistoryParams),
                    ]);
                }

                // 2. Create Assignment Change History
                if (!empty($assigneeDescriptionParts)) {
                    Activity::create([
                        'box_id'          => $boxId,
                        'activity_id'     => $lead->id,
                        'type'            => 'history',
                        'subject'         => Auth::user()->name . ' updated Assignment',
                        'description'     => implode("\n", $assigneeDescriptionParts),
                        'activity_date'   => now(), // Slightly offset if needed for ordering? created_at handles it.
                        'added_by'        => $userId,
                        'last_updated_by' => $userId,
                        'status'          => 'Close',
                        'completed_date'  => now(),
                        'params'          => json_encode($assigneeHistoryParams),
                    ]);
                }
            }

            // Always update params for the main activity
            $activity->update(['params' => json_encode($params)]);

            // Update Activity with latest params
            $activity->update(['params' => json_encode($params)]);

            $activity->load(['assignedUser:id,name', 'addedByUser:id,name', 'activityStatus']);

            return response()->json([
                'success' => true,
                'message' => 'Activity created successfully.',
                'data' => $activity,
                'follow_up_activity' => $followUpActivity
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to create activity.', 500, $th->getMessage());
        }
    }

    /**
     * Update an existing activity.
     */
    public function update(Request $request, $box_slug, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'               => 'sometimes|in:call,email,meeting,whatsapp,note,general',
            'activity_type'      => 'nullable|string',
            'activity_status_id' => 'nullable|integer',
            'subject'            => 'nullable|string|max:255',
            'activity_date'      => 'sometimes|date',
            'status'             => 'sometimes|in:Open,Close,Failed',
            'lead_status_id'     => 'nullable|integer',
            'lead_sub_status_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $boxId = $this->resolveBoxId($box_slug);
            if (!$boxId) {
                return $this->errorResponse('Box not found.', 404);
            }

            $userId = Auth::id();
            $activity = Activity::where('id', $id)->where('box_id', $boxId)->first();

            if (!$activity) {
                return $this->errorResponse('Activity not found or you do not have access to it.', 404);
            }

            // Update Fields
            $updateData = $request->only([
                'activity_type',
                'activity_status_id',
                'label',
                'subject',
                'description',
                'type',
                'task_type',
                'activity_date',
                'reminder_date',
                'assigned_to',
                'status',
                'lead_status_id',
                'lead_sub_status_id'
            ]);

            // Handle remarks mapping
            if (!$request->has('description') && $request->has('remarks')) {
                $updateData['description'] = $request->remarks;
            }

            // Default assignment
            if (array_key_exists('assigned_to', $updateData) && empty($updateData['assigned_to'])) {
                $updateData['assigned_to'] = $userId;
            }

            $updateData['last_updated_by'] = $userId;
            if ($request->has('params')) {
                $updateData['params'] = json_encode($request->params);
            }

            if ($request->has('status') && in_array($request->status, ['Close', 'Completed', 'Closed'])) {
                $updateData['completed_date'] = now();
                $updateData['closed_by'] = $userId;
            }

            $activity->update($updateData);

            // Update Lead and Activity Params
            $lead = Lead::find($activity->activity_id);
            if ($lead) {
                $leadData = ['last_updated_by' => $userId];
                $paramsChanged = false;
                $activityParams = is_string($activity->params) ? json_decode($activity->params, true) : ($activity->params ?? []);

                // Auto-update lead status to "Contacted" if activity status is Answer/PickedUp
                if ($request->filled('activity_status_id')) {
                    $statusObj = ActivityStatus::find($request->activity_status_id);
                    if ($statusObj && in_array(strtolower($statusObj->activity_status), ['answer', 'pickedup', 'answered', 'picked up'])) {
                        $contactedStatusId = ContactStage::where('box_id', $boxId)->where('name', 'Contacted')->value('id');
                        if ($contactedStatusId && $lead->lead_status_id != $contactedStatusId) {
                            if (!$request->filled('lead_status_id') || $request->lead_status_id == $lead->lead_status_id) {
                                $request->merge(['lead_status_id' => $contactedStatusId]);
                            }
                        }
                    }
                }

                // Track actual changes
                $statusChanged = false;
                $subStatusChanged = false;
                $assigneeChanged = false;

                // Lead Status
                if ($request->filled('lead_status_id') && $lead->lead_status_id != $request->lead_status_id) {
                    $leadData['lead_status_id'] = $request->lead_status_id;
                    $status = ContactStage::find($request->lead_status_id);
                    if ($status) {
                        $activityParams['lead_status_name'] = $status->name;
                        $activityParams['lead_status_id'] = $request->lead_status_id;
                        $statusChanged = true;
                    }
                    $paramsChanged = true;
                }

                // Lead Sub Status
                if ($request->filled('lead_sub_status_id') && $lead->lead_sub_status_id != $request->lead_sub_status_id) {
                    $leadData['lead_sub_status_id'] = $request->lead_sub_status_id;
                    $subStatus = LeadSubStatus::find($request->lead_sub_status_id);
                    if ($subStatus) {
                        $activityParams['lead_sub_status_name'] = $subStatus->name;
                        $activityParams['lead_sub_status_id'] = $request->lead_sub_status_id;
                        $subStatusChanged = true;
                    }
                    $paramsChanged = true;
                }

                // Assignee Change
                if ($request->filled('assigned_to') && $lead->assigned_to != $request->assigned_to) {
                    $oldAssigneeId = $lead->assigned_to;
                    $newAssigneeId = $request->assigned_to;

                    $oldAssignee = User::find($oldAssigneeId);
                    $newAssignee = User::find($newAssigneeId);

                    $activityParams['previous_assignee_name'] = $oldAssignee ? $oldAssignee->name : 'Unassigned';
                    $activityParams['current_assignee_name'] = $newAssignee ? $newAssignee->name : 'Unassigned';
                    
                    $leadData['assigned_to'] = $newAssigneeId;
                    $assigneeChanged = true;
                    $paramsChanged = true;
                }

                if (count($leadData) > 1) { // More than just last_updated_by
                    $lead->update($leadData);

                    // Create separate History activities for the changes
                    $statusDescriptionParts = [];
                    $statusHistoryParams = [];
                    
                    $assigneeDescriptionParts = [];
                    $assigneeHistoryParams = [];

                    // Collect Status Changes
                    if ($statusChanged && isset($activityParams['lead_status_name'])) {
                        $statusDescriptionParts[] = "Lead Status changed to " . $activityParams['lead_status_name'];
                        $statusHistoryParams['lead_status_name'] = $activityParams['lead_status_name'];
                        $statusHistoryParams['lead_status_id'] = $activityParams['lead_status_id'];
                    }
                    if ($subStatusChanged && isset($activityParams['lead_sub_status_name'])) {
                        $statusDescriptionParts[] = "Lead Sub Status changed to " . $activityParams['lead_sub_status_name'];
                        $statusHistoryParams['lead_sub_status_name'] = $activityParams['lead_sub_status_name'];
                        $statusHistoryParams['lead_sub_status_id'] = $activityParams['lead_sub_status_id'];
                    }
                    
                    // Collect Assignment Changes
                    if ($assigneeChanged && isset($activityParams['current_assignee_name'])) {
                        $assigneeDescriptionParts[] = "Assigned To changed from {$activityParams['previous_assignee_name']} to {$activityParams['current_assignee_name']}";
                        $assigneeHistoryParams['previous_assignee_name'] = $activityParams['previous_assignee_name'];
                        $assigneeHistoryParams['current_assignee_name'] = $activityParams['current_assignee_name'];
                    }

                    // 1. Create Status History
                    if (!empty($statusDescriptionParts)) {
                        Activity::create([
                            'box_id'          => $boxId,
                            'activity_id'     => $lead->id,
                            'type'            => 'history',
                            'subject'         => Auth::user()->name . ' updated Lead Status',
                            'description'     => implode("\n", $statusDescriptionParts),
                            'activity_date'   => now(),
                            'added_by'        => $userId,
                            'last_updated_by' => $userId,
                            'status'          => 'Close',
                            'completed_date'  => now(),
                            'params'          => json_encode($statusHistoryParams),
                        ]);
                    }
                    
                    // 2. Create Assignment History
                    if (!empty($assigneeDescriptionParts)) {
                        Activity::create([
                            'box_id'          => $boxId,
                            'activity_id'     => $lead->id,
                            'type'            => 'history',
                            'subject'         => Auth::user()->name . ' updated Assignment',
                            'description'     => implode("\n", $assigneeDescriptionParts),
                            'activity_date'   => now(),
                            'added_by'        => $userId,
                            'last_updated_by' => $userId,
                            'status'          => 'Close',
                            'completed_date'  => now(),
                            'params'          => json_encode($assigneeHistoryParams),
                        ]);
                    }
                }

                if ($paramsChanged) {
                    $activity->update(['params' => json_encode($activityParams)]);
                }
            }

            // Handle Follow-up (Specific Logic as requested)
            if ($request->boolean('create_followup') && $request->filled('followup_type')) {
                $fuParams = [];
                if ($request->followup_type == 'meeting' && $request->filled('followup_meeting_date')) {
                    $fuParams['meeting_date'] = $request->followup_meeting_date;
                }
                /*
                 * NOTE: Subject generation is disabled here to save database storage space.
                 * The subject is now dynamically constructed on the frontend instead.
                 * DO NOT REMOVE THIS COMMENTED CODE (kept for reference, AI/developers must not delete).
                 *
                // Resolve status name for subject
                $statusName = 'New';
                if ($request->activity_status_id) {
                    $statusObj = ActivityStatus::find($request->activity_status_id);
                    if ($statusObj) $statusName = $statusObj->activity_status;
                }
                */

                $assignedToId = $request->followup_assigned_to ?? $userId;
                /*
                $assignedName = ($assignedToId == $userId) ? 'Self' : (User::find($assignedToId)->name ?? 'Unknown');
                $fuType = ucfirst($request->followup_type);
                $userName = Auth::user()->name;

                $fuSubject = "{$userName} created a Follow up {$fuType} with status {$statusName} and assigned to {$assignedName}";
                */
                // Note: Using requested activity_type for the follow-up
                Activity::create([
                    'box_id'          => $boxId,
                    'activity_id'     => $activity->activity_id,
                    'activity_type'   => $request->input('activity_type'),
                    'type'            => $request->followup_type,
                    'subject'         => null,
                    'description'     => $request->followup_remarks,
                    'activity_date'   => $request->followup_date,
                    'assigned_to'     => $assignedToId,
                    'added_by'        => $userId,
                    'last_updated_by' => $userId,
                    'status'          => 'Open',
                    'params'          => json_encode($fuParams),
                ]);
            }

            $activity->load(['assignedUser:id,name', 'addedByUser:id,name', 'activityStatus']);

            return response()->json(['success' => true, 'message' => 'Activity updated successfully.', 'data' => $activity]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to update activity.', 500, $th->getMessage());
        }
    }

    /**
     * Delete an activity.
     */
    public function destroy(Request $request, $box_slug, $id): JsonResponse
    {
        try {
            $boxId = $this->resolveBoxId($box_slug);
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $activity = Activity::where('id', $id)->where('box_id', $boxId)->first();
            if (!$activity) return $this->errorResponse('Activity not found.', 404);

            $activity->delete();

            return response()->json(['success' => true, 'message' => 'Activity deleted successfully.']);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to delete activity.', 500, $th->getMessage());
        }
    }

    // --- Helpers ---

    private function handleFileUploads($files): array
    {
        $uploaded = [];
        if (!Storage::disk('public')->exists('activity_files')) {
            Storage::disk('public')->makeDirectory('activity_files');
        }

        foreach ($files as $file) {
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('activity_files', $fileName, 'public');

            if ($path) {
                $uploaded[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'file_name'     => $fileName,
                    'file_path'     => $path,
                    'file_size'     => $file->getSize(),
                    'mime_type'     => $file->getMimeType(),
                ];
            }
        }
        return $uploaded;
    }

    private function createFollowUpTask($request, $boxId, $userId, $leadId)
    {
        $type = $request->followup_task_type ?? 'task';

        // Resolve dynamic activity setting if ID is passed
        if (is_numeric($type)) {
            $setting = Call::find($type);
            if ($setting) {
                $type = $setting->name ?? 'task';
            }
        }

        $date = $request->followup_task_date ?: now()->addDay();
        $assignedTo = $request->followup_task_assign_to;
        if (empty($assignedTo) || $assignedTo === 'self') {
            $assignedTo = $userId;
        }
        /*
         * NOTE: Subject generation is disabled here to save database storage space.
         * The subject is now dynamically constructed on the frontend instead.
         * DO NOT REMOVE THIS COMMENTED CODE (kept for reference, AI/developers must not delete).
         *
        // Resolve status name for subject
        $statusName = 'New';
        if ($request->activity_status_id) {
            $statusObj = ActivityStatus::find($request->activity_status_id);
            if ($statusObj) $statusName = $statusObj->activity_status;
        }

        $assignedName = ($assignedTo == $userId) ? 'Self' : (User::find($assignedTo)->name ?? 'Unknown');
        $fuType = ucfirst($type);
        $userName = Auth::user()->name;

        $subject = "{$userName} created a Follow up Task {$fuType} with status {$statusName} and assigned to {$assignedName}";
        */
        $params = ['priority' => $request->followup_task_priority];
        if ($request->filled('followup_meeting_date')) {
            $params['meeting_date'] = $request->followup_meeting_date;
        }

        $activity = Activity::create([
            'box_id'          => $boxId,
            'activity_type'   => $request->activity_type, // Do not inherit parent sub-type
            'activity_id'     => $leadId,
            'lead_status_id' => $request->lead_status_id,
            'lead_sub_status_id' => $request->lead_sub_status_id,
            'type'            => strtolower($type),
            'subject'         => null,
            'description'     => $request->description ?? $request->remarks,
            'activity_date'   => $date,
            'assigned_to'     => $assignedTo,
            'added_by'        => $userId,
            'last_updated_by' => $userId,
            'status'          => 'Open',
            'params'          => json_encode($params),
        ]);

        $activity->load(['assignedUser:id,name', 'addedByUser:id,name', 'activityStatus']);
        return $activity;
    }

    // --- Dropdown Option Methods (Optimized) ---

    public function getActivityOptions(Request $request): JsonResponse
    {
        return $this->getOptionsData($request, 'options');
    }

    public function getActivityUsers(Request $request): JsonResponse
    {
        return $this->getOptionsData($request, 'users');
    }

    public function getLeadStatuses(Request $request): JsonResponse
    {
        return $this->getOptionsData($request, 'statuses');
    }

    public function getLeadSubStatuses(Request $request): JsonResponse
    {
        return $this->getOptionsData($request, 'sub-statuses');
    }

    private function getOptionsData(Request $request, string $mode): JsonResponse
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $leadId = $request->input('lead_id');
            $data = [];

            if ($mode === 'options' || $mode === 'users') {
                $userIds = BoxUser::where('box_id', $boxId)->pluck('user_id');
                $users = User::whereIn('id', $userIds)->select('id', 'name')->orderBy('name')->get();
                $data['users'] = $users;
            }

            if ($mode === 'options' || $mode === 'statuses') {
                $data['lead_statuses'] = ContactStage::where('box_id', $boxId)
                    ->select('id', 'name', 'task_priority', 'reminder_mandatory')
                    ->orderBy('name')->get();
            }

            if ($mode === 'sub-statuses') {
                $leadStatusId = $request->input('lead_status_id');
                $data = LeadSubStatus::where('box_id', $boxId)
                    ->where('leads_status_id', $leadStatusId)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get();
                return response()->json(['status' => true, 'success' => true, 'data' => $data]);
            }

            if ($mode === 'options') {
                $settings = Call::where('box_id', $boxId)
                    ->where('status', 1)
                    ->select('id', 'name as title', 'created_at', 'status')
                    ->orderBy('created_at', 'desc')->get()
                    ->map(fn($i) => ['id' => $i->id, 'title' => $i->title, 'status' => $i->status, 'created_date' => $i->created_at?->format('d M Y, h:i a')]);

                $settings->prepend(['id' => 'all', 'title' => 'All', 'created_date' => '', 'status' => 1]);
                $data['activity_settings'] = $settings;

                $data['activity_types'] = ActivityType::where('box_id', $boxId)
                    ->where('status', 1)
                    ->select('id', 'activity_setting_id', 'activity_type as title')
                    ->get()
                    ->prepend(['id' => 'all', 'activity_setting_id' => 'all', 'title' => 'All']);

                $data['activity_statuses'] = ActivityStatus::where('box_id', $boxId)
                    ->where('status', 1)
                    ->select('id', 'activity_type_id', 'activity_status as title')
                    ->get()
                    ->prepend(['id' => 'all', 'activity_type_id' => 'all', 'title' => 'All']);

                $data['connected_emails'] = \App\Models\GmailInbox::where('box_id', $boxId)
                    ->where('user_id', Auth::id())
                    ->where('status', 'Enabled')
                    ->select('id', 'email')
                    ->get();
            }

            if ($leadId) {
                $data['current_lead'] = Lead::where('id', $leadId)->where('box_id', $boxId)
                    ->select('id', 'lead_status_id', 'lead_sub_status_id', 'assigned_to')->first();
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch options.', 500, $th->getMessage());
        }
    }

    public function getActivityTypes(Request $request): JsonResponse
    {
        return $this->getSimpleList($request, ActivityType::class, 'activity_setting_id', 'activity_setting_id', 'activity_type');
    }

    public function getActivityStatuses(Request $request): JsonResponse
    {
        return $this->getSimpleList($request, ActivityStatus::class, 'activity_type_id', 'activity_type_id', 'activity_status');
    }

    private function getSimpleList(Request $request, string $modelClass, string $paramName, string $dbColumn, string $titleColumn): JsonResponse
    {
        try {
            $id = $request->input($paramName);
            if (!$id) return $this->errorResponse(ucfirst(str_replace('_', ' ', $paramName)) . ' is required.', 400);

            $boxId = $this->resolveBoxId();
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $items = $modelClass::where('box_id', $boxId)
                ->where($dbColumn, $id)
                ->where('status', 1)
                ->select('id', "$titleColumn as title", 'created_at')
                ->orderBy($titleColumn)
                ->get()
                ->map(fn($item) => $item->setAttribute('created_date', $item->created_at?->format('d M Y, h:i a')));

            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch data.', 500, $th->getMessage());
        }
    }

    public function downloadFile(\Illuminate\Http\Request $request, $box_slug, $path)
    {
        try {
            if (!$this->resolveBoxId($box_slug)) abort(404, 'Box not found.');
            $path = urldecode($path);
            if (!Storage::disk('public')->exists($path)) abort(404);

            if ($request->has('download')) {
                return Storage::disk('public')->download($path);
            }
            
            return Storage::disk('public')->response($path);
        } catch (\Throwable $th) {
            abort(404);
        }
    }

    public function getStates(Request $request)
    {
        try {
            // Default to India (101) if not specified, matching user requirement or generic usage
            $countryId = $request->input('country_id', 101);
            $states = State::where('country_id', $countryId)->where('status', 'Enable')->select('id', 'name')->orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $states]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch states.', 500, $th->getMessage());
        }
    }

    public function getCities(Request $request)
    {
        try {
            $stateId = $request->input('state_id');
            if (!$stateId) return $this->errorResponse('State ID is required.', 400);

            $cities = City::where('state_id', $stateId)->where('status', 'Enable')->select('id', 'name')->orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $cities]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch cities.', 500, $th->getMessage());
        }
    }

    public function getDivisions(Request $request)
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $divisions = Division::where('box_id', $boxId)
                ->where('status', 'Enable')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            return response()->json(['success' => true, 'data' => $divisions]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch divisions.', 500, $th->getMessage());
        }
    }
    public function addWorkingLocation(Request $request)
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $id = $request->input('lead_id');
            $stateId = $request->input('state_id');
            $cityId = $request->input('city_id');
            $divisionId = $request->input('division_id');
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $radius = $request->input('radius');
            $type = $request->input('type', 'radius');
            $polygonPaths = $request->input('polygon_paths');

            if (!$stateId || !$cityId || !$divisionId) {
                 return $this->errorResponse('State, City, and Division are required.', 400);
            }

            $user = Auth::user();

            // Always create new DivisionArea
            $divisionArea = DivisionArea::create([
                'box_id'      => $boxId,
                'division_id' => $divisionId,
                'state_id'    => $stateId,
                'city_id'     => $cityId,
                'lead_id'     => $id, // Added lead_id
                'lat'         => $lat,
                'lng'         => $lng,
                'radius'      => $radius,
                'polygon_paths' => $polygonPaths,
                'location_type' => $type, // Save location type
                'status'      => 'Active',
                'created_by'  => $user->id,
                'limit_member'=> 0,
                'max_members' => 0
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Division Area prepared successfully.',
                'data' => $divisionArea
            ]);

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('AddLocation Error: ' . $th->getMessage());
            return $this->errorResponse('Failed to add location.', 500, $th->getMessage());
        }
    }

    public function saveWorkingLocation(Request $request)
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $id = $request->input('lead_id');  
            $data = $request->input('location_data');
            
            if (!$id || empty($data)) {
                 return $this->errorResponse('Invalid data provided.', 400);
            }

            $user = Auth::user();
            $lead = Lead::where('box_id', $boxId)->where('id', $id)->first();

            if (!$lead) {
                return $this->errorResponse('Invalid Lead or Access Denied.', 404);
            }
            
            // Check usage limit before adding? (Optional/Future)

            foreach ($data as $row) {
                // Point data
                $pointData = $row['point'] ?? [];
                $divisionId = $pointData['division']['id'] ?? ($pointData['division'] ?? null);
                
                // Extract State/City from row data (frontend must send these)
                $rowStateId = $row['state_id'] ?? null;
                $rowCityId = $row['city_id'] ?? null;
                
                if (empty($divisionId)) {
                    continue; // Skip invalid entries
                }
                
                // 1. Manage DivisionArea (Link Division to State/City)
                $divisionAreaId = $pointData['division_area_id'] ?? null;
                $divisionArea = null;

                if ($divisionAreaId) {
                     $divisionArea = DivisionArea::where('box_id', $boxId)->find($divisionAreaId);
                }

                if (!$divisionArea && $rowStateId && $rowCityId) {
                    $divisionArea = DivisionArea::firstOrCreate(
                        [
                            'box_id'      => $boxId,
                            'division_id' => $divisionId,
                            'state_id'    => $rowStateId,
                            'city_id'     => $rowCityId,
                            'lead_id'     => $lead->id
                        ],
                        [
                            'status'      => 'Active',
                            'created_by'  => $user->id,
                            'lat'         => $row['lat'] ?? null,
                            'lng'         => $row['lng'] ?? null,
                            'radius'      => $row['radius'] ?? null,
                            'polygon_paths' => $row['polygon_paths'] ?? ($row['point']['polygon_paths'] ?? null),
                            'location_type' => $row['type'] ?? 'radius', // Save location type
                            'limit_member'=> 0, 
                            'max_members' => 0
                        ]
                    );
                    $divisionAreaId = $divisionArea->id;
                }

                // Sync Location Data to DivisionArea as well
                if ($divisionArea) {
                     $divisionArea->update([
                        'lat' => $row['lat'] ?? $divisionArea->lat,
                        'lng' => $row['lng'] ?? $divisionArea->lng,
                        'radius' => $row['radius'] ?? $divisionArea->radius,
                        'polygon_paths' => $row['polygon_paths'] ?? ($row['point']['polygon_paths'] ?? $divisionArea->polygon_paths),
                        'location_type' => $row['type'] ?? $divisionArea->location_type // Update type
                     ]);
                }

                $pointId = $pointData['id'] ?? null;
                $lead_area = null;

                if (empty($pointId)) {
                    // Create new AreaLead (Link Lead to DivisionArea/Location)
                    // Check for existing linkage to avoid duplicates for same Division+Area
                    $query = AreaLead::where('box_id', $boxId)
                        ->where('area_lead_id', $lead->id)
                        ->where('division_id', $divisionId);
                        
                    if ($divisionAreaId) {
                        $query->where('division_area_id', $divisionAreaId);
                    }
                    
                    $existing = $query->first();

                    if ($existing) {
                        $lead_area = $existing;
                    } else {
                        $lead_area = new AreaLead;
                        $lead_area->box_id = $boxId;
                        $lead_area->division_id = $divisionId;
                        $lead_area->division_area_id = $divisionAreaId;
                        $lead_area->area_lead_id = $lead->id;
                        $lead_area->created_by = $user->id;
                        $lead_area->status = 'Active';
                    }
                } else {
                    $lead_area = AreaLead::where('box_id', $boxId)->find($pointId);
                }

                if ($lead_area) {
                    $lead_area->lat = $row['lat'];
                    $lead_area->lng = $row['lng'];
                    $lead_area->radius = $row['radius'];
                    $lead_area->updated_by = $user->id;
                    $lead_area->save();
                }
            }

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('SaveLocation Error: ' . $th->getMessage());
            return $this->errorResponse('Failed to save location.', 500, $th->getMessage());
        }
    }
    public function getWorkingLocations(Request $request)
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $id = $request->input('lead_id');  
            
            if (!$id) {
                 return $this->errorResponse('Lead ID is required.', 400);
            }

            // Verify access
            $lead = Lead::where('box_id', $boxId)->where('id', $id)->first();
            if (!$lead) {
                return $this->errorResponse('Invalid Lead or Access Denied.', 404);
            }

            $locations = DivisionArea::where('box_id', $boxId)
                ->where('lead_id', $id) // specific to this lead
                ->where('status', 'Active') 
                ->with(['division:id,name,company_id', 'state:id,name', 'city:id,name', 'division.company:id,bill_name']) 
                ->get();

            return response()->json(['success' => true, 'data' => $locations]);

        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch locations.', 500, $th->getMessage());
        }
    }

    public function getWorkingLocationData(Request $request)
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $data = [];

            // 1. Fetch States (Default India: 101)
            $countryId = $request->input('country_id', 101);
            $data['states'] = State::where('country_id', $countryId)
                ->where('status', 'Enable')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            // 2. Fetch Divisions
            $data['divisions'] = Division::where('box_id', $boxId)
                ->where('status', 'Enable')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            // 3. Fetch Saved Locations (if lead_id provided)
            // 3. Fetch Locations (Box Basis + Lead Basis)
            $leadId = $request->input('lead_id');
            
            $locationsQuery = DivisionArea::where('box_id', $boxId)
                ->where('status', 'Active')
                ->with(['division:id,name,company_id', 'state:id,name', 'city:id,name', 'division.company:id,bill_name']);

            $allLocations = $locationsQuery->get();

            // Transform to add 'is_current_lead' flag safely for JSON
            $allLocations->each(function($loc) use ($leadId) {
                $loc->setAttribute('is_current_lead', ($loc->lead_id == $leadId));
            });

            // Sort so current lead location is last (renders on top in map)
            $allLocations = $allLocations->sortBy(fn($loc) => $loc->is_current_lead ? 1 : 0)->values();

            $data['locations'] = $allLocations;

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to fetch working location data.', 500, $th->getMessage());
        }
    }
    public function deleteWorkingLocation(Request $request)
    {
        try {
            $boxId = $this->resolveBoxId($request->route('box_slug'));
            if (!$boxId) return $this->errorResponse('Box not found.', 404);

            $id = $request->input('id'); 
            if (!$id) return $this->errorResponse('ID is required.', 400);

            $divisionArea = DivisionArea::where('box_id', $boxId)->find($id);

            if (!$divisionArea) {
                return $this->errorResponse('Location not found.', 404);
            }

            $divisionArea->delete();

            return response()->json(['success' => true, 'message' => 'Location deleted successfully.']);

        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('DeleteLocation Error: ' . $th->getMessage());
            return $this->errorResponse('Failed to delete location.', 500, $th->getMessage());
        }
    }
}
