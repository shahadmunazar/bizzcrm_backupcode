<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountDeletionRequest;
use App\Models\AccountDeletionAudit;
use App\Mail\AccountDeletionOtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccountDeletionController extends Controller
{
    public function getAccountDeleteLink(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // Generate an auto-login token link if required by your auth system
        $token = $user->createToken('webview-delete')->plainTextToken;
        $boxSlug = $request->route('box_slug') ?? 'default';

        $link = route('account.delete.index', ['box_slug' => $boxSlug]) . '?token=' . $token;

        return response()->json([
            'status' => 'success',
            'data' => [
                'link' => $link
            ]
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending_verification', 'pending_review', 'approved', 'processing', 'completed'])
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $deletionRequest
        ]);
    }

    public function submit(Request $request)
    {
        try {
            $request->validate([
                'reason' => 'required|string',
                'experience' => 'nullable|string',
                'problems' => 'nullable|array',
                'feedback' => 'nullable|string',
                'confirmations' => 'required|array',
            ]);

            $user = Auth::user();
            
            // Check if already requested
            $existing = AccountDeletionRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending_verification', 'pending_review', 'approved', 'processing'])
                ->first();
                
            if ($existing) {
                return response()->json(['status' => 'error', 'message' => 'You already have an active deletion request.'], 400);
            }

            $otp = rand(100000, 999999);
            
            $deletionRequest = AccountDeletionRequest::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'pending_verification'],
                [
                    'email' => $user->email,
                    'reason' => $request->reason,
                    'experience' => $request->experience,
                    'problems' => $request->problems,
                    'feedback' => $request->feedback,
                    'confirmations' => $request->confirmations,
                    'otp_hash' => Hash::make($otp),
                    'otp_expires_at' => now()->addMinutes(15),
                    'status' => 'pending_verification'
                ]
            );

            Mail::to($user->email)->send(new AccountDeletionOtpMail($otp));

            AccountDeletionAudit::create([
                'account_deletion_request_id' => $deletionRequest->id,
                'event' => 'Request Created',
                'description' => 'User submitted account deletion request.',
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'Verification code sent to your email.',
                'expiry' => $deletionRequest->otp_expires_at->toIso8601String(),
                'email' => substr($user->email, 0, 3) . '***@' . explode('@', $user->email)[1]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|string|size:6',
            ]);

            $user = Auth::user();

            $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
                ->where('status', 'pending_verification')
                ->first();

            if (!$deletionRequest) {
                return response()->json(['status' => 'error', 'message' => 'No pending request found.'], 404);
            }

            if (now()->gt($deletionRequest->otp_expires_at)) {
                return response()->json(['status' => 'error', 'message' => 'OTP has expired. Please request a new one.'], 400);
            }

            if (!Hash::check($request->otp, $deletionRequest->otp_hash)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid verification code.'], 400);
            }

            $deletionRequest->update([
                'status' => 'pending_review',
                'otp_verified_at' => now(),
                'otp_hash' => null,
            ]);

            AccountDeletionAudit::create([
                'account_deletion_request_id' => $deletionRequest->id,
                'event' => 'Email Verified',
                'description' => 'OTP verification completed successfully.',
                'user_id' => $user->id,
            ]);
            
            AccountDeletionAudit::create([
                'account_deletion_request_id' => $deletionRequest->id,
                'event' => 'Submitted for Review',
                'description' => 'Request is awaiting Super Admin approval.',
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'Email verified successfully.',
                'request_id' => str_pad($deletionRequest->id, 6, '0', STR_PAD_LEFT)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred.'], 500);
        }
    }
    
    public function resendOtp()
    {
        try {
            $user = Auth::user();
            $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
                ->where('status', 'pending_verification')
                ->first();

            if (!$deletionRequest) {
                return response()->json(['status' => 'error', 'message' => 'No pending request found.'], 404);
            }

            $otp = rand(100000, 999999);
            $deletionRequest->update([
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(15),
            ]);

            Mail::to($user->email)->send(new AccountDeletionOtpMail($otp));

            return response()->json([
                'status' => 'success', 
                'message' => 'A new verification code has been sent to your email.',
                'expiry' => $deletionRequest->otp_expires_at->toIso8601String()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to resend OTP.'], 500);
        }
    }
    
    public function cancel()
    {
        try {
            $user = Auth::user();
            $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending_verification', 'pending_review'])
                ->first();

            if ($deletionRequest) {
                $deletionRequest->update([
                    'status' => 'cancelled'
                ]);
                
                AccountDeletionAudit::create([
                    'account_deletion_request_id' => $deletionRequest->id,
                    'event' => 'Cancelled',
                    'description' => 'User cancelled the account deletion request.',
                    'user_id' => $user->id,
                ]);
                
                return response()->json(['status' => 'success', 'message' => 'Account deletion request cancelled successfully.']);
            }

            return response()->json(['status' => 'error', 'message' => 'Cannot cancel this request.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to cancel request.'], 500);
        }
    }
}
