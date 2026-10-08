<?php

namespace App\Services\FieldTracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Box;
use App\Models\Lead;
use App\Models\CustomerDetail;
use App\Models\CustomerCheckIn;
use App\Models\Deal;
use App\Models\Activity;

class CustomerService
{
    /**
     * Get the query builder for the customers data table
     */
    public function getCustomersDataTableQuery(Box $box, Request $request)
    {
        $customers = Lead::with([
            'customerDetail:id,lead_id,company_image,customer_type,whatsapp_number,territory,customer_level,parent_name,payment_terms,credit_limit,outstanding_amount,latitude,longitude', 
            'city:id,name', 
            'state:id,name', 
            'country:id,name', 
            'leadSource:id,name', 
            'assignees:users.id,users.name'
        ])
        ->where('box_id', $box->id)
        ->where('status', 'Customer')
        ->select([
            'id', 'box_id', 'status', 'company_name', 'name', 'phone', 'mobile', 'email', 'sec_email', 
            'designation', 'gstin_number', 'drug_license', 'pan_number', 'address', 'location', 
            'postal_code', 'experience', 'query', 'city_id', 'state_id', 'country_id', 'lead_source_id',
            'created_at', 'updated_at' // Needed for date sorting/filtering
        ])
        ->withCount('checkIns');

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($request->start_date))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($request->end_date))->endOfDay();
                $customers->whereBetween('leads.created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // If format is different, fallback to standard parsing
                $customers->whereBetween('leads.created_at', [
                    \Carbon\Carbon::parse($request->start_date)->startOfDay(),
                    \Carbon\Carbon::parse($request->end_date)->endOfDay()
                ]);
            }
        }

        if ($request->has('sort') && $request->sort) {
            switch ($request->sort) {
                case 'asc':
                    $customers->orderBy('leads.name', 'asc');
                    break;
                case 'desc':
                    $customers->orderBy('leads.name', 'desc');
                    break;
                case 'recently_added':
                    $customers->orderBy('leads.created_at', 'desc');
                    break;
                case 'recently_viewed':
                    // Just default to recently updated
                    $customers->orderBy('leads.updated_at', 'desc');
                    break;
            }
        } else {
            // Default sort
            $customers->orderBy('leads.id', 'desc');
        }

        return $customers;
    }

    /**
     * Store or update a customer
     */
    public function storeCustomer(Box $box, Request $request, $userId)
    {
        DB::beginTransaction();
        try {
            // 1. Check if we are updating an existing Lead or creating a new one
            if ($request->filled('customer_id')) {
                // UPDATE EXISTING CUSTOMER
                $lead = Lead::where('box_id', $box->id)->findOrFail($request->customer_id);
                $lead->company_name = $request->company_name;
                $lead->gstin_number = $request->gstin_number;
                $lead->name = $request->name;
                $lead->email = $request->email;
                $lead->sec_email = $request->sec_email;
                $lead->phone = $request->phone;
                $lead->mobile = $request->mobile;
                $lead->designation = $request->designation;
                $lead->address = $request->address;
                $lead->postal_code = $request->postal_code;
                $lead->country_id = $request->country_id;
                $lead->state_id = $request->state_id;
                $lead->city_id = $request->city_id;
                $lead->lead_source_id = $request->lead_source_id;
                $lead->drug_license = $request->drug_license;
                $lead->pan_number = $request->pan_number;
                $lead->location = $request->location;
                $lead->experience = $request->experience;
                $lead->query = $request->query_notes;
                $lead->save();
            } else if ($request->filled('existing_lead_id')) {
                $lead = Lead::where('box_id', $box->id)->findOrFail($request->existing_lead_id);
                $lead->status = 'Customer'; // Mark as customer
                
                // Update specific lead fields if they are currently empty but provided in the request
                $fieldsToCheck = [
                    'company_name', 'gstin_number', 'name', 'email', 'sec_email', 
                    'phone', 'mobile', 'designation', 'address', 'postal_code', 
                    'country_id', 'state_id', 'city_id', 'lead_source_id', 
                    'drug_license', 'pan_number', 'location', 'experience'
                ];
                
                foreach ($fieldsToCheck as $field) {
                    if (empty($lead->$field) && $request->filled($field)) {
                        $lead->$field = $request->$field;
                    }
                }
                
                if (empty($lead->query) && $request->filled('query_notes')) {
                    $lead->query = $request->query_notes;
                }
                
                $lead->save();
            } else {
                $lead = new Lead();
                $lead->box_id = $box->id;
                $lead->company_name = $request->company_name;
                $lead->gstin_number = $request->gstin_number;
                $lead->name = $request->name;
                $lead->email = $request->email;
                $lead->sec_email = $request->sec_email;
                $lead->phone = $request->phone;
                $lead->mobile = $request->mobile;
                $lead->designation = $request->designation;
                $lead->address = $request->address;
                $lead->postal_code = $request->postal_code;
                $lead->country_id = $request->country_id;
                $lead->state_id = $request->state_id;
                $lead->city_id = $request->city_id;
                $lead->lead_source_id = $request->lead_source_id;
                $lead->drug_license = $request->drug_license;
                $lead->pan_number = $request->pan_number;
                $lead->location = $request->location;
                $lead->experience = $request->experience;
                $lead->query = $request->query_notes;
                $lead->status = 'Customer'; // Mark as customer
                $lead->added_by = $userId;
                $lead->save();
            }

            // Handle Image Upload
            $companyImagePath = null;
            if ($request->hasFile('company_image')) {
                $companyImagePath = $request->file('company_image')->store('customers', 'public');
            }

            $customerDetailData = [
                'company_image' => $companyImagePath ?: DB::raw('company_image'), // keep existing image if no new one
                'customer_level' => $request->customer_level,
                'parent_name' => $request->parent_name,
                'whatsapp_number' => $request->whatsapp_number,
                'customer_type' => $request->customer_type,
                'territory' => $request->territory,
                'payment_terms' => $request->payment_terms,
                'credit_limit' => $request->credit_limit ?? 0,
                'outstanding_amount' => $request->outstanding_amount ?? 0,
                'created_by' => $userId,
            ];

            if ($request->filled('latitude')) $customerDetailData['latitude'] = $request->latitude;
            if ($request->filled('longitude')) $customerDetailData['longitude'] = $request->longitude;

            // 2. Update or Create the CustomerDetail record
            CustomerDetail::updateOrCreate(
                ['lead_id' => $lead->id],
                $customerDetailData
            );

            // 3. Assign Staff (Pivot table)
            if ($request->filled('customer_id') || !$request->filled('existing_lead_id')) {
                if ($request->has('assignees') && is_array($request->assignees)) {
                    $lead->assignees()->sync($request->assignees);
                }
            }

            DB::commit();
            return ['success' => true, 'message' => 'Customer created successfully!'];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get a customer with calculated insights
     */
    public function getCustomerWithInsights(Box $box, $customerId)
    {
        $customer = Lead::with(['customerDetail', 'assignees', 'city', 'state', 'country', 'leadSource'])
                        ->where('box_id', $box->id)
                        ->findOrFail($customerId);

        $tc = CustomerCheckIn::where('customer_id', $customer->id)->count();
        $pc = Deal::where('lead_id', $customer->id)->count();
        $output_percentage = $tc > 0 ? round(($pc / $tc) * 100, 2) : 0;
        
        $lastVisitDate = CustomerCheckIn::where('customer_id', $customer->id)->latest('created_at')->value('created_at');
        
        $lastOrder = Deal::where('lead_id', $customer->id)->latest('created_at')->first();
        $lastOrderDate = $lastOrder ? $lastOrder->created_at : null;
        $lastOrderValue = $lastOrder ? $lastOrder->deal_value : 0;
        
        $avgOrderValue = Deal::where('lead_id', $customer->id)->avg('deal_value') ?? 0;

        $insights = [
            'tc' => $tc,
            'pc' => $pc,
            'output_percentage' => $output_percentage,
            'last_visit_date' => $lastVisitDate ? \Carbon\Carbon::parse($lastVisitDate)->format('d M Y') : 'N/A',
            'last_order_date' => $lastOrderDate ? \Carbon\Carbon::parse($lastOrderDate)->format('d M Y') : 'N/A',
            'last_order_value' => round($lastOrderValue, 2),
            'avg_order_value' => round($avgOrderValue, 2)
        ];

        return [
            'success' => true,
            'data' => $customer,
            'insights' => $insights
        ];
    }

    /**
     * Delete a customer and associated details
     */
    public function deleteCustomer(Box $box, $customerId)
    {
        try {
            $customer = Lead::where('box_id', $box->id)->findOrFail($customerId);
            
            if ($customer->customerDetail) {
                $customer->customerDetail->delete();
            }
            $customer->delete(); 

            return ['success' => true, 'message' => 'Customer deleted successfully!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Calculate distance between two coordinates in meters (Haversine formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius in meters
        
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($lat1) * cos($lat2) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Store a new Check-in for a customer
     */
    public function storeCheckIn(Box $box, Request $request, $userId)
    {
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('checkins', 'public');
                $imagePaths[] = $path;
            }
        }

        try {
            $customer = Lead::with('customerDetail')->where('box_id', $box->id)->findOrFail($request->customer_id);

            // Radius Validation
            if ($customer->customerDetail && $customer->customerDetail->latitude && $customer->customerDetail->longitude) {
                $allowedRadius = $box->customer_location_radius ?? 30;
                $distance = $this->calculateDistance($customer->customerDetail->latitude, $customer->customerDetail->longitude, $request->latitude, $request->longitude);
                
                if ($distance > $allowedRadius) {
                    return [
                        'success' => false, 
                        'message' => "You are " . round($distance - $allowedRadius) . " meters outside the allowed radius. Current distance: " . round($distance) . "m, Required radius: {$allowedRadius}m."
                    ];
                }
            } else {
                // First check-in, set location
                CustomerDetail::updateOrCreate(
                    ['lead_id' => $customer->id],
                    [
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'created_by' => $userId
                    ]
                );
            }

            $checkIn = CustomerCheckIn::create([
                'customer_id' => $request->customer_id,
                'box_id' => $box->id,
                'boxuser_id' => $userId,
                'remarks' => $request->remarks,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'images' => empty($imagePaths) ? null : $imagePaths,
                'created_by' => $userId,
            ]);

            // Create Activity for this Check-in
            Activity::create([
                'box_id'        => $box->id,
                'activity_id'   => $request->customer_id,
                'activity_type' => 'check_in',
                'type'          => 'general',
                'subject'       => 'Check-in Completed',
                'description'   => "Location: {$request->latitude}, {$request->longitude}\nRemarks: {$request->remarks}",
                'added_by'      => $userId,
                'activity_date' => now(),
            ]);

            return ['success' => true, 'message' => 'Check-in completed successfully', 'data' => $checkIn];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Fetch Check-in history for a customer
     */
    public function getCheckInHistory($customerId)
    {
        return CustomerCheckIn::where('customer_id', $customerId)
            ->with(['creator' => function($q) {
                $q->select('id', 'name'); 
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
