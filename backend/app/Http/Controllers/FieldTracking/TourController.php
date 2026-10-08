<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tour;
use App\Models\BoxUser;
use App\Models\Box;
use Carbon\Carbon;

class TourController extends Controller
{
    public function tourBulkUploading() { return view('fieldstracking.tour-bulk-uploading'); }

    public function tourList(Request $request) {
        $boxSlug = $request->route('box_slug') ?? 'medsyn-lab-biotech';
        return view('fieldstracking.tour-list', compact('boxSlug'));
    }

    public function getFormData(Request $request) {
        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        
        if (!$box) {
            return response()->json(['status' => false, 'data' => []]);
        }

        $boxUsers = BoxUser::with('user')->where('box_id', $box->id)->where('status', 'Enabled')->get();
        $customerTypes = \App\Models\CustomerDetail::whereNotNull('customer_type')
            ->where('box_id', $box->id)
            ->select('customer_type')
            ->distinct()
            ->pluck('customer_type');
        
        return response()->json([
            'status' => true,
            'data' => [
                'staff' => $boxUsers,
                'customer_types' => $customerTypes
            ]
        ]);
    }

    public function getLeadsData(Request $request) {
        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        
        if (!$box) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $term = $request->input('search');
        
        $query = \App\Models\Lead::where('box_id', $box->id)
            ->where(function($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'Customer');
            });
            
        if ($term) {
            $query->where(function($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('company_name', 'LIKE', "%{$term}%");
            });
        }
        
        $leads = $query->select('id', 'name', 'company_name')->paginate(20);
        
        $results = [];
        foreach ($leads as $lead) {
            $displayName = $lead->company_name ? $lead->company_name : $lead->name;
            $results[] = [
                'id' => $lead->id,
                'text' => $displayName
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $leads->hasMorePages()
            ]
        ]);
    }

    public function tourPlan() { return view('fieldstracking.tour-plan'); }

    public function store(Request $request)
    {
        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['status' => false, 'message' => 'Box not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'territory' => 'nullable|string|max:255',
            'staff' => 'nullable|array',
            'staff.*' => 'exists:box_users,id'
        ]);

        $tour = Tour::create([
            'box_id' => $box->id,
            'name' => $request->name,
            'city' => $request->city,
            'territory' => $request->territory,
            'status' => 'Active',
        ]);

        if ($request->has('staff') && is_array($request->staff)) {
            $tour->staff()->attach($request->staff);
        }
        
        if ($request->has('customers') && is_array($request->customers)) {
            $tour->customers()->attach($request->customers);
        }

        if ($request->has('leads') && is_array($request->leads)) {
            $tour->customers()->attach($request->leads); // uses same lead_tour pivot table
        }

        return response()->json([
            'status' => true,
            'message' => 'Beat created successfully',
            'data' => $tour
        ]);
    }

    public function getCustomers(Request $request)
    {
        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['status' => false, 'data' => []]);
        }

        $customerLevel = $request->input('customer_level');
        
        // Fetch leads that have a customer detail matching this level
        $customers = \App\Models\Lead::where('box_id', $box->id)
            ->whereHas('customerDetail', function ($query) use ($customerLevel) {
                if ($customerLevel) {
                    // Match either by customer_level or customer_type based on the form values
                    $query->where('customer_level', $customerLevel)
                          ->orWhere('customer_type', $customerLevel);
                }
            })
            ->select('id', 'name', 'company_name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $customers
        ]);
    }

    public function tourListData(Request $request)
    {
        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['data' => []]);
        }

        $tours = Tour::with('staff.user')->where('box_id', $box->id)->orderBy('id', 'desc')->get();

        $data = $tours->map(function ($tour) {
            $staffNames = $tour->staff->map(function ($boxUser) {
                return $boxUser->user ? $boxUser->user->name : 'Unknown';
            })->implode(', ');

            return [
                'name' => $tour->name,
                'city' => $tour->city ?? '---',
                'territory' => $tour->territory ?? '---',
                'customers' => 0, // Placeholder
                'tc' => '---', // Placeholder
                'pc' => '---', // Placeholder
                'order_value' => '0', // Placeholder
                'previously_visited' => '---', // Placeholder
                'created_at' => $tour->created_at ? $tour->created_at->format('d-M-Y') : '---',
                'updated_at' => $tour->updated_at ? $tour->updated_at->format('d-M-Y') : '---',
                'staff' => $staffNames ?: 'None',
                'action' => '
                    <div class="dropdown table-action">
                        <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                <i class="ti ti-edit text-blue"></i>Edit
                            </a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                <i class="ti ti-trash text-danger"></i>Delete
                            </a>
                        </div>
                    </div>
                '
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'data' => $data
        ]);
    }
}
