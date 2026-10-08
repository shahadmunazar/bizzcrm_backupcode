<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return redirect()->route('login')->with('error', 'No account found.');
        }

        return view('frontend.tasks.index', compact('status'));
    }

    public function getTasks(Request $request)
    {
        try {
            $userId = Auth::id();
            $boxId = BoxUser::where('user_id', $userId)->value('box_id');

            if (!$boxId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Box not found for the user.'
                ], 404);
            }

            $filter = $request->input('filter', 'all');

            // Query Activities directly, filtering for non-history records.
            // BizzCRM treats history records with type="history".
            $query = Activity::where('box_id', $boxId)
                ->where('type', '!=', 'history')
                ->with(['lead:id,name,last_name,email,phone', 'assignedUser:id,name']);

            $todayDate = Carbon::today()->toDateString();

            if ($filter === 'completed') {
                $query->where('status', 'Completed');
            } else {
                // For all other tabs, we typically only want to see actionable/open tasks
                $query->where(function($q) {
                    $q->where('status', '!=', 'Completed')
                      ->orWhereNull('status');
                });

                if ($filter === 'today') {
                    $query->whereDate('activity_date', $todayDate);
                } elseif ($filter === 'tomorrow') {
                    $query->whereDate('activity_date', Carbon::tomorrow()->toDateString());
                } elseif ($filter === 'this_week') {
                    $query->whereBetween('activity_date', [
                        Carbon::now()->startOfWeek()->toDateString(), 
                        Carbon::now()->endOfWeek()->toDateString()
                    ]);
                } elseif ($filter === 'this_month') {
                    $query->whereMonth('activity_date', Carbon::now()->month)
                          ->whereYear('activity_date', Carbon::now()->year);
                } elseif ($filter === 'upcoming') {
                    $query->whereDate('activity_date', '>', Carbon::today()->toDateString());
                } elseif ($filter === 'pending') {
                    // Pending usually means overdue
                    $query->whereDate('activity_date', '<', $todayDate);
                }
            }

            return DataTables::of($query)
                ->addColumn('lead_name', function ($activity) {
                    return $activity->lead ? trim($activity->lead->name . ' ' . $activity->lead->last_name) : 'No Lead';
                })
                ->addColumn('assigned_to_name', function ($activity) {
                    return $activity->assignedUser ? $activity->assignedUser->name : 'Unassigned';
                })
                ->addColumn('activity_date_formatted', function ($activity) {
                    return $activity->activity_date ? Carbon::parse($activity->activity_date)->format('d-M-Y H:i') : '--';
                })
                ->addColumn('action', function ($activity) {
                    return '<a href="'.box_route('LeadsDetailsPage', ['id' => $activity->activity_id]).'" class="btn btn-sm btn-primary">View Lead</a>';
                })
                ->rawColumns(['action'])
                ->make(true);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching tasks.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
