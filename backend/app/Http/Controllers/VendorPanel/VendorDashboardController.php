<?php

namespace App\Http\Controllers\VendorPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Vendor;
use App\Models\Box;
use App\Models\VendorCommission;
use App\Models\Plan;
use App\Helpers\SubscriptionHelper;
use App\Models\VendorReferralInvitation;
use App\Jobs\SendVendorReferralInvitationJob;

class VendorDashboardController extends Controller
{
    /**
     * Retrieve authenticated vendor profile securely.
     */
    protected function getVendor()
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Please log in to continue.');
        }

        if (!$user->hasRole('vendor') && !$user->vendor()->exists()) {
            abort(403, 'Unauthorized access. Only vendor partner accounts can access this panel.');
        }

        $vendor = $user->vendor;
        if (!$vendor) {
            abort(403, 'Vendor profile not found for this account.');
        }

        return $vendor;
    }

    public function index()
    {
        $vendor = $this->getVendor();

        $currentMonthCommission = VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');

        $lastMonthCommission = VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('commission_amount');

        $referralUrl = $vendor->referral_code ? route('register', ['ref' => $vendor->referral_code]) : '';
        $referredVendorsCount = Vendor::where('referred_by_vendor_id', $vendor->id)->count();
        $invitationsCount = VendorReferralInvitation::where('vendor_id', $vendor->id)->count();
        $joinedInvitationsCount = VendorReferralInvitation::where('vendor_id', $vendor->id)->where('status', 'joined')->count();
        $pendingInvitationsCount = VendorReferralInvitation::where('vendor_id', $vendor->id)->where('status', 'pending')->count();

        $latestReferredVendors = Vendor::with(['user', 'industry'])
            ->where('referred_by_vendor_id', $vendor->id)
            ->latest()
            ->take(6)
            ->get();

        $latestInvitations = VendorReferralInvitation::with(['registeredVendor.user', 'registeredBox.superadmin'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.vendor_panel.dashboard.index', compact(
            'vendor',
            'currentMonthCommission',
            'lastMonthCommission',
            'referralUrl',
            'referredVendorsCount',
            'invitationsCount',
            'joinedInvitationsCount',
            'pendingInvitationsCount',
            'latestReferredVendors',
            'latestInvitations'
        ));
    }

    public function transaction()
    {
        $vendor = $this->getVendor();
        $grossSales = VendorCommission::where('vendor_id', $vendor->id)->sum('transaction_amount');
        $netCleared = VendorCommission::where('vendor_id', $vendor->id)->where('status', 'paid')->sum('commission_amount');
        $pendingBalance = VendorCommission::where('vendor_id', $vendor->id)->whereIn('status', ['unpaid', 'pending', 'verified'])->sum('commission_amount');
        
        $transactions = VendorCommission::with('box')->where('vendor_id', $vendor->id)->latest()->get();

        return view('frontend.vendor_panel.dashboard.transaction', compact('vendor', 'grossSales', 'netCleared', 'pendingBalance', 'transactions'));
    }

    public function commission()
    {
        $vendor = $this->getVendor();
        $commissions = VendorCommission::with('box')->where('vendor_id', $vendor->id)->latest()->get();
        return view('frontend.vendor_panel.dashboard.commission', compact('vendor', 'commissions'));
    }

    public function referrals()
    {
        $vendor = $this->getVendor();
        $referralUrl = $vendor->referral_code ? route('register', ['ref' => $vendor->referral_code]) : '';
        
        $referredVendors = Vendor::with(['user', 'industry'])
            ->where('referred_by_vendor_id', $vendor->id)
            ->latest()
            ->get();
        $referredVendorsCount = $referredVendors->count();

        // Invitations sent by this vendor
        $invitations = VendorReferralInvitation::with('registeredVendor.user')
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->get();

        // Synchronize pending invitations if matching vendor already registered
        foreach ($invitations as $inv) {
            if ($inv->status !== 'joined') {
                $matchedVendor = $referredVendors->first(function ($v) use ($inv) {
                    return strtolower(trim($v->user->email ?? '')) === strtolower(trim($inv->recipient_email));
                });
                if ($matchedVendor) {
                    $inv->update([
                        'status' => 'joined',
                        'registered_vendor_id' => $matchedVendor->id,
                        'registered_at' => $matchedVendor->created_at ?? now(),
                    ]);
                    $inv->load('registeredVendor.user');
                }
            }
        }

        $invitationsCount = $invitations->count();
        $joinedInvitationsCount = $invitations->where('status', 'joined')->count();
        $pendingInvitationsCount = $invitations->where('status', 'pending')->count();

        $referredBoxes = $vendor->referral_code ? Box::where('referral_code', $vendor->referral_code)->latest()->get() : collect();

        return view('frontend.vendor_panel.dashboard.referrals', compact(
            'vendor',
            'referralUrl',
            'referredVendorsCount',
            'referredVendors',
            'invitations',
            'invitationsCount',
            'joinedInvitationsCount',
            'pendingInvitationsCount',
            'referredBoxes'
        ));
    }

    public function referral_details($id)
    {
        $vendor = $this->getVendor();
        $box = Box::where('referral_code', $vendor->referral_code)->findOrFail($id);
        
        $transactions = VendorCommission::where('vendor_id', $vendor->id)
            ->where('box_id', $box->id)
            ->latest()
            ->get();
            
        return view('frontend.vendor_panel.dashboard.vendor_detail', compact('vendor', 'box', 'transactions'));
    }

    public function shareReferralEmail(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email',
            'subject' => 'nullable|string|max:150',
            'message' => 'nullable|string|max:1500',
        ]);

        $vendor = $this->getVendor();
        $user = Auth::user();

        if (empty($vendor->referral_code) || $vendor->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your vendor account is pending approval. Your referral code will be generated once your account is approved by the Super Admin.',
            ], 422);
        }

        $recipientEmail = strtolower(trim($request->recipient_email));

        if ($recipientEmail === strtolower(trim($user->email))) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot send a referral invitation to your own email address.',
            ], 422);
        }

        $baseReferralUrl = route('register', ['ref' => $vendor->referral_code]);
        $referralUrl = route('register', [
            'ref' => $vendor->referral_code,
            'email' => $recipientEmail,
        ]);
        $appName = config('app.name', 'ZipZapGo');
        $subject = $request->input('subject') ?: "Invitation from {$vendor->company_name} to join {$appName}";
        $bodyMessage = $request->input('message') ?: "Hello,\n\nYou have been invited by {$vendor->company_name} ({$user->name}) to join {$appName}.\n\nUse Referral Code: {$vendor->referral_code}\nOr register directly using the official link:\n{$referralUrl}\n\nJoin today to get started with powerful CRM and business management tools.";

        if (strpos($bodyMessage, $baseReferralUrl) !== false && strpos($bodyMessage, 'email=') === false) {
            $bodyMessage = str_replace($baseReferralUrl, $referralUrl, $bodyMessage);
        }

        // Check if recipient is already a registered box or vendor under this referral code
        $existingBox = Box::where('referral_code', $vendor->referral_code)
            ->whereHas('superadmin', function ($q) use ($recipientEmail) {
                $q->where('email', $recipientEmail);
            })
            ->first();

        $existingVendor = Vendor::where('referred_by_vendor_id', $vendor->id)
            ->whereHas('user', function ($q) use ($recipientEmail) {
                $q->where('email', $recipientEmail);
            })
            ->first();

        $isJoined = $existingBox || $existingVendor;
        $status = $isJoined ? 'joined' : 'pending';
        $registeredBoxId = $existingBox ? $existingBox->id : null;
        $registeredVendorId = $existingVendor ? $existingVendor->id : null;
        $registeredAt = $existingBox ? ($existingBox->created_at ?? now()) : ($existingVendor ? ($existingVendor->created_at ?? now()) : null);

        try {
            $invitation = VendorReferralInvitation::updateOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'recipient_email' => $recipientEmail,
                ],
                [
                    'referral_code' => $vendor->referral_code,
                    'referral_url' => $referralUrl,
                    'message' => $bodyMessage,
                    'status' => $status,
                    'registered_vendor_id' => $registeredVendorId,
                    'registered_box_id' => $registeredBoxId,
                    'registered_at' => $registeredAt,
                    'last_sent_at' => now(),
                ]
            );

            // Dispatch background queue job for sending invitation email
            SendVendorReferralInvitationJob::dispatch(
                $recipientEmail,
                $user->name,
                $vendor->company_name ?: $user->name,
                $vendor->referral_code,
                $referralUrl,
                $bodyMessage,
                $subject,
                $invitation->id
            );

            return response()->json([
                'success' => true,
                'message' => "Referral invitation has been queued and is being sent to {$recipientEmail}!",
                'invitation' => [
                    'id' => $invitation->id,
                    'recipient_email' => $invitation->recipient_email,
                    'referral_code' => $invitation->referral_code,
                    'referral_url' => $invitation->referral_url,
                    'status' => $invitation->status,
                    'registered_vendor' => $existingVendor ? [
                        'company_name' => $existingVendor->company_name ?: 'N/A',
                        'name' => $existingVendor->user->name ?? 'N/A',
                        'status' => ucfirst($existingVendor->status),
                    ] : null,
                    'registered_at' => $invitation->registered_at ? $invitation->registered_at->format('M d, Y h:i A') : null,
                    'last_sent_at' => now()->format('M d, Y h:i A'),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process invitation email: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function plans()
    {
        return redirect()->route('vendor.dashboard')->with('info', 'The Vendor Panel is completely free. No subscription plan is required.');
    }

    public function api_dashboard()
    {
        $vendor = $this->getVendor();
        
        $currentQuery = VendorCommission::where('vendor_id', $vendor->id);
        
        if ($vendor->target_type === 'custom') {
            if ($vendor->target_start_date) {
                $currentQuery->whereDate('created_at', '>=', $vendor->target_start_date);
            }
            if ($vendor->target_end_date) {
                $currentQuery->whereDate('created_at', '<=', $vendor->target_end_date);
            }
        } else {
            $currentQuery->whereMonth('created_at', now()->month)
                         ->whereYear('created_at', now()->year);
        }
        
        $currentCommission = $currentQuery->sum('commission_amount');

        $lastMonthCommission = VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('commission_amount');

        $latestReferredVendors = Vendor::with(['user'])
            ->where('referred_by_vendor_id', $vendor->id)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($ref) {
                return [
                    'id' => $ref->id,
                    'company_name' => $ref->company_name ?: 'N/A',
                    'contact_name' => $ref->user->name ?? 'N/A',
                    'email' => $ref->user->email ?? 'N/A',
                    'phone' => $ref->user->mobile ?? ($ref->pincode ?? 'N/A'),
                    'status' => ucfirst($ref->status),
                    'is_verified' => !is_null($ref->user?->email_verified_at),
                    'created_at' => $ref->created_at->format('M d, Y'),
                ];
            });

        $latestInvitations = VendorReferralInvitation::with('registeredVendor.user')
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'recipient_email' => $inv->recipient_email,
                    'referral_code' => $inv->referral_code,
                    'status' => $inv->status, // 'joined' or 'pending'
                    'registered_vendor' => $inv->registeredVendor ? [
                        'company_name' => $inv->registeredVendor->company_name ?: 'N/A',
                        'name' => $inv->registeredVendor->user->name ?? 'N/A',
                        'status' => ucfirst($inv->registeredVendor->status),
                    ] : null,
                    'last_sent_at' => $inv->last_sent_at ? $inv->last_sent_at->format('M d, Y h:i A') : $inv->created_at->format('M d, Y h:i A'),
                    'created_at' => $inv->created_at->format('M d, Y'),
                ];
            });

        $effectiveMonthlyTarget = \App\Models\VendorTarget::getTargetForMonth($vendor->id, now()->year, now()->month);
        $monthlyTarget = $effectiveMonthlyTarget > 0 ? $effectiveMonthlyTarget : (float) ($vendor->monthly_target ?? 0);

        $currentMonthPerf = \App\Models\VendorTarget::calculatePeriodPerformance($vendor->id, 'monthly', now()->year, now()->month);
        $currentQuarterPerf = \App\Models\VendorTarget::calculatePeriodPerformance($vendor->id, 'quarterly', now()->year, (int) ceil(now()->month / 3));
        $currentHalfPerf = \App\Models\VendorTarget::calculatePeriodPerformance($vendor->id, 'half_yearly', now()->year, now()->month <= 6 ? 1 : 2);
        $currentYearPerf = \App\Models\VendorTarget::calculatePeriodPerformance($vendor->id, 'yearly', now()->year, 1);

        return response()->json([
            'currentCommission' => $currentCommission,
            'lastMonthCommission' => $lastMonthCommission,
            'monthlyTarget' => $monthlyTarget,
            'effectiveMonthlyTarget' => $monthlyTarget,
            'targetType' => $vendor->target_type,
            'targetStartDate' => $vendor->target_start_date,
            'targetEndDate' => $vendor->target_end_date,
            'current_month_perf' => $currentMonthPerf,
            'current_quarter_perf' => $currentQuarterPerf,
            'current_half_perf' => $currentHalfPerf,
            'current_year_perf' => $currentYearPerf,
            'referral_code' => $vendor->referral_code,
            'referral_url' => $vendor->referral_code ? route('register', ['ref' => $vendor->referral_code]) : '',
            'referred_vendors_count' => Vendor::where('referred_by_vendor_id', $vendor->id)->count(),
            'invitations_count' => VendorReferralInvitation::where('vendor_id', $vendor->id)->count(),
            'joined_invitations_count' => VendorReferralInvitation::where('vendor_id', $vendor->id)->where('status', 'joined')->count(),
            'pending_invitations_count' => VendorReferralInvitation::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'latest_referred_vendors' => $latestReferredVendors,
            'latest_invitations' => $latestInvitations,
        ]);
    }

    public function api_transaction()
    {
        $vendor = $this->getVendor();
        $grossSales = VendorCommission::where('vendor_id', $vendor->id)->sum('transaction_amount');
        $netCleared = VendorCommission::where('vendor_id', $vendor->id)->where('status', 'paid')->sum('commission_amount');
        $pendingBalance = VendorCommission::where('vendor_id', $vendor->id)->whereIn('status', ['unpaid', 'pending', 'verified'])->sum('commission_amount');
        
        $transactions = VendorCommission::with('box')->where('vendor_id', $vendor->id)->latest()->get();

        return response()->json([
            'grossSales' => $grossSales,
            'netCleared' => $netCleared,
            'pendingBalance' => $pendingBalance,
            'transactions' => $transactions
        ]);
    }

    public function api_referrals()
    {
        $vendor = $this->getVendor();
        $referralUrl = $vendor->referral_code ? route('register', ['ref' => $vendor->referral_code]) : '';

        $referredVendors = Vendor::with(['user', 'industry'])
            ->where('referred_by_vendor_id', $vendor->id)
            ->latest()
            ->get();

        $referredVendorsData = $referredVendors->map(function ($ref) {
            return [
                'id' => $ref->id,
                'company_name' => $ref->company_name ?: 'N/A',
                'contact_name' => $ref->user->name ?? 'N/A',
                'email' => $ref->user->email ?? 'N/A',
                'phone' => $ref->user->mobile ?? ($ref->pincode ?? 'N/A'),
                'status' => ucfirst($ref->status),
                'is_verified' => !is_null($ref->user?->email_verified_at),
                'created_at' => $ref->created_at->format('M d, Y'),
            ];
        });

        $invitations = VendorReferralInvitation::with(['registeredVendor.user', 'registeredBox.superadmin'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->get()
            ->map(function ($inv) use ($vendor, $referredVendors) {
                // If status is still pending, check if the recipient registered as a Box or Vendor
                if ($inv->status !== 'joined') {
                    $matchedBox = Box::where('referral_code', $vendor->referral_code)
                        ->whereHas('superadmin', function ($q) use ($inv) {
                            $q->where('email', $inv->recipient_email);
                        })
                        ->first();

                    if ($matchedBox) {
                        $inv->update([
                            'status' => 'joined',
                            'registered_box_id' => $matchedBox->id,
                            'registered_at' => $matchedBox->created_at ?? now(),
                        ]);
                        $inv->load('registeredBox.superadmin');
                    } else {
                        $matched = $referredVendors->first(function ($v) use ($inv) {
                            return strtolower(trim($v->user->email ?? '')) === strtolower(trim($inv->recipient_email));
                        });
                        if ($matched) {
                            $inv->update([
                                'status' => 'joined',
                                'registered_vendor_id' => $matched->id,
                                'registered_at' => $matched->created_at ?? now(),
                            ]);
                            $inv->load('registeredVendor.user');
                        }
                    }
                }

                $regInfo = null;
                if ($inv->registeredBox) {
                    $regInfo = [
                        'company_name' => $inv->registeredBox->name ?: 'N/A',
                        'name' => $inv->registeredBox->superadmin->name ?? 'N/A',
                        'email' => $inv->registeredBox->superadmin->email ?? 'N/A',
                        'status' => 'Active',
                        'type' => 'CRM User',
                    ];
                } elseif ($inv->registeredVendor) {
                    $regInfo = [
                        'company_name' => $inv->registeredVendor->company_name ?: 'N/A',
                        'name' => $inv->registeredVendor->user->name ?? 'N/A',
                        'email' => $inv->registeredVendor->user->email ?? 'N/A',
                        'status' => ucfirst($inv->registeredVendor->status),
                        'type' => 'Vendor',
                    ];
                }

                return [
                    'id' => $inv->id,
                    'recipient_email' => $inv->recipient_email,
                    'referral_code' => $inv->referral_code,
                    'referral_url' => $inv->referral_url,
                    'status' => $inv->status, // 'joined' or 'pending'
                    'registered_vendor' => $regInfo,
                    'registered_at' => $inv->registered_at ? $inv->registered_at->format('M d, Y h:i A') : null,
                    'last_sent_at' => $inv->last_sent_at ? $inv->last_sent_at->format('M d, Y h:i A') : $inv->created_at->format('M d, Y h:i A'),
                    'created_at' => $inv->created_at->format('M d, Y'),
                ];
            });

        $referredBoxes = $vendor->referral_code ? Box::where('referral_code', $vendor->referral_code)->latest()->get() : collect();

        return response()->json([
            'success' => true,
            'referral_code' => $vendor->referral_code,
            'referral_url' => $referralUrl,
            'total_referred_vendors' => $referredVendorsData->count(),
            'referred_vendors' => $referredVendorsData,
            'total_invitations' => $invitations->count(),
            'joined_invitations_count' => $invitations->where('status', 'joined')->count(),
            'pending_invitations_count' => $invitations->where('status', 'pending')->count(),
            'invitations' => $invitations,
            'referredBoxes' => $referredBoxes,
        ]);
    }

    public function api_referral_details($id)
    {
        $vendor = $this->getVendor();
        $box = Box::where('referral_code', $vendor->referral_code)->findOrFail($id);
        $transactions = VendorCommission::where('vendor_id', $vendor->id)
            ->where('box_id', $box->id)
            ->latest()
            ->get();
            
        return response()->json([
            'box' => $box,
            'transactions' => $transactions
        ]);
    }
}
