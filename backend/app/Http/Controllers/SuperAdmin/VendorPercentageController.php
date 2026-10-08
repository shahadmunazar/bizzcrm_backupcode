<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorPercentage;

class VendorPercentageController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('vendorPercentage')->get();
        return view('frontend.superadmin.vendors.percentages', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        VendorPercentage::updateOrCreate(
            ['vendor_id' => $request->vendor_id],
            ['percentage' => $request->percentage]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor percentage updated successfully.',
            'percentage' => $request->percentage
        ]);
    }
}
