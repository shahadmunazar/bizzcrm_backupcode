<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\MasterIndustry;
use App\Models\Box;
use App\Models\VendorReferralInvitation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendVendorStatusEmailJob;
use App\Models\VendorTarget;

class VendorManagementController extends Controller
{
    public function vendor_login()
    {
        return view('auth.vendor_login');
    }

    public function vendor_signup(\Illuminate\Http\Request $request)
    {
        $industries = \App\Models\MasterIndustry::all();
        $refCode = $request->query('ref');
        $email = $request->query('email');
        $referringVendor = null;
        if ($refCode) {
            $referringVendor = \App\Models\Vendor::with('user')->where('referral_code', trim($refCode))->first();
            if (!$email && $referringVendor) {
                $invitation = \App\Models\VendorReferralInvitation::where('vendor_id', $referringVendor->id)
                    ->where('status', 'pending')
                    ->latest('id')
                    ->first();
                if ($invitation) {
                    $email = $invitation->recipient_email;
                }
            }
        }
        return view('auth.vendor_signup', compact('industries', 'referringVendor', 'refCode', 'email'));
    }

    // Vendors Pages
    public function vendor_dashboard($boxSlug)
    {
        return view('frontend.superadmin.vendors.dashboard');
    }

    public function vendors()
    {
        return view('frontend.superadmin.vendors.index');
    }

    public function vendor_detail($boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::with(['user', 'industry', 'city'])->findOrFail($id);
        return view('frontend.superadmin.vendors.detail', compact('vendor', 'id', 'boxSlug'));
    }

    public function vendor_transaction()
    {
        $grossSales = \App\Models\VendorCommission::sum('transaction_amount');
        $netCleared = \App\Models\VendorCommission::where('status', 'paid')->sum('commission_amount');
        $pendingBalance = \App\Models\VendorCommission::whereIn('status', ['unpaid', 'pending', 'verified'])->sum('commission_amount');
        
        $transactions = \App\Models\VendorCommission::with(['box', 'vendor'])->latest()->get();

        return view('frontend.superadmin.vendors.transaction', compact('grossSales', 'netCleared', 'pendingBalance', 'transactions'));
    }

