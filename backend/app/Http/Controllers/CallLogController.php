<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Box;
use App\Models\CallLog;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

class CallLogController extends Controller
{
    public function index(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            abort(404);
        }

        $user = auth()->user();
        if ($user && $user->roles->pluck('name')->first() === 'superadmin') {
            $boxUsers = \App\Models\User::all();
        } else {
            $boxUsers = \App\Models\User::whereHas('boxes', function($q) use ($box) {
                $q->where('boxes.id', $box->id);
            })->get();
        }

        return view('call_logs.index', compact('box', 'boxUsers'));
    }

    public function getLogsDataTable(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        if ($request->ajax()) {
            $user = auth()->user();
            if ($user && $user->hasRole('superadmin')) {
                $query = CallLog::with('creator');
            } else {
                $query = CallLog::with('creator')->where('box_id', $box->id);
            }

            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $role = $boxUser ? $boxUser->staff_role : null;
            
            // Filter according to login user unless admin
            if ($role !== 'admin' && $role !== 'superadmin' && !$user->hasRole(['admin', 'superadmin'])) {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            }

            if ($request->has('user_ids') && is_array($request->user_ids)) {
                $query->whereIn('user_id', $request->user_ids);
            }

            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->whereDate('start_time', '>=', $request->from_date);
            }

            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->whereDate('start_time', '<=', $request->to_date);
            }

            $sortOrder = $request->has('sort_order') && in_array(strtolower($request->sort_order), ['asc', 'desc']) ? strtolower($request->sort_order) : 'desc';
            $query->orderBy('start_time', $sortOrder);

            \Illuminate\Support\Facades\Log::info('CallLogs query: ' . $query->toSql(), $query->getBindings());

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('creator_name', function($row) {
                    return $row->creator ? $row->creator->name . ' (' . $row->creator->email . ')' : '-';
                })
                ->editColumn('start_time', function($row) {
                    return $row->start_time ? \Carbon\Carbon::parse($row->start_time)->format('Y-m-d h:i:s A') : '-';
                })
                ->editColumn('end_time', function($row) {
                    return $row->end_time ? \Carbon\Carbon::parse($row->end_time)->format('Y-m-d h:i:s A') : '-';
                })
                ->editColumn('duration', function($row) {
                    $mins = floor($row->duration / 60);
                    $secs = $row->duration % 60;
                    return ($mins > 0 ? $mins . 'm ' : '') . $secs . 's';
                })
                ->editColumn('start_lat', function($row) {
                    return $row->start_lat ? '<a href="https://maps.google.com/?q=' . $row->start_lat . ',' . $row->start_long . '" target="_blank" class="badge bg-light text-primary border text-decoration-none"><i class="bx bx-map"></i> ' . $row->start_lat . '</a>' : '-';
                })
                ->editColumn('start_long', function($row) {
                    return $row->start_long ? '<a href="https://maps.google.com/?q=' . $row->start_lat . ',' . $row->start_long . '" target="_blank" class="badge bg-light text-primary border text-decoration-none"><i class="bx bx-map"></i> ' . $row->start_long . '</a>' : '-';
                })
                ->editColumn('end_lat', function($row) {
                    return $row->end_lat ? '<a href="https://maps.google.com/?q=' . $row->end_lat . ',' . $row->end_long . '" target="_blank" class="badge bg-light text-primary border text-decoration-none"><i class="bx bx-map"></i> ' . $row->end_lat . '</a>' : '-';
                })
                ->editColumn('end_long', function($row) {
                    return $row->end_long ? '<a href="https://maps.google.com/?q=' . $row->end_lat . ',' . $row->end_long . '" target="_blank" class="badge bg-light text-primary border text-decoration-none"><i class="bx bx-map"></i> ' . $row->end_long . '</a>' : '-';
                })
                ->editColumn('rawTimestamp', function($row) {
                    if ($row->rawTimestamp) {
                        $seconds = intval($row->rawTimestamp / 1000);
                        return \Carbon\Carbon::createFromTimestamp($seconds)->format('Y-m-d h:i:s A');
                    }
                    return '-';
                })
                ->editColumn('type', function($row) {
                    if($row->type == 'incoming') return '<span class="badge bg-success"><i class="bx bx-phone-incoming"></i> Incoming</span>';
                    if($row->type == 'outgoing') return '<span class="badge bg-info"><i class="bx bx-phone-outgoing"></i> Outgoing</span>';
                    return '<span class="badge bg-danger"><i class="bx bx-phone-off"></i> Missed</span>';
                })
                ->rawColumns(['type', 'start_lat', 'start_long', 'end_lat', 'end_long'])
                ->make(true);
        }

        return abort(403);
    }

    public function store(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'from' => 'required|string|max:50',
            'to' => 'required|string|max:50',
            'type' => 'required|in:incoming,outgoing,missed',
            'status' => 'required|string|max:50',
            'duration' => 'nullable|integer',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'start_lat' => 'nullable|numeric',
            'start_long' => 'nullable|numeric',
            'end_lat' => 'nullable|numeric',
            'end_long' => 'nullable|numeric',
            'rawTimestamp' => 'nullable|string',
        ]);

        $validated['box_id'] = $box->id;
        $validated['created_by'] = auth()->id(); // Assuming we track who created it
        $validated['user_id'] = auth()->id(); // Add user_id as requested

        if (empty($validated['rawTimestamp']) && !empty($validated['start_time'])) {
            $validated['rawTimestamp'] = (string) (\Carbon\Carbon::parse($validated['start_time'])->timestamp * 1000);
        }

        $callLog = CallLog::create($validated);

        // Find user by 'from' number
        $matchedUser = \App\Models\User::whereHas('boxes', function($q) use ($box) {
            $q->where('boxes.id', $box->id);
        })->where('mobile', $request->from)->first();

        // Find lead by robust matching (handles missing +91)
        $to10 = $request->to ? substr(preg_replace('/[^0-9]/', '', $request->to), -10) : null;
        $from10 = $request->from ? substr(preg_replace('/[^0-9]/', '', $request->from), -10) : null;
        $searchPhones = array_filter([
            $request->to, ltrim($request->to ?? '', '+'), '+' . ltrim($request->to ?? '', '+'), $to10,
            $request->from, ltrim($request->from ?? '', '+'), '+' . ltrim($request->from ?? '', '+'), $from10
        ]);

        $matchedLead = \App\Models\Lead::where('box_id', $box->id)
            ->where(function($q) use ($searchPhones) {
                $q->whereIn('phone', $searchPhones)
                  ->orWhereIn('sec_phone', $searchPhones);
            })->first();
        // Create Activity if lead is found
        if ($matchedLead) {
            \App\Models\Activity::create([
                'box_id' => $box->id,
                'activity_id' => $matchedLead->id, // Activity's activity_id maps to Lead ID
                'label' => 'Call',
                'subject' => ucfirst($request->type) . ' Call Log',
                'description' => "Status: {$request->status} | Duration: {$request->duration} sec | From: {$request->from} | To: {$request->to}",
                'type' => 'call',
                'task_type' => 'call',
                'activity_date' => $request->start_time ?? now(),
                'assigned_to' => $matchedUser ? $matchedUser->id : null,
                'added_by' => auth()->id(),
                'last_updated_by' => auth()->id(),
                'status' => 'Close',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Call log created successfully!']);
    }

    public function apiIndex(Request $request, $boxSlug)
    {
        try {
            $box = \App\Models\Box::where('slug', $boxSlug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $user = auth()->user();
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
            $role = $boxUser ? $boxUser->staff_role : null;

            $isSuperAdmin = $user->hasRole('superadmin');
            $isAdmin = $user->hasRole('admin');

            // ALL users (including superadmin) must only see logs for the requested box
            $query = CallLog::where('box_id', $box->id);

            // Regular users are further restricted to only their own logs
            if (!$isSuperAdmin && !$isAdmin) {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            } elseif ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by call type
            if ($request->has('type') && !empty($request->type)) {
                $query->where('type', $request->type);
            }

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('from', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('to', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                });
            }

        // Filter by predefined date ranges
        if ($request->has('date_filter') && !empty($request->date_filter)) {
            $dateFilter = $request->date_filter;
            $today = \Carbon\Carbon::today();

            if ($dateFilter === 'today') {
                $query->whereDate('start_time', $today);
            } elseif ($dateFilter === 'yesterday') {
                $query->whereDate('start_time', $today->copy()->subDay());
            } elseif ($dateFilter === 'last_7_days') {
                $query->whereDate('start_time', '>=', $today->copy()->subDays(7));
            } elseif ($dateFilter === 'last_30_days') {
                $query->whereDate('start_time', '>=', $today->copy()->subDays(30));
            }
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('start_time', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('start_time', '<=', $request->to_date);
        }

        // Get details (grouped by user) before applying order_by and pagination
        $groupQuery = clone $query;
        $details = $groupQuery->select('created_by', 'user_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_logs'))
            ->with('creator:id,name,email')
            ->groupBy('created_by', 'user_id')
            ->get();

        $sortOrder = $request->has('sort_order') && strtolower($request->sort_order) === 'asc' ? 'asc' : 'desc';
        $query->orderBy('start_time', $sortOrder);

        $perPage = $request->input('per_page', 50); // Default to 50 items per page
        $logs = $query->paginate($perPage);

        // Collect all unique phone numbers (with '+', without '+', and last 10 digits)
        $phoneNumbers = collect($logs->items())->flatMap(function ($log) {
            $from = $log->from;
            $to = $log->to;
            $from10 = $from ? substr(preg_replace('/[^0-9]/', '', $from), -10) : null;
            $to10 = $to ? substr(preg_replace('/[^0-9]/', '', $to), -10) : null;

            return [
                $from, 
                $to, 
                $from ? ltrim($from, '+') : null, 
                $to ? ltrim($to, '+') : null,
                $from ? '+' . ltrim($from, '+') : null,
                $to ? '+' . ltrim($to, '+') : null,
                $from10,
                $to10
            ];
        })->filter()->unique(null, true)->values()->toArray();

        // Fetch matching leads efficiently in a single query
        $leadPhoneSet = collect();
        if (!empty($phoneNumbers)) {
            $matchingLeads = \App\Models\Lead::where('box_id', $box->id)
                ->where(function ($q) use ($phoneNumbers) {
                    $q->whereIn('phone', $phoneNumbers)
                      ->orWhereIn('sec_phone', $phoneNumbers);
                })
                ->get(['phone', 'sec_phone']);

            foreach ($matchingLeads as $lead) {
                if ($lead->phone) $leadPhoneSet->push($lead->phone);
                if ($lead->sec_phone) $leadPhoneSet->push($lead->sec_phone);
            }
        }

        // Attach the flag to each log item
        $items = collect($logs->items())->map(function ($log) use ($leadPhoneSet) {
            $logArray = $log->toArray();
            
            $from10 = $log->from ? substr(preg_replace('/[^0-9]/', '', $log->from), -10) : null;
            $to10 = $log->to ? substr(preg_replace('/[^0-9]/', '', $log->to), -10) : null;

            $fromVariations = [
                $log->from, 
                $log->from ? ltrim($log->from, '+') : null, 
                $log->from ? '+' . ltrim($log->from, '+') : null,
                $from10
            ];
            $toVariations = [
                $log->to, 
                $log->to ? ltrim($log->to, '+') : null, 
                $log->to ? '+' . ltrim($log->to, '+') : null,
                $to10
            ];
            
            $isLead = false;
            foreach ($fromVariations as $v) {
                if ($v && $leadPhoneSet->contains($v)) { $isLead = true; break; }
            }
            if (!$isLead) {
                foreach ($toVariations as $v) {
                    if ($v && $leadPhoneSet->contains($v)) { $isLead = true; break; }
                }
            }
            
            $logArray['is_already_lead'] = $isLead;
            return $logArray;
        });

        return response()->json([
            'success' => true,
            'total_synced' => $logs->total(),
            'details' => $details,
            'data' => $items,
            'pagination' => [
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'next_page_url' => $logs->nextPageUrl(),
                'prev_page_url' => $logs->previousPageUrl(),
            ]
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiStore(Request $request, $boxSlug)
    {
        $box = \App\Models\Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $user = auth()->user();

        $inputData = $request->all();
        
        // Determine if input is a single object or an array of objects
        $isMultiple = isset($inputData[0]) && is_array($inputData[0]);
        if (!$isMultiple && isset($inputData['logs']) && is_array($inputData['logs'])) {
            $isMultiple = true;
            $logsData = $inputData['logs'];
        } elseif ($isMultiple) {
            $logsData = $inputData;
        } else {
            $logsData = [$inputData];
        }

        $validator = \Illuminate\Support\Facades\Validator::make(['logs' => $logsData], [
            'logs.*.box_id' => 'nullable',
            'logs.*.name' => 'nullable|string|max:255',
            'logs.*.from' => 'required|string|max:50',
            'logs.*.to' => 'required|string|max:50',
            'logs.*.type' => 'required|in:incoming,outgoing,missed',
            'logs.*.status' => 'required|string|max:50',
            'logs.*.duration' => 'nullable|integer',
            'logs.*.start_time' => 'nullable|date',
            'logs.*.end_time' => 'nullable|date',
            'logs.*.start_lat' => 'nullable|numeric',
            'logs.*.start_long' => 'nullable|numeric',
            'logs.*.end_lat' => 'nullable|numeric',
            'logs.*.end_long' => 'nullable|numeric',
            'logs.*.rawTimestamp' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedLogs = $validator->validated()['logs'];
        $createdLogs = [];

        // Process in chunks of 100 to optimize memory
        $chunks = array_chunk($validatedLogs, 100);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($chunks as $chunk) {
                $fromNumbers = array_column($chunk, 'from');
                $toNumbers = array_column($chunk, 'to');
                
                $allUserNumbers = array_unique(array_merge($fromNumbers, $toNumbers));
                $matchedUsers = \App\Models\User::whereHas('boxes', function($q) use ($box) {
                    $q->where('boxes.id', $box->id);
                })->whereIn('mobile', $allUserNumbers)->get()->keyBy('mobile');
                
                // Extract all variations to robustly match leads
                $allNumbers = collect(array_merge($fromNumbers, $toNumbers))->filter()->flatMap(function($num) {
                    $num10 = substr(preg_replace('/[^0-9]/', '', $num), -10);
                    return [$num, ltrim($num, '+'), '+' . ltrim($num, '+'), $num10];
                })->filter()->unique()->values()->toArray();

                $matchedLeads = \App\Models\Lead::where('box_id', $box->id)
                    ->where(function($q) use ($allNumbers) {
                        $q->whereIn('phone', $allNumbers)
                          ->orWhereIn('sec_phone', $allNumbers);
                    })->get();

                $callLogsToInsert = [];
                $activitiesToInsert = [];
                $now = now();

                foreach ($chunk as $logData) {
                    if (empty($logData['rawTimestamp']) && !empty($logData['start_time'])) {
                        $logData['rawTimestamp'] = (string) (\Carbon\Carbon::parse($logData['start_time'])->timestamp * 1000);
                    }
                    
                    $logEntry = [
                        'box_id' => $box->id,
                        'user_id' => $user->id,
                        'created_by' => $user->id,
                        'name' => $logData['name'] ?? null,
                        'from' => $logData['from'] ?? null,
                        'to' => $logData['to'] ?? null,
                        'type' => $logData['type'] ?? null,
                        'status' => $logData['status'] ?? null,
                        'duration' => $logData['duration'] ?? 0,
                        'start_time' => $logData['start_time'] ?? null,
                        'end_time' => $logData['end_time'] ?? null,
                        'start_lat' => $logData['start_lat'] ?? null,
                        'start_long' => $logData['start_long'] ?? null,
                        'end_lat' => $logData['end_lat'] ?? null,
                        'end_long' => $logData['end_long'] ?? null,
                        'rawTimestamp' => $logData['rawTimestamp'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    
                    $callLogsToInsert[] = $logEntry;
                    $createdLogs[] = $logEntry; // Add to response array

                    // Determine the user and lead numbers based on call type
                    $isIncoming = in_array($logData['type'], ['incoming', 'missed']);
                    $userNumberToMatch = $isIncoming ? ($logData['to'] ?? null) : ($logData['from'] ?? null);
                    $leadNumberToMatch = $isIncoming ? ($logData['from'] ?? null) : ($logData['to'] ?? null);
                    
                    // Match user robustly (exact match or 10-digit match)
                    $matchedUser = $matchedUsers->first(function($user) use ($userNumberToMatch) {
                        if (!$userNumberToMatch || !$user->mobile) return false;
                        if ($user->mobile === $userNumberToMatch) return true;
                        
                        $userMobile10 = substr(preg_replace('/[^0-9]/', '', $user->mobile), -10);
                        $searchNumber10 = substr(preg_replace('/[^0-9]/', '', $userNumberToMatch), -10);
                        
                        return $userMobile10 && ($userMobile10 === $searchNumber10);
                    });
                    
                    // Match the lead robustly (strict direction based on call type)
                    $matchedLead = $matchedLeads->first(function($lead) use ($leadNumberToMatch) {
                        if (!$leadNumberToMatch) return false;
                        
                        $leadNumber10 = substr(preg_replace('/[^0-9]/', '', $leadNumberToMatch), -10);
                        
                        $leadPhones = array_filter([
                            $lead->phone, 
                            $lead->phone ? substr(preg_replace('/[^0-9]/', '', $lead->phone), -10) : null,
                            $lead->sec_phone,
                            $lead->sec_phone ? substr(preg_replace('/[^0-9]/', '', $lead->sec_phone), -10) : null
                        ]);
                        
                        return (!empty($leadNumber10) && in_array($leadNumber10, $leadPhones)) || 
                               in_array($leadNumberToMatch, [$lead->phone, $lead->sec_phone]);
                    });
                    
                    // Prepare Activity data if lead is found
                    if ($matchedLead) {
                        $assignedTo = $matchedUser ? $matchedUser->id : $matchedLead->assigned_to;
                        
                        $activitiesToInsert[] = [
                            'box_id' => $box->id,
                            'activity_id' => $matchedLead->id,
                            'label' => 'Call',
                            'subject' => ucfirst($logData['type']) . ' Call Log',
                            'description' => "Status: {$logData['status']} | Duration: " . ($logData['duration'] ?? 0) . " sec | From: {$logData['from']} | To: {$logData['to']}",
                            'type' => 'call',
                            'task_type' => 'call',
                            'activity_date' => $logData['start_time'] ?? $now,
                            'assigned_to' => $assignedTo,
                            'added_by' => $user->id,
                            'last_updated_by' => $user->id,
                            'status' => 'Close',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                // Bulk Insert for immense speedup
                if (!empty($callLogsToInsert)) {
                    CallLog::insert($callLogsToInsert);
                }
                if (!empty($activitiesToInsert)) {
                    \App\Models\Activity::insert($activitiesToInsert);
                }
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Failed to sync call logs: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true, 
            'message' => count($validatedLogs) . ' call log(s) synced successfully!', 
            'data' => $isMultiple ? $createdLogs : ($createdLogs[0] ?? null)
        ], 201);
    }

    public function apiSyncCount(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $user = auth()->user();
        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        $role = $boxUser ? $boxUser->staff_role : null;

        $isSuperAdmin = $user->hasRole('superadmin');
        $isAdmin = $user->hasRole('admin');

        if ($isSuperAdmin) {
            // Superadmin gets all logs
            $query = CallLog::query();
        } else {
            // Everyone else is restricted to this box
            $query = CallLog::where('box_id', $box->id);
        }

        // Regular users are further restricted to only their own logs
        if (!$isSuperAdmin && !$isAdmin) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        } elseif ($request->has('user_id')) {
            // Admins & Superadmins can optionally filter by a specific user
            $query->where('user_id', $request->user_id);
        }

        $totalSynced = $query->count();

        $groupQuery = clone $query;
        $details = $groupQuery->select('created_by', 'user_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_logs'))
            ->with('creator:id,name,email')
            ->groupBy('created_by', 'user_id')
            ->get();

        return response()->json([
            'success' => true,
            'total_synced' => $totalSynced,
            'details' => $details,
        ]);
    }

    public function status(Request $request, $boxSlug)
    {
        $user = auth()->user();
        $box = Box::where('slug', $boxSlug)->first();
        
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)
            ->where('box_id', $box->id)
            ->first();

        if (!$boxUser) {
            return response()->json(['success' => false, 'message' => 'User does not belong to this box'], 403);
        }

        if ($boxUser->status !== 'Enabled') {
            return response()->json(['success' => false, 'message' => 'User account is disabled'], 403);
        }

        return response()->json([
            'success' => true,
            'call_logs_enabled' => (bool) $boxUser->call_logs,
            'employee' => $boxUser->status === 'Enabled',
            'whatsapp' => (bool) $box->whatsapp_enable
        ]);
    }

    public function export(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            abort(404);
        }

        $user = auth()->user();
        if ($user && $user->roles->pluck('name')->first() === 'superadmin') {
            $query = CallLog::with('creator');
        } else {
            $query = CallLog::with('creator')->where('box_id', $box->id);
        }

        $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->where('box_id', $box->id)->first();
        $role = $boxUser ? $boxUser->staff_role : null;
        
        // Filter according to login user unless admin
        if ($role !== 'admin' && $role !== 'superadmin' && $user->roles->pluck('name')->first() !== 'superadmin') {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }

        if ($request->has('user_ids') && is_array($request->user_ids)) {
            $query->whereIn('user_id', $request->user_ids);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('start_time', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('start_time', '<=', $request->to_date);
        }

        $sortOrder = $request->has('sort_order') && in_array(strtolower($request->sort_order), ['asc', 'desc']) ? strtolower($request->sort_order) : 'desc';
        $query->orderBy('start_time', $sortOrder);

        $exportType = $request->input('export_type', 'excel');
        $fileName = 'call_logs_' . date('Y-m-d_H_i_s');

        if ($exportType === 'pdf') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CallLogsExport($query), $fileName . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CallLogsExport($query), $fileName . '.xlsx');
    }
}
