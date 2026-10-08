<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\BoxUser;
use App\Models\Box;
use Carbon\Carbon;

class FieldTrackingTourApiController extends Controller
{
    public function getFormData(Request $request, $box_slug) {
        $box = Box::where('slug', $box_slug)->first();
        
        if (!$box) {
            return response()->json(['success' => false, 'data' => [], 'message' => 'Box not found'], 404);
        }

        $boxUsers = BoxUser::with('user')->where('box_id', $box->id)->where('status', 'Enabled')->get();
        
        $staff = $boxUsers->map(function ($boxUser) {
            return [
                'id' => $boxUser->id,
                'user_id' => $boxUser->user_id,
                'name' => $boxUser->user ? $boxUser->user->name : 'Unknown',
                'avatar' => $boxUser->user && $boxUser->user->avatar ? asset($boxUser->user->avatar) : null
            ];
        });

        $customerTypes = \App\Models\CustomerDetail::whereNotNull('customer_type')
            ->where('box_id', $box->id)
            ->select('customer_type')
            ->distinct()
            ->pluck('customer_type');
        
        return response()->json([
            'success' => true,
            'data' => [
                'staff' => $staff,
                'customer_types' => $customerTypes
            ]
        ]);
    }

    public function getLeadsData(Request $request, $box_slug) {
        $box = Box::where('slug', $box_slug)->first();
        
        if (!$box) {
            return response()->json(['success' => false, 'results' => [], 'pagination' => ['more' => false], 'message' => 'Box not found'], 404);
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
            'success' => true,
            'results' => $results,
            'pagination' => [
                'more' => $leads->hasMorePages()
            ]
        ]);
    }

    public function store(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'territory' => 'nullable|string|max:255',
            'staff' => 'nullable|array',
            'staff.*' => 'exists:box_users,id',
            'customers' => 'nullable|array',
            'leads' => 'nullable|array'
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
            'success' => true,
            'message' => 'Tour created successfully',
            'data' => $tour
        ]);
    }

    public function getCustomers(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'data' => [], 'message' => 'Box not found'], 404);
        }

        $customerLevel = $request->input('customer_level');
        
        // Fetch leads that have a customer detail matching this level
        $customers = \App\Models\Lead::where('box_id', $box->id)
            ->whereHas('customerDetail', function ($query) use ($customerLevel) {
                if ($customerLevel) {
                    $query->where('customer_level', $customerLevel)
                          ->orWhere('customer_type', $customerLevel);
                }
            })
            ->select('id', 'name', 'company_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    public function tourListData(Request $request, $box_slug)
    {
        $box = Box::where('slug', $box_slug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'data' => [], 'message' => 'Box not found'], 404);
        }

        $perPage = $request->input('per_page', 20);
        $tours = Tour::with('staff.user')->where('box_id', $box->id)->orderBy('id', 'desc')->paginate($perPage);

        $data = $tours->map(function ($tour) {
            $staff = $tour->staff->map(function ($boxUser) {
                return [
                    'id' => $boxUser->id,
                    'name' => $boxUser->user ? $boxUser->user->name : 'Unknown'
                ];
            });

            return [
                'id' => $tour->id,
                'name' => $tour->name,
                'city' => $tour->city ?? '---',
                'territory' => $tour->territory ?? '---',
                'customers' => 0, // Placeholder
                'tc' => '---', // Placeholder
                'pc' => '---', // Placeholder
                'order_value' => '0', // Placeholder
                'previously_visited' => '---', // Placeholder
                'created_at' => $tour->created_at ? $tour->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $tour->updated_at ? $tour->updated_at->format('Y-m-d H:i:s') : null,
                'staff' => $staff
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $tours->total(),
                'current_page' => $tours->currentPage(),
                'last_page' => $tours->lastPage(),
                'per_page' => $tours->perPage()
            ]
        ]);
    }
}
