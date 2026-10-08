<?php

namespace App\Http\Controllers\Call;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class CallDashboardController extends Controller
{
    public function call_dashboard()
    {
        $boxUser = Auth::user()->boxUser ?? \App\Models\BoxUser::where('user_id', Auth::id())->first();
        $boxId = $boxUser->box_id ?? null;
        $isAdmin = $boxUser && in_array(strtolower($boxUser->staff_role), ['admin', 'superadmin']);
        $canViewAll = $isAdmin || Auth::user()->can('call_dashboard.view_all');

        $users = collect();
        if ($boxId) {
            if ($canViewAll) {
                $users = User::whereHas('box_user_new', function($q) use ($boxId) {
                    $q->where('box_id', $boxId);
                })->get();
            } else {
                $users = collect([Auth::user()]);
            }
        }
        return view('frontend.admin.call_dashboard.calldashboard', compact('users', 'canViewAll'));
    }

    private function getCallStats($query)
    {
        $calls = $query->get();

        $stats = [
            'total_calls' => 0,
            'calls_duration' => 0,
            'incoming' => 0,
            'incoming_duration' => 0,
            'outgoing' => 0,
            'outgoing_duration' => 0,
            'missed' => 0,
            'rejected' => 0,
            'never_attended' => 0,
            'not_pickup' => 0,
            'unique_clients' => [],
            'working_hours' => 0,
            'connected_calls' => 0,
        ];

        $firstCall = null;
        $lastCall = null;

        foreach ($calls as $call) {
            $stats['total_calls']++;
            
            $duration = (int) $call->duration;
            $type = strtolower($call->type);
            $status = strtolower($call->status);

            $stats['calls_duration'] += $duration;

            if ($type === 'incoming') {
                $stats['incoming']++;
                $stats['incoming_duration'] += $duration;
                if ($status !== 'connected') {
                    $stats['missed']++;
                }
            } elseif ($type === 'outgoing') {
                $stats['outgoing']++;
                $stats['outgoing_duration'] += $duration;
                if ($status !== 'connected') {
                    $stats['not_pickup']++;
                }
            } elseif ($type === 'missed') {
                $stats['missed']++;
            }

            if ($status === 'connected') {
                $stats['connected_calls']++;
            }

            // For unique clients, use the 'to' number if outgoing, or 'from' if incoming
            $clientNumber = ($type === 'outgoing') ? $call->to : $call->from;
            if ($clientNumber && !in_array($clientNumber, $stats['unique_clients'])) {
                $stats['unique_clients'][] = $clientNumber;
            }

            $callDate = Carbon::parse($call->start_time);
            if (!$firstCall || $callDate < $firstCall) {
                $firstCall = $callDate;
            }
            if (!$lastCall || $callDate > $lastCall) {
                $lastCall = $callDate;
            }
        }

        $stats['unique_clients'] = count($stats['unique_clients']);
        
        if ($firstCall && $lastCall) {
            $stats['working_hours'] = $lastCall->diffInSeconds($firstCall);
        }

        // Format durations
        $formatDuration = function ($seconds) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $secs = $seconds % 60;
            return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
        };

        $stats['calls_duration'] = $formatDuration($stats['calls_duration']);
        $stats['incoming_duration'] = $formatDuration($stats['incoming_duration']);
        $stats['outgoing_duration'] = $formatDuration($stats['outgoing_duration']);
        $stats['working_hours'] = $formatDuration($stats['working_hours']);

