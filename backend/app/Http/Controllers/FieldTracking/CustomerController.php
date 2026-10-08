<?php
namespace App\Http\Controllers\FieldTracking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Lead;
use App\Services\FieldTracking\CustomerService;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function customerList(Request $request) 
    { 
        return view('fieldstracking.customer-list'); 
    }

    public function customerListData(Request $request, $box_slug) 
    { 
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['error' => 'Box not found']);

        $customers = $this->customerService->getCustomersDataTableQuery($box, $request);

        return DataTables::eloquent($customers)
            ->addColumn('customer_name', function ($customer) {
                $image = ($customer->customerDetail && $customer->customerDetail->company_image)
                    ? asset('storage/' . $customer->customerDetail->company_image)
                    : asset('assets/img/profiles/avatar-10.jpg');
                $name = $customer->company_name ?? $customer->name;
                $type = ($customer->customerDetail && $customer->customerDetail->customer_type)
                    ? '<span class="badge bg-soft-info">' . $customer->customerDetail->customer_type . '</span>'
                    : '';
                return '<div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 avatar-rounded">
                                <img src="' . $image . '" alt="img">
                            </div>
                            <div>
                                <div class="lh-1">
                                    <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_cus_detail">
                                        <span>' . $name . '</span> ' . $type . '
                                    </a>
                                </div>
                            </div>
                        </div>';
            })
            ->addColumn('check_ins_count', function ($customer) {
                if ($customer->check_ins_count > 0) {
                    return '<button type="button" class="btn btn-sm btn-primary view-checkins-btn" data-customer-id="' . $customer->id . '" data-customer-name="' . htmlspecialchars($customer->company_name ?? $customer->name) . '">' . $customer->check_ins_count . ' Check-Ins</button>';
                }
                return '<span class="badge bg-secondary">0</span>';
            })
            ->addColumn('check_in', function ($customer) {
                return '<button type="button" class="btn btn-success w-100 btn-dt-check-in" data-customer-id="' . $customer->id . '" data-customer-name="' . htmlspecialchars($customer->company_name ?? $customer->name) . '">Check In</button>';
            })
            ->addColumn('location_status', function ($customer) use ($box) {
                if ($customer->customerDetail && $customer->customerDetail->latitude && $customer->customerDetail->longitude) {
                    $radius = $box->customer_location_radius ?? 30;
                    return '<span class="badge bg-soft-success"><i class="ti ti-map-pin"></i> Saved (' . $radius . 'm)</span>';
                }
                return '<span class="badge bg-soft-warning"><i class="ti ti-alert-circle"></i> Not Set</span>';
            })
            ->addColumn('telephonic_order', function ($customer) {
                return '<button type="button" class="btn btn-info w-100 btn-dt-telephonic-order" data-customer-id="' . $customer->id . '" data-customer-name="' . htmlspecialchars($customer->company_name ?? $customer->name) . '">Telephonic Order</button>';
            })
            ->addColumn('name', function ($customer) {
                return '<strong>' . ($customer->name ?? 'N/A') . '</strong>';
            })
            ->addColumn('action', function ($customer) {
                return '<div class="dropdown table-action">
                            <a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item view-customer-btn" href="#" data-id="' . $customer->id . '" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_cus_detail">
                                    <i class="ti ti-eye text-blue"></i>View
                                </a>
                                <a class="dropdown-item edit-customer-btn" href="#" data-id="' . $customer->id . '" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_customer">
                                    <i class="ti ti-edit text-blue"></i>Edit
                                </a>
                                <a class="dropdown-item delete-customer-btn" href="#" data-id="' . $customer->id . '">
                                    <i class="ti ti-trash text-danger"></i>Delete
                                </a>
                            </div>
                        </div>';
            })
            ->addColumn('whatsapp_number', function ($customer) {
                return $customer->customerDetail?->whatsapp_number ?? $customer->phone ?? '';
            })
            ->addColumn('territory', function ($customer) {
                return $customer->customerDetail?->territory ?? 'N/A';
            })
            ->addColumn('city_name', function ($customer) {
                return $customer->city?->name ?? 'N/A';
            })
            ->addColumn('state_name', function ($customer) {
                return $customer->state?->name ?? 'N/A';
            })
            ->addColumn('country_name', function ($customer) {
                return $customer->country?->name ?? 'N/A';
            })
            ->addColumn('lead_source_name', function ($customer) {
                return $customer->leadSource?->name ?? 'N/A';
            })
            ->addColumn('customer_level', function ($customer) {
                return $customer->customerDetail?->customer_level ?? 'N/A';
            })
            ->addColumn('parent_name', function ($customer) {
                return $customer->customerDetail?->parent_name ?? 'N/A';
            })
            ->addColumn('customer_type', function ($customer) {
                return $customer->customerDetail?->customer_type ?? 'N/A';
            })
            ->addColumn('payment_terms', function ($customer) {
                return $customer->customerDetail?->payment_terms ?? 'N/A';
            })
            ->addColumn('credit_limit', function ($customer) {
                return $customer->customerDetail?->credit_limit ?? 0;
            })
            ->addColumn('outstanding_amount', function ($customer) {
                return $customer->customerDetail?->outstanding_amount ?? 0;
            })
            ->addColumn('assigned_staff', function ($customer) {
                return clone $customer->assignees ? $customer->assignees->pluck('name')->implode(', ') : '';
            })
            ->rawColumns(['customer_name', 'location_status', 'check_ins_count', 'check_in', 'telephonic_order', 'name', 'action'])
            ->make(true);
    }

    public function searchLeads(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['results' => [], 'pagination' => ['more' => false]]);

        $term = $request->input('q');
        
        $leadsQuery = Lead::with(['assignees', 'city', 'state', 'country', 'leadSource'])->where('box_id', $box->id)
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

        $results = [];
        foreach ($leads as $lead) {
            $results[] = [
                'id' => $lead->id,
                'text' => ($lead->company_name ? $lead->company_name . ' - ' : '') . $lead->name . ($lead->phone ? ' (' . $lead->phone . ')' : ''),
                'lead_data' => $lead
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $leads->hasMorePages()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $rules = [];
        if (!$request->filled('existing_lead_id') && (!$request->filled('customer_id'))) {
            $rules = [
                'company_name' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
            ];
        }
        $request->validate($rules);
        
        $box = \App\Models\Box::where('slug', $request->route('box_slug'))->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

        $result = $this->customerService->storeCustomer($box, $request, Auth::id());
        
        $status = $result['success'] ? 200 : 500;
        return response()->json($result, $status);
    }

    public function edit($box_slug, $id)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        try {
            $result = $this->customerService->getCustomerWithInsights($box, $id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Customer not found or error occurred: ' . $e->getMessage()], 404);
        }
    }

    public function destroy($box_slug, $id)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $result = $this->customerService->deleteCustomer($box, $id);
        
        $status = $result['success'] ? 200 : 500;
        return response()->json($result, $status);
    }

    public function storeCheckIn(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->first();
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

    public function checkInHistory($box_slug, $customer_id)
    {
        $checkIns = $this->customerService->getCheckInHistory($customer_id);

        return response()->json([
            'success' => true,
            'data' => $checkIns
        ]);
    }
}
