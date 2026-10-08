<?php
namespace App\Http\Controllers\Frontend;
use App\Models\Plan;
use App\Models\PackageFeature;
use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    public function facebook_index()
    {
        return view('frontend.index');
    }

    public function features()
    {
        return view('frontend.features');
    }

    public function pricing()
{
    $plans = Plan::active()
        ->subscription()
        ->get()
        ->groupBy('type');

    $monthlyPlans = $plans->get('monthly', collect());
    $yearlyPlans = $plans->get('yearly', collect());

    $whatsappPlans = Plan::active()->whatsapp()->get();

    $masterFeatures = PackageFeature::where('is_active', true)->get();

    return view('frontend.pricing', compact('monthlyPlans', 'yearlyPlans', 'whatsappPlans', 'masterFeatures'));
}

    public function deleteAccount()
    {
        return view('frontend.delete_account');
    }

    public function PrivicayPolicy()
    {
        return view('frontend.privacy_policy');
    }

    public function termsCondition()
    {
        return view('frontend.terms_and_conditions');
    }

    public function RefundCancellation()
    {
        return view('frontend.refund_and_cancellation');
    }

    public function faq()
    {
        return view('frontend.faq');
    }

    public function faqApi(\Illuminate\Http\Request $request)
    {
        $search = $request->query('search');
        
        $query = \App\Models\Faq::where('is_active', 1);
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('question', 'LIKE', '%' . $search . '%')
                  ->orWhere('answer', 'LIKE', '%' . $search . '%');
            });
        }
        
        $faqs = $query->orderBy('order', 'asc')->get();
        
        return response()->json([
            'status' => true,
            'data' => $faqs
        ]);
    }
}
