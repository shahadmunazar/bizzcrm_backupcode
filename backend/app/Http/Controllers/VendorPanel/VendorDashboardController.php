<?php

namespace App\Http\Controllers\VendorPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $currentMonthCommission = \App\Models\VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');

        $lastMonthCommission = \App\Models\VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('commission_amount');

        return view('frontend.vendor_panel.dashboard.index', compact('vendor', 'currentMonthCommission', 'lastMonthCommission'));
    }

    public function transaction()
    {
        $vendor = Auth::user()->vendor;
        $grossSales = \App\Models\VendorCommission::where('vendor_id', $vendor->id)->sum('transaction_amount');
        $netCleared = \App\Models\VendorCommission::where('vendor_id', $vendor->id)->where('status', 'paid')->sum('commission_amount');
        $pendingBalance = \App\Models\VendorCommission::where('vendor_id', $vendor->id)->where('status', 'pending')->sum('commission_amount');
        
        $transactions = \App\Models\VendorCommission::with('box')->where('vendor_id', $vendor->id)->latest()->get();

        return view('frontend.vendor_panel.dashboard.transaction', compact('vendor', 'grossSales', 'netCleared', 'pendingBalance', 'transactions'));
    }

    public function commission()
    {
        $vendor = Auth::user()->vendor;
        $commissions = \App\Models\VendorCommission::with('box')->where('vendor_id', $vendor->id)->latest()->get();
        return view('frontend.vendor_panel.dashboard.commission', compact('vendor', 'commissions'));
    }

    public function referrals()
    {
        $vendor = Auth::user()->vendor;
        $referredBoxes = \App\Models\Box::where('referral_code', $vendor->referral_code)->latest()->get();
        return view('frontend.vendor_panel.dashboard.referrals', compact('vendor', 'referredBoxes'));
    }

    // Added referral_details method
    public function referral_details($id)
    {
        $vendor = Auth::user()->vendor;
        $box = \App\Models\Box::where('referral_code', $vendor->referral_code)->findOrFail($id);
        
        $transactions = \App\Models\VendorCommission::where('vendor_id', $vendor->id)
            ->where('box_id', $box->id)
            ->latest()
            ->get();
            
        return view('frontend.vendor_panel.dashboard.vendor_detail', compact('vendor', 'box', 'transactions'));
    }

    public function api_dashboard()
    {
        $vendor = Auth::user()->vendor;
        
        $currentQuery = \App\Models\VendorCommission::where('vendor_id', $vendor->id);
        
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

        $lastMonthCommission = \App\Models\VendorCommission::where('vendor_id', $vendor->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('commission_amount');

        return response()->json([
            'currentCommission' => $currentCommission,
            'lastMonthCommission' => $lastMonthCommission,
            'monthlyTarget' => $vendor->monthly_target,
            'targetType' => $vendor->target_type,
            'targetStartDate' => $vendor->target_start_date,
            'targetEndDate' => $vendor->target_end_date
        ]);
    }

    public function api_transaction()
    {
        $vendor = Auth::user()->vendor;
        $grossSales = \App\Models\VendorCommission::where('vendor_id', $vendor->id)->sum('transaction_amount');
        $netCleared = \App\Models\VendorCommission::where('vendor_id', $vendor->id)->where('status', 'paid')->sum('commission_amount');
        $pendingBalance = \App\Models\VendorCommission::where('vendor_id', $vendor->id)->where('status', 'pending')->sum('commission_amount');
        
        $transactions = \App\Models\VendorCommission::with('box')->where('vendor_id', $vendor->id)->latest()->get();

        return response()->json([
            'grossSales' => $grossSales,
            'netCleared' => $netCleared,
            'pendingBalance' => $pendingBalance,
            'transactions' => $transactions
        ]);
    }

    public function api_referrals()
    {
        $vendor = Auth::user()->vendor;
        $referredBoxes = \App\Models\Box::where('referral_code', $vendor->referral_code)->latest()->get();
        return response()->json([
            'referredBoxes' => $referredBoxes
        ]);
    }

    public function api_referral_details($id)
    {
        $vendor = Auth::user()->vendor;
        $box = \App\Models\Box::where('referral_code', $vendor->referral_code)->findOrFail($id);
        $transactions = \App\Models\VendorCommission::where('vendor_id', $vendor->id)
            ->where('box_id', $box->id)
            ->latest()
            ->get();
            
        return response()->json([
            'box' => $box,
            'transactions' => $transactions
        ]);
    }

}
