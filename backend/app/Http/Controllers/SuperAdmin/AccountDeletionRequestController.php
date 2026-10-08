<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AccountDeletionRequest;
use App\Models\AccountDeletionAudit;
use Illuminate\Support\Facades\Auth;

class AccountDeletionRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountDeletionRequest::with('user');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'pending_review' => AccountDeletionRequest::where('status', 'pending_review')->count(),
            'verified' => AccountDeletionRequest::where('status', 'pending_verification')->count(),
            'approved' => AccountDeletionRequest::where('status', 'approved')->count(),
            'rejected' => AccountDeletionRequest::where('status', 'rejected')->count(),
            'processing' => AccountDeletionRequest::where('status', 'processing')->count(),
            'completed' => AccountDeletionRequest::where('status', 'completed')->count(),
        ];

        return view('frontend.superadmin.account-deletion-requests.index', compact('requests', 'stats'));
    }

    public function show($box_slug, $id)
    {
        $deletionRequest = AccountDeletionRequest::with(['user', 'audits' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id);
        return view('frontend.superadmin.account-deletion-requests.show', compact('deletionRequest'));
    }

    public function approve($box_slug, $id)
    {
        try {
            $deletionRequest = AccountDeletionRequest::findOrFail($id);
            
            if ($deletionRequest->status !== 'pending_review') {
                return response()->json(['status' => 'error', 'message' => 'Only pending review requests can be approved.'], 400);
            }

            $deletionRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'reviewed_by' => Auth::id(),
                'scheduled_deletion_at' => now()->addDays(7),
            ]);

            AccountDeletionAudit::create([
                'account_deletion_request_id' => $deletionRequest->id,
                'event' => 'Approved',
                'description' => 'Approved by Super Admin. Deletion scheduled for ' . now()->addDays(7)->format('d M Y'),
                'user_id' => Auth::id(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Account deletion request approved and scheduled.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to approve request.'], 500);
        }
    }

    public function reject(Request $request, $box_slug, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string',
            ]);

            $deletionRequest = AccountDeletionRequest::findOrFail($id);
            
            if ($deletionRequest->status !== 'pending_review') {
                return response()->json(['status' => 'error', 'message' => 'Only pending review requests can be rejected.'], 400);
            }

            $deletionRequest->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'reviewed_by' => Auth::id(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            AccountDeletionAudit::create([
                'account_deletion_request_id' => $deletionRequest->id,
                'event' => 'Rejected',
                'description' => 'Rejected by Super Admin. Reason: ' . $request->rejection_reason,
                'user_id' => Auth::id(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Account deletion request rejected.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to reject request.'], 500);
        }
    }
}
