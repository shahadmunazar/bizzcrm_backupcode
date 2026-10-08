<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LeadStatusApprovalRequest;
use App\Models\BoxUser;

class LeadApprovalController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $boxUser = BoxUser::where('user_id', $user_id)->first();
        $box_id = $boxUser ? $boxUser->box_id : null;
        
        $users = \App\Models\User::join('box_users', 'users.id', '=', 'box_users.user_id')
            ->where('box_users.box_id', $box_id)
            ->select('users.id', 'users.name')
            ->get();
        
        return view('frontend.leads.lead_approvals', compact('box_id', 'users'));
    }

    public function approve(Request $request)
    {
        try {
            $approval = LeadStatusApprovalRequest::findOrFail($request->id);
            $approval->status = 'approved';
            $approval->reviewed_by = Auth::id();
            $approval->reviewed_at = now();
            $approval->save();
            
            $lead = \App\Models\Lead::findOrFail($approval->lead_id);
            $lead->bypass_approval = true;
            if ($approval->new_status_id) {
                $lead->lead_status_id = $approval->new_status_id;
            }
            if ($approval->new_sub_status_id) {
                $lead->lead_sub_status_id = $approval->new_sub_status_id;
            }
            $lead->save();
            \App\Models\Activity::create([
                'box_id' => $approval->box_id,
                'activity_id' => $approval->lead_id,
                'type' => 'history',
                'activity_type' => 'leads',
                'subject' => 'Status Change Approved',
                'description' => "The pending status change request was approved by " . Auth::user()->name . ".",
                'activity_date' => now(),
                'added_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
                'status' => 'Close',
                'completed_date' => now(),
            ]);
            
            // Note: Add Notification logic here
            
            return response()->json(['success' => true, 'message' => 'Approval request approved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reject(Request $request)
    {
        try {
            $approval = LeadStatusApprovalRequest::findOrFail($request->id);
            $approval->status = 'disapproved';
            $approval->reviewed_by = Auth::id();
            $approval->reviewed_at = now();
            $approval->save();
            
            \App\Models\Activity::create([
                'box_id' => $approval->box_id,
                'activity_id' => $approval->lead_id,
                'type' => 'history',
                'activity_type' => 'leads',
                'subject' => 'Status Change Rejected',
                'description' => "The pending status change request was rejected by " . Auth::user()->name . ".",
                'activity_date' => now(),
                'added_by' => Auth::id(),
                'last_updated_by' => Auth::id(),
                'status' => 'Close',
                'completed_date' => now(),
            ]);
            
            // Note: Add Notification logic here
            
            return response()->json(['success' => true, 'message' => 'Approval request rejected successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getApprovals(Request $request)
    {
        try {
            $user = Auth::user();

            $boxUser = BoxUser::where('user_id', $user->id)->first();
            $box_id = $boxUser ? $boxUser->box_id : null;
            $designationId = $boxUser ? $boxUser->designation : null;

            $query = LeadStatusApprovalRequest::with(['lead', 'requester', 'oldStatus', 'newStatus', 'oldSubStatus', 'newSubStatus'])
                ->where('lead_status_approval_requests.box_id', $box_id);

            $requestedByFilter = $request->input('requested_by_filter');
            if (!empty($requestedByFilter)) {
                $query->where('lead_status_approval_requests.requested_by', $requestedByFilter);
            }
            $statusFilter = $request->input('status_filter');
            if (!empty($statusFilter)) {
                $query->where('lead_status_approval_requests.status', $statusFilter);
            }

            $isGlobalAdmin = $user->hasRole(['superadmin', 'admin']);
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            // Filter by requests where the required approval matches the user's designation.
            if (!$isGlobalAdmin && !$isBoxAdmin) {
                $query->where('lead_status_approval_requests.approval_designation_id', $designationId);
            }

            return DataTables::eloquent($query)
                ->addColumn('lead_name', function ($item) {
                    return $item->lead ? $item->lead->name : 'Unknown';
                })
                ->addColumn('requested_by_name', function ($item) {
                    return $item->requester ? $item->requester->name : 'Unknown';
                })
                ->addColumn('status_change', function ($item) {
                    $old = $item->oldStatus ? $item->oldStatus->name : '-';
                    $new = $item->newStatus ? $item->newStatus->name : '-';
                    return $old . ' <i class="fas fa-arrow-right mx-1"></i> ' . $new;
                })
                ->addColumn('sub_status_change', function ($item) {
                    $old = $item->oldSubStatus ? $item->oldSubStatus->name : '-';
                    $new = $item->newSubStatus ? $item->newSubStatus->name : '-';
                    return $old . ' <i class="fas fa-arrow-right mx-1"></i> ' . $new;
                })
                ->addColumn('action', function ($item) {
                    return '<button class="btn btn-sm btn-success me-1 approve-btn" data-id="' . $item->id . '">Approve</button>
                            <button class="btn btn-sm btn-danger reject-btn" data-id="' . $item->id . '">Reject</button>';
                })
                ->rawColumns(['status_change', 'sub_status_change', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
