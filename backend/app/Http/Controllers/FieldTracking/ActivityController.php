<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BoxUser;
use App\Models\Box;
use App\Models\UserLocationHistory;
use App\Models\Attendance;
use App\Models\CustomerCheckIn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ActivityController extends Controller
{
    public function teamActivityDetail() { return view('fieldstracking.team-activity-detail'); }
    public function teamActivity() { return view('fieldstracking.team-activity'); }
    public function trackingHistory(Request $request) {
        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        
        $boxUsers = BoxUser::with('user')->where('box_id', $box->id)->where('status', 'Enabled')->get();
        return view('fieldstracking.tracking-history', compact('boxUsers'));
    }

    public function trackingHistoryData(Request $request)
    {
        $boxuserId = $request->input('boxuser_id');
        $date = $request->input('date');

        $query = UserLocationHistory::with('user');

        if ($boxuserId) {
            $query->where('boxuser_id', $boxuserId);
        }

        if ($date) {
            // Assuming date is in 'Y-m-d' format
            $query->whereDate('recorded_at', $date);
        }

        $history = $query->orderBy('recorded_at', 'asc')->get();

        return response()->json([
            'status' => true,
            'data' => $history
        ]);
    }
    public function teamActivityData(Request $request)
    {
        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['data' => []]);
        }

        $dateRange = $request->input('date_range');
        $startDate = Carbon::today();
        $endDate = Carbon::today();

        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) == 2) {
                try {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                } catch (\Exception $e) {
                    try {
                        $startDate = Carbon::createFromFormat('Y-m-d', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('Y-m-d', trim($dates[1]))->endOfDay();
                    } catch (\Exception $e) {}
                }
            } else {
                try {
                    $startDate = Carbon::parse($dateRange)->startOfDay();
                    $endDate = Carbon::parse($dateRange)->endOfDay();
                } catch (\Exception $e) {}
            }
        }

        $boxUsers = BoxUser::with('user')
            ->where('box_id', $box->id)
            ->where('status', 'Enabled')
            ->get();

        $data = [];

        foreach ($boxUsers as $boxUser) {
            $totalCheckins = CustomerCheckIn::where('boxuser_id', $boxUser->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            $productiveCheckins = $totalCheckins;

            $attendances = Attendance::where('box_user_id', $boxUser->id)
                ->whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('attendance_date', 'asc')
                ->get();
            
            $startDay = '---';
            $endDay = '---';
            $duration = '---';
            
            if ($attendances->count() > 0) {
                $firstAttendance = $attendances->first();
                $lastAttendance = $attendances->last();
                
                $startDay = $firstAttendance->check_in ? Carbon::parse($firstAttendance->check_in)->format('h:i A') : '---';
                $endDay = $lastAttendance->check_out ? Carbon::parse($lastAttendance->check_out)->format('h:i A') : '---';
                
                $totalMinutes = 0;
                foreach($attendances as $att) {
                    if ($att->production_hours) {
                        $parts = explode(':', $att->production_hours);
                        if (count($parts) >= 2) {
                            $totalMinutes += ($parts[0] * 60) + $parts[1];
                        }
                    }
                }
                if ($totalMinutes > 0) {
                    $hours = floor($totalMinutes / 60);
                    $mins = $totalMinutes % 60;
                    $duration = sprintf("%02dh %02dm", $hours, $mins);
                }
            }

            $lastCheckin = CustomerCheckIn::where('boxuser_id', $boxUser->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->first();
            
            $lastActivity = '---';
            if ($lastCheckin) {
                $lastActivity = $lastCheckin->created_at->format('h:i A');
            } elseif ($attendances->count() > 0 && $attendances->last()->check_out) {
                 $lastActivity = Carbon::parse($attendances->last()->check_out)->format('h:i A');
            } elseif ($attendances->count() > 0 && $attendances->last()->check_in) {
                 $lastActivity = Carbon::parse($attendances->last()->check_in)->format('h:i A');
            }

            $avatarUrl = $boxUser->user && $boxUser->user->avatar ? asset($boxUser->user->avatar) : asset('assets/img/profiles/avatar-14.jpg');
            $userName = $boxUser->user ? $boxUser->user->name : 'Unknown';

            $data[] = [
                'user_info' => '<div class="d-flex align-items-center"><div class="avatar avatar-sm me-2 avatar-rounded"><img src="'.$avatarUrl.'" alt="img"></div><div><div class="lh-1"><span>'.$userName.'</span></div></div></div>',
                'tc' => $totalCheckins,
                'pc' => $productiveCheckins,
                'order_value' => '0',
                'distance' => '0 Km',
                'start_day' => $startDay,
                'last_activity' => $lastActivity,
                'end_day' => $endDay,
                'duration' => $duration
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