    public function vendor_approve(Request $request, $boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::with('user')->findOrFail($id);
        $targetAmount = (float) $request->input('target_amount', $request->input('monthly_target', 0));
        $targetType = $request->input('target_type', 'monthly');
        $targetStart = $request->input('target_start_date');
        $targetEnd = $request->input('target_end_date');

        $vendor->status = 'approved';
        $vendor->monthly_target = $targetAmount;
        $vendor->target_type = $targetType;
        $vendor->target_start_date = $targetStart;
        $vendor->target_end_date = $targetEnd;
        
        if (!$vendor->referral_code) {
            $vendor->referral_code = \App\Models\Vendor::generateUniqueReferralCode();
        }
        $vendor->save();

        if ($targetAmount > 0) {
            VendorTarget::create([
                'vendor_id' => $vendor->id,
                'period_type' => $targetType,
                'target_amount' => $targetAmount,
                'start_date' => $targetStart ? \Carbon\Carbon::parse($targetStart)->startOfMonth()->toDateString() : now()->startOfMonth()->toDateString(),
                'end_date' => $targetEnd ? \Carbon\Carbon::parse($targetEnd)->endOfMonth()->toDateString() : null,
                'notes' => 'Initial target assigned upon vendor approval',
            ]);
        }

        SendVendorStatusEmailJob::dispatch($vendor, 'approved', $vendor->referral_code);

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved and target updated successfully.'
        ]);
    }

    public function vendor_update_target(Request $request, $boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        $targetType = strtolower($request->input('target_type', 'monthly'));
        $targetAmount = (float) $request->input('target_amount', $request->input('monthly_target', 0));
        
        $startDate = $request->input('target_start_date') 
            ? \Carbon\Carbon::parse($request->input('target_start_date'))->startOfDay() 
            : now()->startOfMonth();
            
        $endDate = $request->input('target_end_date') 
            ? \Carbon\Carbon::parse($request->input('target_end_date'))->endOfDay() 
            : null;
            
        $notes = $request->input('notes');

        // Check if Stepped / Increased Target is configured (e.g. 3 months same and after 3 months target is increased)
        $isStepped = $request->boolean('is_stepped') 
            || $targetType === 'stepped' 
            || ($request->filled('next_target_amount') && (float) $request->input('next_target_amount') > 0);
            
        $stepMonths = (int) $request->input('step_months', 3);
        $nextTargetAmount = (float) $request->input('next_target_amount', 0);

        if ($isStepped && $stepMonths > 0 && $nextTargetAmount > 0) {
            // First Target: from startDate for stepMonths
            $firstEndDate = (clone $startDate)->addMonths($stepMonths - 1)->endOfMonth();
            $secondStartDate = (clone $firstEndDate)->addDay()->startOfDay();

            VendorTarget::create([
                'vendor_id' => $vendor->id,
                'period_type' => 'monthly',
                'target_amount' => $targetAmount,
                'start_date' => $startDate->toDateString(),
                'end_date' => $firstEndDate->toDateString(),
                'effective_months' => $stepMonths,
                'notes' => $notes ?: "Initial {$stepMonths}-month target (₹{$targetAmount}/mo)",
            ]);

            VendorTarget::create([
                'vendor_id' => $vendor->id,
                'period_type' => 'monthly',
                'target_amount' => $nextTargetAmount,
                'start_date' => $secondStartDate->toDateString(),
                'end_date' => $endDate ? $endDate->toDateString() : null,
                'effective_months' => null,
                'notes' => "Increased target after {$stepMonths} months (₹{$nextTargetAmount}/mo)",
            ]);

            $vendor->monthly_target = $targetAmount;
            $vendor->target_type = 'monthly';
            $vendor->target_start_date = $startDate->toDateString();
            $vendor->target_end_date = null;
            $vendor->save();

            $message = "Stepped target configured: ₹" . number_format($targetAmount, 2) . "/mo for initial {$stepMonths} months, then increased to ₹" . number_format($nextTargetAmount, 2) . "/mo.";
        } else {
            // Standard target: monthly, quarterly, half_yearly, yearly, or custom
            $monthlyEquivalent = $targetAmount;
            if ($targetType === 'quarterly') {
                $monthlyEquivalent = round($targetAmount / 3, 2);
                if (!$endDate) {
                    $endDate = (clone $startDate)->addMonths(2)->endOfMonth();
                }
            } elseif ($targetType === 'half_yearly') {
                $monthlyEquivalent = round($targetAmount / 6, 2);
                if (!$endDate) {
                    $endDate = (clone $startDate)->addMonths(5)->endOfMonth();
                }
            } elseif ($targetType === 'yearly') {
                $monthlyEquivalent = round($targetAmount / 12, 2);
                if (!$endDate) {
                    $endDate = (clone $startDate)->addYear()->subDay()->endOfDay();
                }
            }

            VendorTarget::create([
                'vendor_id' => $vendor->id,
                'period_type' => $targetType,
                'target_amount' => $targetAmount,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate ? $endDate->toDateString() : null,
                'effective_months' => $request->input('effective_months'),
                'notes' => $notes ?: ucfirst(str_replace('_', ' ', $targetType)) . ' target',
            ]);

            $vendor->monthly_target = $monthlyEquivalent;
            $vendor->target_type = $targetType;
            $vendor->target_start_date = $startDate->toDateString();
            $vendor->target_end_date = $endDate ? $endDate->toDateString() : null;
            $vendor->save();

            $message = ucfirst(str_replace('_', ' ', $targetType)) . ' target of ₹' . number_format($targetAmount, 2) . ' saved successfully.';
        }

        $overview = VendorTarget::getFullYearOverview($vendor->id);

        return response()->json([
            'success' => true,
            'message' => $message,
            'target_overview' => $overview,
        ]);
    }

    public function vendor_delete_target(Request $request, $boxSlug, $id, $targetId)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        $target = VendorTarget::where('vendor_id', $vendor->id)->findOrFail($targetId);
        $target->delete();

        // Recalculate latest active target for vendor
        $latest = VendorTarget::where('vendor_id', $vendor->id)->orderBy('start_date', 'desc')->first();
        if ($latest) {
            $vendor->monthly_target = $latest->getMonthlyEquivalent();
            $vendor->target_type = $latest->period_type;
            $vendor->target_start_date = $latest->start_date ? $latest->start_date->toDateString() : null;
            $vendor->target_end_date = $latest->end_date ? $latest->end_date->toDateString() : null;
        } else {
            $vendor->monthly_target = 0;
            $vendor->target_type = 'monthly';
            $vendor->target_start_date = null;
            $vendor->target_end_date = null;
        }
        $vendor->save();

        $overview = VendorTarget::getFullYearOverview($vendor->id);

        return response()->json([
            'success' => true,
            'message' => 'Target schedule entry removed successfully.',
            'target_overview' => $overview,
        ]);
    }

    /**
     * Update commission status: verify, pay, or revert to unpaid.
     */
    public function update_commission_status(Request $request, $boxSlug, $commissionId)
    {
        $commission = \App\Models\VendorCommission::with(['vendor', 'box'])->findOrFail($commissionId);
        $action = strtolower($request->input('action', ''));
        $reference = $request->input('reference');
        $notes = $request->input('notes');
        $adminId = \Illuminate\Support\Facades\Auth::id();

        if ($action === 'verify') {
            $commission->verify($adminId, $notes);
            $message = "Commission #TXN-" . str_pad($commission->id, 5, '0', STR_PAD_LEFT) . " verified successfully.";
        } elseif ($action === 'pay') {
            $commission->markPaid($adminId, $reference, $notes);
            $message = "Commission #TXN-" . str_pad($commission->id, 5, '0', STR_PAD_LEFT) . " marked as paid successfully.";
        } elseif ($action === 'unpaid') {
            $commission->markUnpaid($notes);
            $message = "Commission #TXN-" . str_pad($commission->id, 5, '0', STR_PAD_LEFT) . " reverted to unpaid.";
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid commission status action.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'commission' => [
                'id' => $commission->id,
                'status' => $commission->status,
                'verified_at' => $commission->verified_at ? $commission->verified_at->format('M d, Y h:i A') : null,
                'paid_at' => $commission->paid_at ? $commission->paid_at->format('M d, Y h:i A') : null,
                'payment_reference' => $commission->payment_reference,
                'payment_notes' => $commission->payment_notes,
            ]
        ]);
    }

    public function vendor_reject(Request $request, $boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::with('user')->findOrFail($id);
        $vendor->status = 'rejected';
        $vendor->remarks = $request->remarks;
        $vendor->monthly_target = $request->input('monthly_target', 0);
        $vendor->target_type = $request->input('target_type', 'monthly');
        $vendor->target_start_date = $request->input('target_start_date');
        $vendor->target_end_date = $request->input('target_end_date');
        $vendor->save();

        SendVendorStatusEmailJob::dispatch($vendor, 'rejected');

        return response()->json([
            'success' => true,
            'message' => 'Vendor rejected and target updated successfully.'
        ]);
    }

    public function api_dashboard()
    {
        $currentMonthCommission = \App\Models\VendorCommission::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');

        $lastMonthCommission = \App\Models\VendorCommission::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('commission_amount');
            
        $monthlyTarget = \App\Models\Vendor::where('status', 'approved')->sum('monthly_target');

        return response()->json([
            'currentCommission' => $currentMonthCommission,
            'lastMonthCommission' => $lastMonthCommission,
            'monthlyTarget' => $monthlyTarget,
            'targetType' => 'monthly'
        ]);
    }

    public function api_transaction()
    {
        $grossSales = \App\Models\VendorCommission::sum('transaction_amount');
        $netCleared = \App\Models\VendorCommission::where('status', 'paid')->sum('commission_amount');
        $pendingBalance = \App\Models\VendorCommission::where('status', 'pending')->sum('commission_amount');
        
        $transactions = \App\Models\VendorCommission::with(['box', 'vendor'])->latest()->get();

        return response()->json([
            'grossSales' => $grossSales,
            'netCleared' => $netCleared,
            'pendingBalance' => $pendingBalance,
            'transactions' => $transactions
        ]);
    }

    public function api_list()
    {
        $vendors = \App\Models\Vendor::with('user')->get();
        foreach($vendors as $vendor) {
            $vendor->referred_boxes_count = $vendor->referral_code ? \App\Models\Box::where('referral_code', $vendor->referral_code)->count() : 0;
            $vendor->invitations_count = \App\Models\VendorReferralInvitation::where('vendor_id', $vendor->id)->count();
            $vendor->joined_invitations_count = \App\Models\VendorReferralInvitation::where('vendor_id', $vendor->id)->where('status', 'joined')->count();
            $vendor->referred_vendors_count = \App\Models\Vendor::where('referred_by_vendor_id', $vendor->id)->count();
        }
        return response()->json([
            'vendors' => $vendors
        ]);
    }

    public function api_detail($boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::with(['user', 'industry', 'city'])->findOrFail($id);
        $transactions = \App\Models\VendorCommission::with('box')->where('vendor_id', $vendor->id)->latest()->get();

        $vendorPercentage = \App\Models\VendorPercentage::where('vendor_id', $vendor->id)->first();
        $percentage = $vendorPercentage ? (float) $vendorPercentage->percentage : (float) config('bizzcrm.default_commission_percentage', 10);

        $referredBoxesCount = $vendor->referral_code ? \App\Models\Box::where('referral_code', $vendor->referral_code)->count() : 0;
        $referredVendorsCount = \App\Models\Vendor::where('referred_by_vendor_id', $vendor->id)->count();
        $totalReferrals = $referredBoxesCount + $referredVendorsCount;

        $grossSales = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)->sum('transaction_amount');
        $totalCommission = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)->sum('commission_amount');
        $paidCommission = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)->where('status', 'paid')->sum('commission_amount');
        $pendingCommission = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)->whereIn('status', ['unpaid', 'pending', 'verified'])->sum('commission_amount');
        $unpaidCommission = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)->whereIn('status', ['unpaid', 'pending'])->sum('commission_amount');
        $verifiedCommission = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)->where('status', 'verified')->sum('commission_amount');

        $currentMonthSales = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('transaction_amount');

        $currentMonthCommission = (float) \App\Models\VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');

        $targetOverview = VendorTarget::getFullYearOverview($vendor->id);
        $currentMonthPerf = VendorTarget::calculatePeriodPerformance($vendor->id, 'monthly');
        $currentQuarterPerf = VendorTarget::calculatePeriodPerformance($vendor->id, 'quarterly');
        $currentHalfPerf = VendorTarget::calculatePeriodPerformance($vendor->id, 'half_yearly');
        $currentYearPerf = VendorTarget::calculatePeriodPerformance($vendor->id, 'yearly');

        $effectiveMonthlyTarget = VendorTarget::getTargetForMonth($vendor->id, now()->year, now()->month);
        $monthlyTarget = $effectiveMonthlyTarget > 0 ? $effectiveMonthlyTarget : (float) ($vendor->monthly_target ?? 0);
        $targetProgress = $monthlyTarget > 0 ? min(500, round(($currentMonthSales / $monthlyTarget) * 100, 1)) : 0;
        $targetProgressCommission = $monthlyTarget > 0 ? min(500, round(($currentMonthCommission / $monthlyTarget) * 100, 1)) : 0;

        $stats = [
            'commission_percentage' => $percentage,
            'total_referrals' => $totalReferrals,
            'referred_boxes_count' => $referredBoxesCount,
            'referred_vendors_count' => $referredVendorsCount,
            'gross_sales' => $grossSales,
            'total_commission' => $totalCommission,
            'paid_commission' => $paidCommission,
            'pending_commission' => $pendingCommission,
            'unpaid_commission' => $unpaidCommission,
            'verified_commission' => $verifiedCommission,
            'monthly_target' => $monthlyTarget,
            'target_type' => $vendor->target_type ?: 'monthly',
            'target_start_date' => $vendor->target_start_date,
            'target_end_date' => $vendor->target_end_date,
            'current_month_sales' => $currentMonthSales,
            'current_month_commission' => $currentMonthCommission,
            'target_progress' => $targetProgress,
            'target_progress_commission' => $targetProgressCommission,
        ];

        return response()->json([
            'vendor' => $vendor,
            'stats' => $stats,
            'current_month_perf' => $currentMonthPerf,
            'current_quarter_perf' => $currentQuarterPerf,
            'current_half_perf' => $currentHalfPerf,
            'current_year_perf' => $currentYearPerf,
            'target_overview' => $targetOverview,
            'transactions' => $transactions->map(function ($txn) {
                return [
                    'id' => $txn->id,
                    'box_name' => $txn->box ? $txn->box->name : 'N/A',
                    'box_slug' => $txn->box ? $txn->box->slug : '',
                    'transaction_amount' => (float) $txn->transaction_amount,
                    'commission_percentage' => (float) $txn->commission_percentage,
                    'commission_amount' => (float) $txn->commission_amount,
                    'status' => $txn->status,
                    'verified_at' => $txn->verified_at ? $txn->verified_at->format('M d, Y h:i A') : null,
                    'paid_at' => $txn->paid_at ? \Carbon\Carbon::parse($txn->paid_at)->format('M d, Y h:i A') : null,
                    'payment_reference' => $txn->payment_reference,
                    'payment_notes' => $txn->payment_notes,
                    'created_at' => $txn->created_at ? $txn->created_at->format('M d, Y h:i A') : 'N/A',
                ];
            }),
        ]);
    }

    /**
     * Direct Super Admin Login as Vendor (Impersonation)
     */
    public function impersonate_vendor(Request $request, $boxSlug, $id)
    {
        $vendor = Vendor::with('user')->findOrFail($id);
        if (!$vendor->user_id || !$vendor->user) {
            return redirect()->back()->with('error', 'Vendor user account not found for this profile.');
        }

        // Store original superadmin ID and current box slug in session
        session([
            'impersonated_by' => Auth::id(),
            'impersonated_box_slug' => $boxSlug,
            'is_vendor_impersonation' => true,
        ]);

        // Log in as the vendor user
        Auth::loginUsingId($vendor->user_id);

        return redirect()->route('vendor.dashboard')->with('success', 'You are now logged in as Vendor: ' . ($vendor->company_name ?: $vendor->user->name));
    }

    /**
     * Leave Vendor Impersonation and return to Super Admin Vendors list
     */
    public function leave_vendor_impersonation(Request $request, $boxSlug = null)
    {
        if (session()->has('impersonated_by')) {
            $originalUserId = session('impersonated_by');
            $storedBoxSlug = session('impersonated_box_slug') ?: $boxSlug;

            Auth::loginUsingId($originalUserId);
            session()->forget(['impersonated_by', 'impersonated_box_slug', 'is_vendor_impersonation']);

            if (!$storedBoxSlug) {
                $superadminBox = Box::where('superadmin_id', $originalUserId)->first();
                $storedBoxSlug = $superadminBox ? $superadminBox->slug : 'demobizzcom';
            }

            return redirect()->route('superadmin.vendors.index', ['box_slug' => $storedBoxSlug])
                ->with('success', 'Returned to Superadmin Vendors Management.');
        }

        return redirect()->back();
    }

    /**
     * Fetch all referrals (companies, users, sub-vendors, and sent invitations) for a vendor
     */
    public function vendor_referrals(Request $request, $boxSlug, $id)
    {
        $vendor = Vendor::with('user')->findOrFail($id);

        // 1. Boxes / Companies registered with this vendor's referral code
        $referredBoxes = $vendor->referral_code 
            ? Box::with('superadmin')
                ->withCount('users')
                ->where('referral_code', $vendor->referral_code)
                ->latest()
                ->get()
            : collect();

        // 2. Sub-Vendors registered under this vendor
        $referredVendors = Vendor::with(['user', 'industry'])
            ->where('referred_by_vendor_id', $vendor->id)
            ->latest()
            ->get();

        // 3. Email Invitations sent by this vendor
        $invitations = VendorReferralInvitation::where('vendor_id', $vendor->id)
            ->latest()
            ->get();

        $boxIds = $invitations->pluck('registered_box_id')->filter()->unique();
        $vendorIds = $invitations->pluck('registered_vendor_id')->filter()->unique();

        $boxesMap = $boxIds->isNotEmpty() 
            ? Box::with('superadmin')->whereIn('id', $boxIds)->get()->keyBy('id') 
            : collect();
        $vendorsMap = $vendorIds->isNotEmpty() 
            ? Vendor::with('user')->whereIn('id', $vendorIds)->get()->keyBy('id') 
            : collect();

        $totalJoinedBoxes = $referredBoxes->count();
        $totalJoinedBoxUsers = $referredBoxes->sum('users_count');
        $totalReferredVendors = $referredVendors->count();
        $totalInvitations = $invitations->count();
        $joinedInvitations = $invitations->where('status', 'joined')->count();
        $pendingInvitations = $invitations->where('status', 'pending')->count();

        return response()->json([
            'vendor' => [
                'id' => $vendor->id,
                'company_name' => $vendor->company_name,
                'name' => $vendor->user->name ?? 'N/A',
                'email' => $vendor->user->email ?? 'N/A',
                'phone' => $vendor->mobile_number ?? 'N/A',
                'referral_code' => $vendor->referral_code,
                'referral_url' => $vendor->referral_code ? route('register', ['ref' => $vendor->referral_code]) : '',
            ],
            'summary' => [
                'total_boxes' => $totalJoinedBoxes,
                'total_box_users' => $totalJoinedBoxUsers,
                'total_vendors' => $totalReferredVendors,
                'total_invitations' => $totalInvitations,
                'joined_invitations' => $joinedInvitations,
                'pending_invitations' => $pendingInvitations,
                'total_joined_accounts' => $totalJoinedBoxes + $totalReferredVendors,
            ],
            'boxes' => $referredBoxes->map(function ($box) {
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'slug' => $box->slug,
                    'contact_name' => $box->superadmin->name ?? 'N/A',
                    'contact_email' => $box->superadmin->email ?? 'N/A',
                    'contact_phone' => $box->superadmin->mobile_number ?? ($box->phone ?? 'N/A'),
                    'users_count' => $box->users_count ?: 1,
                    'status' => $box->superadmin->status ?? 'Active',
                    'created_at' => $box->created_at ? $box->created_at->format('M d, Y h:i A') : 'N/A',
                ];
            }),
            'vendors' => $referredVendors->map(function ($v) {
                return [
                    'id' => $v->id,
                    'company_name' => $v->company_name,
                    'contact_name' => $v->user->name ?? 'N/A',
                    'contact_email' => $v->user->email ?? 'N/A',
                    'contact_phone' => $v->mobile_number ?? 'N/A',
                    'industry' => $v->industry->name ?? 'N/A',
                    'status' => $v->status,
                    'created_at' => $v->created_at ? $v->created_at->format('M d, Y h:i A') : 'N/A',
                ];
            }),
            'invitations' => $invitations->map(function ($inv) use ($boxesMap, $vendorsMap) {
                $joinedName = 'N/A';
                if ($inv->registered_box_id && isset($boxesMap[$inv->registered_box_id])) {
                    $box = $boxesMap[$inv->registered_box_id];
                    $joinedName = $box->name . ($box->superadmin ? ' (' . $box->superadmin->name . ')' : '');
                } elseif ($inv->registered_vendor_id && isset($vendorsMap[$inv->registered_vendor_id])) {
                    $v = $vendorsMap[$inv->registered_vendor_id];
                    $joinedName = $v->company_name . ($v->user ? ' (' . $v->user->name . ')' : '');
                }

                return [
                    'id' => $inv->id,
                    'recipient_email' => $inv->recipient_email,
                    'status' => $inv->status,
                    'joined_entity' => $joinedName,
                    'last_sent_at' => $inv->last_sent_at ? $inv->last_sent_at->format('M d, Y h:i A') : ($inv->created_at ? $inv->created_at->format('M d, Y h:i A') : 'N/A'),
                    'registered_at' => $inv->registered_at ? $inv->registered_at->format('M d, Y h:i A') : '-',
                ];
            }),
        ]);
    }
}
