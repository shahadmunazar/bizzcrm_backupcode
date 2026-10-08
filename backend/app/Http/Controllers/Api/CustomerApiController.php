<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Box;
use App\Models\Lead;
use App\Services\FieldTracking\CustomerService;

class CustomerApiController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $query = $this->customerService->getCustomersDataTableQuery($box, $request);
        
        // Use standard pagination for the API
        $perPage = $request->input('per_page', 20);
        $customers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    public function show(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        try {
            $result = $this->customerService->getCustomerWithInsights($box, $id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Customer not found or error occurred: ' . $e->getMessage()], 404);
        }
    }

    public function store(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $rules = [];
        if (!$request->filled('existing_lead_id') && !$request->filled('customer_id')) {
            $rules = [
                'company_name' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
            ];
        }
        $request->validate($rules);
        
        $result = $this->customerService->storeCustomer($box, $request, Auth::id());
        
        $status = $result['success'] ? 200 : 500;
        return response()->json($result, $status);
    }

    public function destroy(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $result = $this->customerService->deleteCustomer($box, $id);
        
        $status = $result['success'] ? 200 : 500;
        return response()->json($result, $status);
    }

    public function storeCheckIn(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $request->validate([
            'customer_id' => 'required|exists:leads,id',
            'remarks' => 'nullable|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $result = $this->customerService->storeCheckIn($box, $request, Auth::id());

        $status = $result['success'] ? 200 : 500;
        return response()->json($result, $status);
    }

    public function checkInHistory(Request $request, $box_slug, $id)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $checkIns = $this->customerService->getCheckInHistory($id);

        return response()->json([
            'success' => true,
            'data' => $checkIns
        ]);
    }

    public function searchLeads(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $term = $request->input('q');
        
        $leadsQuery = Lead::with(['assignees', 'city', 'state', 'country', 'leadSource'])
                     ->where('box_id', $box->id)
                     ->where(function($q) {
                         $q->where('status', '!=', 'Customer')
                           ->orWhereNull('status');
                     });
                     
        if ($term) {
            $leadsQuery->where(function($query) use ($term) {
                 $query->where('name', 'LIKE', "%{$term}%")
                       ->orWhere('phone', 'LIKE', "%{$term}%")
                       ->orWhere('email', 'LIKE', "%{$term}%")
                       ->orWhere('company_name', 'LIKE', "%{$term}%");
             });
        }
                     
        $leads = $leadsQuery->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $leads
        ]);
    }
}