        return $stats;
    }

    public function dashboardData(Request $request)
    {
        $boxUser = Auth::user()->boxUser ?? \App\Models\BoxUser::where('user_id', Auth::id())->first();
        $boxId = $boxUser->box_id ?? null;
        if (!$boxId) {
            return response()->json(['error' => 'No Box ID found']);
        }

        $isAdmin = $boxUser && in_array(strtolower($boxUser->staff_role), ['admin', 'superadmin']);
        $canViewAll = $isAdmin || Auth::user()->can('call_dashboard.view_all');

        if (!$canViewAll) {
            $userId = Auth::id();
        } else {
            $userId = $request->get('user_id');
        }
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $baseQuery = \App\Models\CallLog::where('box_id', $boxId);

        if ($userId) {
            $selectedUser = User::find($userId);
            if ($selectedUser && $selectedUser->mobile) {
                // Remove non-numeric characters for broader matching (e.g., getting the last 10 digits)
                $mobileDigits = substr(preg_replace("/[^0-9]+/", "", $selectedUser->mobile), -10);
                if (!empty($mobileDigits)) {
                    $baseQuery->where(function ($q) use ($mobileDigits) {
                        $q->where('from', 'like', '%' . $mobileDigits . '%')
                          ->orWhere('to', 'like', '%' . $mobileDigits . '%');
                    });
                } else {
                    $baseQuery->where('created_by', $userId);
                }
            } else {
                $baseQuery->where('created_by', $userId);
            }
        }

        $response = [];

        if ($dateFrom && $dateTo) {
            // Custom Range Mode
            $query = clone $baseQuery;
            $query->whereBetween('start_time', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
            
            $response['custom'] = $this->getCallStats($query);
        } else {
            // Default 3 periods Mode
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();
            
            $yesterdayStart = Carbon::yesterday()->startOfDay();
            $yesterdayEnd = Carbon::yesterday()->endOfDay();
            
            $lastWeekStart = Carbon::today()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::today()->subWeek()->endOfWeek();

            $todayQuery = clone $baseQuery;
            $todayQuery->whereBetween('start_time', [$todayStart, $todayEnd]);
            $response['today'] = $this->getCallStats($todayQuery);
            $response['today_label'] = Carbon::today()->format('d-M-Y');

            $yesterdayQuery = clone $baseQuery;
            $yesterdayQuery->whereBetween('start_time', [$yesterdayStart, $yesterdayEnd]);
            $response['yesterday'] = $this->getCallStats($yesterdayQuery);
            $response['yesterday_label'] = Carbon::yesterday()->format('d-M-Y');

            $lastWeekQuery = clone $baseQuery;
            $lastWeekQuery->whereBetween('start_time', [$lastWeekStart, $lastWeekEnd]);
            $response['last_week'] = $this->getCallStats($lastWeekQuery);
            $response['last_week_label'] = $lastWeekStart->format('d-M-Y') . ' to ' . $lastWeekEnd->format('d-M-Y');
        }

        return response()->json($response);
    }

    public function apiDashboardData(Request $request)
    {
        $boxUser = Auth::user()->boxUser ?? \App\Models\BoxUser::where('user_id', Auth::id())->first();
        $boxId = $boxUser->box_id ?? null;
        if (!$boxId) {
            return response()->json(['error' => 'No Box ID found']);
        }

        $isAdmin = $boxUser && in_array(strtolower($boxUser->staff_role), ['admin', 'superadmin']);
        $canViewAll = $isAdmin || Auth::user()->can('call_dashboard.view_all');

        if (!$canViewAll) {
            $userId = Auth::id();
        } else {
            $userId = $request->get('user_id');
        }
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $baseQuery = \App\Models\CallLog::where('box_id', $boxId);

        if ($userId) {
            $selectedUser = User::find($userId);
            if ($selectedUser && $selectedUser->mobile) {
                // Remove non-numeric characters for broader matching (e.g., getting the last 10 digits)
                $mobileDigits = substr(preg_replace("/[^0-9]+/", "", $selectedUser->mobile), -10);
                if (!empty($mobileDigits)) {
                    $baseQuery->where(function ($q) use ($mobileDigits) {
                        $q->where('from', 'like', '%' . $mobileDigits . '%')
                          ->orWhere('to', 'like', '%' . $mobileDigits . '%');
                    });
                } else {
                    $baseQuery->where('created_by', $userId);
                }
            } else {
                $baseQuery->where('created_by', $userId);
            }
        }

        $columns = [];

        if ($dateFrom && $dateTo) {
            // Custom Range Mode
            $query = clone $baseQuery;
            $query->whereBetween('start_time', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
            
            $stats = $this->getCallStats($query);
            $columns[] = $this->formatForApp($stats, 'Custom Range', Carbon::parse($dateFrom)->format('d-M-Y') . ' to ' . Carbon::parse($dateTo)->format('d-M-Y'));
        } else {
            // Default 3 periods Mode
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();
            
            $yesterdayStart = Carbon::yesterday()->startOfDay();
            $yesterdayEnd = Carbon::yesterday()->endOfDay();
            
            $lastWeekStart = Carbon::today()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::today()->subWeek()->endOfWeek();

            $todayQuery = clone $baseQuery;
            $todayQuery->whereBetween('start_time', [$todayStart, $todayEnd]);
            $columns[] = $this->formatForApp($this->getCallStats($todayQuery), 'Today', Carbon::today()->format('d-M-Y'));

            $yesterdayQuery = clone $baseQuery;
            $yesterdayQuery->whereBetween('start_time', [$yesterdayStart, $yesterdayEnd]);
            $columns[] = $this->formatForApp($this->getCallStats($yesterdayQuery), 'Yesterday', Carbon::yesterday()->format('d-M-Y'));

            $lastWeekQuery = clone $baseQuery;
            $lastWeekQuery->whereBetween('start_time', [$lastWeekStart, $lastWeekEnd]);
            $columns[] = $this->formatForApp($this->getCallStats($lastWeekQuery), 'Last Week', $lastWeekStart->format('d-M-Y') . ' to ' . $lastWeekEnd->format('d-M-Y'));
        }

        return response()->json([
            'status' => true,
            'message' => 'Call dashboard data fetched successfully.',
            'data' => [
                'columns' => $columns
            ]
        ]);
    }

    private function formatForApp($stats, $title, $dateLabel)
    {
        return [
            'title' => $title,
            'date_label' => $dateLabel,
            'metrics' => [
                ['label' => 'Total Calls', 'value' => (string)($stats['total_calls'] ?? 0), 'icon_class' => 'ti ti-phone', 'color_class' => 'text-muted'],
                ['label' => 'Calls Duration', 'value' => (string)($stats['calls_duration'] ?? '00:00:00'), 'icon_class' => 'ti ti-phone', 'color_class' => 'text-muted'],
                ['label' => 'Incomming', 'value' => (string)($stats['incoming'] ?? 0), 'icon_class' => 'fe fe-phone-incoming', 'color_class' => 'text-success'],
                ['label' => 'Incomming Mins.', 'value' => (string)($stats['incoming_duration'] ?? '00:00:00'), 'icon_class' => 'fe fe-phone-incoming', 'color_class' => 'text-muted'],
                ['label' => 'Outgoing', 'value' => (string)($stats['outgoing'] ?? 0), 'icon_class' => 'fe fe-phone-outgoing', 'color_class' => 'text-warning'],
                ['label' => 'Outgoing Mins.', 'value' => (string)($stats['outgoing_duration'] ?? '00:00:00'), 'icon_class' => 'fe fe-phone-outgoing', 'color_class' => 'text-warning'],
                ['label' => 'Missed', 'value' => (string)($stats['missed'] ?? 0), 'icon_class' => 'fe fe-phone-missed', 'color_class' => 'text-danger'],
                ['label' => 'Rejected', 'value' => (string)($stats['rejected'] ?? 0), 'icon_class' => 'fe fe-slash', 'color_class' => 'text-danger'],
                ['label' => 'Never Attended', 'value' => (string)($stats['never_attended'] ?? 0), 'icon_class' => 'fe fe-phone-off', 'color_class' => 'text-danger'],
                ['label' => 'Not Pickup', 'value' => (string)($stats['not_pickup'] ?? 0), 'icon_class' => 'fe fe-phone-off', 'color_class' => 'text-danger'],
                ['label' => 'Unique Clients', 'value' => (string)($stats['unique_clients'] ?? 0), 'icon_class' => 'fe fe-user', 'color_class' => 'text-muted'],
                ['label' => 'Working Hours', 'value' => (string)($stats['working_hours'] ?? '00:00:00'), 'icon_class' => 'fa-regular fa-clock', 'color_class' => 'text-muted'],
                ['label' => 'Connected Calls', 'value' => (string)($stats['connected_calls'] ?? 0), 'icon_class' => 'fe fe-phone', 'color_class' => 'text-muted'],
            ]
        ];
    }

    public function calllogs()
    {
        $user = Auth::user();
        $requestData = request()->all();
        $file_path = storage_path() . "/logs/calllogs_" . date('Y_m_d') . ".log";
        error_log(date('Y-m-d H:i:s') . " " . json_encode($requestData) . "\n", 3, $file_path);

        // Assuming the request body is an array of call objects
        if (is_array($requestData)) {
            foreach ($requestData as $call) {
                // Skip if not a valid call array
                if (!is_array($call) || !isset($call['from']) || !isset($call['to'])) {
                    continue;
                }

                $from = substr(preg_replace("/[^0-9]+/", "", $call['from']), -10);
                // $to = substr(preg_replace("/[^0-9]+/", "", $call['to']), -10); // Unused in query?

                // Get current box ID. Assuming user is logged in.
                $boxId = $user->box_user_new->box_id ?? null;
                
                if (!$boxId) {
                     continue; 
                }

                $lead = Lead::where('box_id', $boxId)->where('phone', $from)->first();

                if ($lead) {
                    $activity_date = Carbon::parse($call['start_time']);
                    
                    // Check for existing activity
                    $activityCount = Activity::where('box_id', $boxId)
                        ->where('activity_type', 'App\Models\Lead') // Adjusted namespace if needed, user said App\Lead but usually App\Models\Lead
                        ->where('type', 'task')
                        ->where('task_type', 'call')
                        ->where('created_from', 'app')
                        ->where('activity_date', $activity_date)
                        ->where('activity_id', $lead->id)
                        ->count();

                    if ($activityCount == 0) {
                        $activity = new Activity;
                        $activity->box_id = $boxId;
                        $activity->activity_id = $lead->id;
                        $activity->activity_type = 'App\Models\Lead'; // Adjusted namespace
                        $activity->added_by = $user->id;
                        $activity->created_from = "app";
                        $activity->last_updated_by = $user->id;
                        $activity->activity_date = $activity_date;
                        $activity->assigned_to = $user->id;
                        $activity->type = 'task';
                        $activity->status = 'Close';
                        $activity->completed_date = $activity_date;
                        $activity->task_type = 'call';

                        // Fix: 'duration' key was used twice in user snippet. 
                        // one was $call['to'] (maybe destination?) and other $call['duration']
                        // I will assign 'to' -> to, and 'duration' -> duration
                        $activity->params = [
                            'from' => $call['from'],
                            'to' => $call['to'],
                            'type' => $call['type'],
                            'duration' => $call['duration'] ?? 0,
                            'lat' => $call['start_lat'] ?? null,
                            'lng' => $call['start_long'] ?? null
                        ];

                        if (in_array($call['type'], ["outgoing", "incoming"])) {
                             // Assuming $call['status'] exists
                            $status = $call['status'] ?? '';
                            $activity->activity_type_status = ($status == "connected" ? "Picked" : "Not Picked");
                        } else {
                            $activity->activity_type_status = "Not Picked";
                        }
                        $activity->save();
                    }
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'saved']);
    }
}   
