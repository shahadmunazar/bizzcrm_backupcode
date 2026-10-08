<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountDeletionRequest;
use App\Models\AccountDeletionAudit;
use App\Mail\AccountDeletionOtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccountDeletionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending_verification', 'pending_review', 'approved', 'processing', 'completed'])
            ->orderBy('id', 'desc')
            ->first();

        return view('frontend.account-delete.index', compact('deletionRequest'));
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
                return back()->with('error', 'You already have an active deletion request.');
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

            return back()->with('success', 'Verification code sent to your email.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred. Please try again.');
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
                return back()->with('error', 'No pending request found.');
            }

            if (now()->gt($deletionRequest->otp_expires_at)) {
                return back()->with('error', 'OTP has expired. Please request a new one.');
            }

            if (!Hash::check($request->otp, $deletionRequest->otp_hash)) {
                return back()->with('error', 'Invalid verification code.');
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

            return back()->with('success', 'Email verified successfully. Request submitted for review.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred.');
        }
    }
    
    public function resendOtp(Request $request)
    {
        try {
            $user = Auth::user();
            $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
                ->where('status', 'pending_verification')
                ->first();

            if (!$deletionRequest) {
                return back()->with('error', 'No pending request found.');
            }

            $otp = rand(100000, 999999);
            $deletionRequest->update([
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(15),
            ]);

            Mail::to($user->email)->send(new AccountDeletionOtpMail($otp));

            return back()->with('success', 'A new verification code has been sent to your email.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resend OTP.');
        }
    }
    
    public function cancel(Request $request)
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
                
                return back()->with('success', 'Account deletion request cancelled successfully.');
            }

            return back()->with('error', 'Cannot cancel this request.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel request.');
        }
    }
}
