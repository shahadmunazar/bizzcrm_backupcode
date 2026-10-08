<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\MasterIndustry;
use Illuminate\Support\Str;
use App\Jobs\SendVendorStatusEmailJob;

class VendorManagementController extends Controller
{
    public function vendor_login()
    {
        return view('auth.vendor_login');
    }

    public function vendor_signup()
    {
        $industries = \App\Models\MasterIndustry::all();
        return view('auth.vendor_signup', compact('industries'));
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
        return view('frontend.superadmin.vendors.detail');
    }

    public function vendor_transaction()
    {
        $grossSales = \App\Models\VendorCommission::sum('transaction_amount');
        $netCleared = \App\Models\VendorCommission::where('status', 'paid')->sum('commission_amount');
        $pendingBalance = \App\Models\VendorCommission::where('status', 'pending')->sum('commission_amount');
        
        $transactions = \App\Models\VendorCommission::with(['box', 'vendor'])->latest()->get();

        return view('frontend.superadmin.vendors.transaction', compact('grossSales', 'netCleared', 'pendingBalance', 'transactions'));
    }

    public function vendor_approve(Request $request, $boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::with('user')->findOrFail($id);
        $vendor->status = 'approved';
        $vendor->monthly_target = $request->input('monthly_target', 0);
        $vendor->target_type = $request->input('target_type', 'monthly');
        $vendor->target_start_date = $request->input('target_start_date');
        $vendor->target_end_date = $request->input('target_end_date');
        
        if (!$vendor->referral_code) {
            $vendor->referral_code = 'VNDR-' . strtoupper(Str::random(6));
        }
        $vendor->save();

        SendVendorStatusEmailJob::dispatch($vendor, 'approved', $vendor->referral_code);

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved and target updated successfully.'
        ]);
    }

    public function vendor_update_target(Request $request, $boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        $vendor->monthly_target = $request->input('monthly_target', 0);
        $vendor->target_type = $request->input('target_type', 'monthly');
        $vendor->target_start_date = $request->input('target_start_date');
        $vendor->target_end_date = $request->input('target_end_date');
        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => 'Vendor monthly target updated successfully.'
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
            $vendor->referred_boxes_count = \App\Models\Box::where('referral_code', $vendor->referral_code)->count();
        }
        return response()->json([
            'vendors' => $vendors
        ]);
    }

    public function api_detail($boxSlug, $id)
    {
        $vendor = \App\Models\Vendor::with(['user', 'industry', 'city'])->findOrFail($id);
        $transactions = \App\Models\VendorCommission::where('vendor_id', $vendor->id)->latest()->get();
        return response()->json([
            'vendor' => $vendor,
            'transactions' => $transactions
        ]);
    }

}
