<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\PackageFeature;
use App\Models\BoxUser;
use App\Models\Industry;
use App\Models\MasterIndustry;
use App\Models\Coupon;
use Illuminate\Support\Str;

class SuperadminFrontendController extends Controller
{
    protected $razorpayService;
    private const TEAM_ID_OFFSET = 1000000;


    public function __construct(\App\Services\RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $total_companies = \App\Models\Box::count();
            $active_companies = \App\Models\Box::where('status', '1')->count();
            $total_subscribers = \App\Models\UserSubscription::count();
            
            // New companies today
            $new_companies_today = \App\Models\Box::whereDate('created_at', now()->toDateString())->count();

            // Month over month calculations (simple mock logic if historical data is sparse)
            $this_month_companies = \App\Models\Box::whereMonth('created_at', now()->month)->count();
            $last_month_companies = \App\Models\Box::whereMonth('created_at', now()->subMonth()->month)->count();
            
            $companies_perc = $last_month_companies > 0 ? (($this_month_companies - $last_month_companies) / $last_month_companies) * 100 : 100;
            $companies_perc_str = ($companies_perc >= 0 ? '+' : '') . number_format($companies_perc, 2) . '%';

            // Revenue calculation
            $total_revenue = 0;
            $all_subs = \App\Models\UserSubscription::with('plan')->get();
            foreach($all_subs as $sub) {
                if ($sub->plan) {
                    $total_revenue += (float) $sub->plan->price;
                }
            }
            $revenue_str = '$' . number_format($total_revenue, 2);

            // Top plans percentages
            $total_subs = $all_subs->count();
            $top_plans = [];
            if ($total_subs > 0) {
                $plan_counts = $all_subs->groupBy('plan_id')->map->count();
                $plans = \App\Models\Plan::whereIn('id', $plan_counts->keys())->get()->keyBy('id');
                
                foreach($plan_counts as $plan_id => $count) {
                    if (isset($plans[$plan_id])) {
                        $top_plans[] = [
                            'name' => $plans[$plan_id]->name,
                            'percentage' => round(($count / $total_subs) * 100) . '%',
                            'color' => 'primary' // default color
                        ];
                    }
                }
                
                // Sort by count desc and take top 3
                usort($top_plans, function($a, $b) {
                    return (int)$b['percentage'] - (int)$a['percentage'];
                });
                $top_plans = array_slice($top_plans, 0, 3);
                
                $colors = ['primary', 'warning', 'info'];
                foreach($top_plans as $index => &$tp) {
                    $tp['color'] = $colors[$index % count($colors)];
                }
            }

            // Recent Transactions
            $recent_transactions_raw = \App\Models\UserSubscription::with(['box', 'plan'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            $recent_transactions = [];
            foreach ($recent_transactions_raw as $sub) {
                $box = $sub->box;
                $logo = null;
                if ($box) {
                    $logo = $box->company_logo ? asset($box->company_logo) : ($box->logo ? asset($box->logo) : null);
                }
                $recent_transactions[] = [
                    'company_name' => $box->name ?? 'Unknown',
                    'logo' => $logo,
                    'date' => $sub->created_at ? $sub->created_at->format('d M Y') : '',
                    'plan_name' => $sub->plan->name ?? 'N/A',
                    'amount' => $sub->plan ? '+$' . $sub->plan->price : '+$0',
                    'id_label' => '#' . rand(10000, 99999) // mock id
                ];
            }

            // Recently Registered
            $recently_registered_raw = \App\Models\Box::with('users')->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
            $recently_registered = [];
            foreach ($recently_registered_raw as $box) {
                $sub = \App\Models\UserSubscription::with('plan')->where('box_id', $box->id)->latest()->first();
                $users_count = $box->users->count();
                $logo = $box->company_logo ? asset($box->company_logo) : ($box->logo ? asset($box->logo) : null);
                
                $recently_registered[] = [
                    'company_name' => $box->name ?? 'Unknown',
                    'logo' => $logo,
                    'plan_name' => $sub && $sub->plan ? $sub->plan->name : 'Basic (Monthly)',
                    'users_count' => $users_count . ' Users',
                    'domain' => $box->domain ?? ($box->slug ? $box->slug . '.example.com' : 'example.com'),
                ];
            }
            
            // Recent Plan Expired
            $recent_expired_raw = \App\Models\UserSubscription::with(['box', 'plan'])
                ->where('ends_at', '<', now())
                ->orderBy('ends_at', 'desc')
                ->take(5)
                ->get();
                
            $recent_expired = [];
            foreach ($recent_expired_raw as $sub) {
                $box = $sub->box;
                $logo = null;
                if ($box) {
                    $logo = $box->company_logo ? asset($box->company_logo) : ($box->logo ? asset($box->logo) : null);
                }
                $recent_expired[] = [
                    'company_name' => $box->name ?? 'Unknown',
                    'logo' => $logo,
                    'plan_name' => $sub->plan->name ?? 'N/A',
                    'expired_date' => 'Expired : ' . ($sub->ends_at ? $sub->ends_at->format('d M Y') : 'Unknown')
                ];
            }

            // Filter parameters
            $company_filter = $request->input('company_filter', 'this_week'); // this_week, this_month, today
            $revenue_year = $request->input('revenue_year', date('Y')); // e.g. 2025
            $plan_filter = $request->input('plan_filter', 'this_month'); // this_month, this_week, today

            // 1. Company Chart Data (Count by Day/Week/Month)
            $company_chart_data = [];
            $company_chart_categories = [];
            
            if ($company_filter == 'this_week') {
                $startOfWeek = now()->startOfWeek();
                for($i=0; $i<7; $i++) {
                    $date = $startOfWeek->copy()->addDays($i);
                    $count = \App\Models\Box::whereDate('created_at', $date->toDateString())->count();
                    $company_chart_data[] = $count;
                    $company_chart_categories[] = substr($date->format('D'), 0, 1); // M, T, W, etc.
                }
            } elseif ($company_filter == 'this_month') {
                $startOfMonth = now()->startOfMonth();
                $daysInMonth = now()->daysInMonth;
                for($i=1; $i<=$daysInMonth; $i+=5) { // Group by 5 days or just plot weeks
                    $date = $startOfMonth->copy()->addDays($i-1);
                    $end_date = $date->copy()->addDays(4);
                    $count = \App\Models\Box::whereBetween('created_at', [$date->startOfDay(), $end_date->endOfDay()])->count();
                    $company_chart_data[] = $count;
                    $company_chart_categories[] = $date->format('d M');
                }
            } elseif ($company_filter == 'today') {
                for($i=0; $i<24; $i+=4) {
                    $count = \App\Models\Box::whereDate('created_at', now()->toDateString())
                        ->whereTime('created_at', '>=', sprintf('%02d:00:00', $i))
                        ->whereTime('created_at', '<', sprintf('%02d:00:00', $i+4))
                        ->count();
                    $company_chart_data[] = $count;
                    $company_chart_categories[] = sprintf('%02d:00', $i);
                }
            }

            // 2. Revenue Chart Data (Income by Month for the selected year)
            // User requested to ensure offline transactions are also checked. We will sum up all subscriptions for the year.
            $revenue_chart_data = [];
            $revenue_chart_categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            for($m=1; $m<=12; $m++) {
                $monthly_subs = \App\Models\UserSubscription::with('plan')
                    ->whereYear('created_at', $revenue_year)
                    ->whereMonth('created_at', $m)
                    ->get();
                $sum = 0;
                foreach($monthly_subs as $sub) {
                    if ($sub->plan) {
                        $sum += (float) $sub->plan->price;
                    }
                }
                $revenue_chart_data[] = $sum;
            }

            // 3. Top Plans Pie Chart Data
            $plan_query = \App\Models\UserSubscription::with('plan');
            if ($plan_filter == 'this_month') {
                $plan_query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            } elseif ($plan_filter == 'this_week') {
                $plan_query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($plan_filter == 'today') {
                $plan_query->whereDate('created_at', now()->toDateString());
            }
            
            $filtered_subs = $plan_query->get();
            $total_filtered_subs = $filtered_subs->count();
            
            $plan_pie_series = [];
            $plan_pie_labels = [];
            
            if ($total_filtered_subs > 0) {
                $plan_counts = $filtered_subs->groupBy('plan_id')->map->count();
                $plans = \App\Models\Plan::whereIn('id', $plan_counts->keys())->get()->keyBy('id');
                
                $temp_plans = [];
                foreach($plan_counts as $plan_id => $count) {
                    if (isset($plans[$plan_id])) {
                        $temp_plans[] = [
                            'name' => $plans[$plan_id]->name,
                            'count' => $count
                        ];
                    }
                }
                usort($temp_plans, function($a, $b) {
                    return $b['count'] - $a['count'];
                });
                $temp_plans = array_slice($temp_plans, 0, 3);
                foreach($temp_plans as $tp) {
                    $plan_pie_series[] = $tp['count'];
                    $plan_pie_labels[] = $tp['name'];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_companies' => $total_companies,
                    'active_companies' => $active_companies,
                    'total_subscribers' => $total_subscribers,
                    'total_earnings' => $revenue_str,
                    
                    'new_companies_today' => $new_companies_today,
                    'companies_perc_str' => $companies_perc_str,
                    'last_month_companies' => $last_month_companies,
                    
                    'top_plans' => $top_plans, // For the text list
                    
                    'recent_transactions' => $recent_transactions,
                    'recently_registered' => $recently_registered,
                    'recent_expired' => $recent_expired,
                    
                    // Chart specific data
                    'charts' => [
                        'company' => [
                            'series' => $company_chart_data,
                            'categories' => $company_chart_categories
                        ],
                        'revenue' => [
                            'series' => $revenue_chart_data,
                            'categories' => $revenue_chart_categories
                        ],
                        'plans' => [
                            'series' => $plan_pie_series,
                            'labels' => $plan_pie_labels
                        ]
                    ]
                ]
            ]);
        }
        return view('frontend.superadmin.index');
    }

    public function organization_structure()
    {
        return view('frontend.admin.structure.organization_structure');
    }

    public function create_organization_node(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:department,team',
                'parent_id' => 'nullable',
                'parent_type' => 'nullable|in:organization,department,team',
            ]);

            $userId = Auth::id();
            $name = $request->input('name');
            $type = $request->input('type');
            $parentId = $request->input('parent_id');
            $parentType = $request->input('parent_type');

            if ($type === 'department') {
                // Check if department with same name exists
                $existingDept = Department::where('box_id', $box->id)
                    ->where('name', $name)
                    ->first();

                if ($existingDept) {
                    // If exists, create a team under it instead
                    $newTeam = new Team([
                        'department_id' => $existingDept->id,
                        'box_id' => $box->id,
                        'name' => $name . ' Team',
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                    $newTeam->saveAsRoot();
                    $newTeam->refresh();

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Department already exists. Created team under it.',
                        'data' => [
                            'id' => 'team_' . $newTeam->id,
                            'name' => $newTeam->name,
                            'parent' => 'dept_' . $existingDept->id,
                            'type' => 'team',
                            'original_id' => $newTeam->id,
                        ],
                    ]);
                }

                // Create new department
                $department = Department::create([
                    'name'               => $name,
                    'box_id'             => $box->id,
                    'user_id'            => $userId,
                    'show_into_leads'    => $request->boolean('show_into_leads'),
                    'show_into_accounts' => $request->boolean('show_into_accounts'),
                    'created_by'         => $userId,
                    'updated_by'         => $userId,
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Department created successfully.',
                    'data' => [
                        'id' => 'dept_' . $department->id,
                        'name' => $department->name,
                        'parent' => 0,
                        'type' => 'department',
                        'original_id' => $department->id,
                    ],
                ]);
            } else {
                // Creating a team
                $departmentId = null;
                $parentTeam = null;

                if ($parentType === 'department' && $parentId) {
                    $deptId = str_replace('dept_', '', $parentId);
                    $departmentId = $deptId;
                } elseif ($parentType === 'team' && $parentId) {
                    $teamId = str_replace('team_', '', $parentId);
                    $parentTeam = Team::where('box_id', $box->id)->find($teamId);
                    if ($parentTeam) {
                        $departmentId = $parentTeam->department_id;
                    }
                }

                if (!$departmentId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parent specified.',
                    ], 400);
                }

                // Check if team with same name exists in same location
                $query = Team::where('box_id', $box->id)
                    ->where('department_id', $departmentId)
                    ->where('name', $name);

                if ($parentTeam) {
                    $query->where('parent_id', $parentTeam->id);
                } else {
                    $query->whereIsRoot();
                }

                $existingTeam = $query->first();

                if ($existingTeam) {
                    // If exists, create as child of existing team
                    $newTeam = new Team([
                        'department_id' => $departmentId,
                        'box_id' => $box->id,
                        'name' => $name,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                    $newTeam->appendToNode($existingTeam)->save();
                    $newTeam->refresh();

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Team already exists. Created as child.',
                        'data' => [
                            'id' => 'team_' . $newTeam->id,
                            'name' => $newTeam->name,
                            'parent' => 'team_' . $existingTeam->id,
                            'type' => 'team',
                            'original_id' => $newTeam->id,
                        ],
                    ]);
                }

                // Create new team
                $team = new Team([
                    'department_id'      => $departmentId,
                    'box_id'             => $box->id,
                    'name'               => $name,
                    'show_into_leads'    => $request->boolean('show_into_leads'),
                    'show_into_accounts' => $request->boolean('show_into_accounts'),
                    'created_by'         => $userId,
                    'updated_by'         => $userId,
                ]);

                if ($parentTeam) {
                    $team->appendToNode($parentTeam)->save();
                } else {
                    $team->saveAsRoot();
                }
                $team->refresh();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Team created successfully.',
                    'data' => [
                        'id' => 'team_' . $team->id,
                        'name' => $team->name,
                        'parent' => $parentTeam ? 'team_' . $parentTeam->id : 'dept_' . $departmentId,
                        'type' => 'team',
                        'original_id' => $team->id,
                    ],
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error creating organization node: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create node.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function create_sequential_child(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            $request->validate([
                'parent_id' => 'required',
                'parent_type' => 'required|in:organization,department,team',
            ]);

            $userId = Auth::id();
            $parentId = $request->input('parent_id');
            $parentType = $request->input('parent_type');

            // Skip organization root
            if ($parentType === 'organization' || $parentId == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create child under organization root.',
                ], 400);
            }

            $parentName = '';
            $departmentId = null;
            $parentTeam = null;

            if ($parentType === 'department') {
                $deptId = str_replace('dept_', '', $parentId);
                $department = Department::where('box_id', $box->id)->find($deptId);
                if (!$department) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent department not found.',
                    ], 404);
                }
                $parentName = $department->name;
                $departmentId = $deptId;
            } elseif ($parentType === 'team') {
                $teamId = str_replace('team_', '', $parentId);
                $parentTeam = Team::where('box_id', $box->id)->find($teamId);
                if (!$parentTeam) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent team not found.',
                    ], 404);
                }
                $parentName = $parentTeam->name;
                $departmentId = $parentTeam->department_id;
            }

            // Generate sequential child name
            $childName = $this->generateSequentialChildName($parentName, $departmentId, $parentTeam, $box->id);

            // Create the team
            $team = new Team([
                'department_id' => $departmentId,
                'box_id' => $box->id,
                'name' => $childName,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            if ($parentTeam) {
                $team->appendToNode($parentTeam)->save();
            } else {
                $team->saveAsRoot();
            }
            $team->refresh();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Child node created successfully.',
                'data' => [
                    'id' => 'team_' . $team->id,
                    'name' => $team->name,
                    'parent' => $parentId,
                    'type' => 'team',
                    'original_id' => $team->id,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error creating sequential child: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create child node.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function generateSequentialChildName($parentName, $departmentId, $parentTeam, $boxId)
    {
        // Get all existing children of the parent
        if ($parentTeam) {
            // Get direct children of this team
            $children = $parentTeam->children()->get();
        } else {
            // Get root teams under this department
            $children = Team::where('box_id', $boxId)
                ->where('department_id', $departmentId)
                ->whereIsRoot()
                ->get();
        }

        // Extract numbers from existing children names
        // Pattern: parentName + number (e.g., A1, A2, A11, A21, etc.)
        $numbers = [];
        $parentNameEscaped = preg_quote($parentName, '/');
        
        foreach ($children as $child) {
            $childName = $child->name;
            // Match pattern: parentName followed by digits (exact match)
            // Examples: 
            //   Parent "A" → matches "A1", "A2", "A3" (not "A11", "A21")
            //   Parent "A1" → matches "A11", "A12", "A13" (not "A1", "A111")
            //   Parent "A2" → matches "A21", "A22", "A23"
            //   Parent "A21" → matches "A211", "A212", "A213"
            if (preg_match('/^' . $parentNameEscaped . '(\d+)$/', $childName, $matches)) {
                $numbers[] = (int)$matches[1];
            }
        }

        // Find next available number
        if (empty($numbers)) {
            $nextNumber = 1;
        } else {
            $nextNumber = max($numbers) + 1;
        }

        // Generate child name: parentName + nextNumber
        return $parentName . $nextNumber;
    }

    public function update_organization_node_name(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            $request->validate([
                'node_id' => 'required',
                'name' => 'required|string|max:255',
            ]);

            $userId = Auth::id();
            $nodeId = $request->input('node_id');
            $name = $request->input('name');

            if (strpos($nodeId, 'dept_') === 0) {
                $deptId = str_replace('dept_', '', $nodeId);
                $department = Department::where('box_id', $box->id)->find($deptId);
                if ($department) {
                    $department->update([
                        'name'               => $name,
                        'show_into_leads'    => $request->boolean('show_into_leads'),
                        'show_into_accounts' => $request->boolean('show_into_accounts'),
                        'updated_by'         => $userId,
                    ]);
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Department name updated successfully.',
                    ]);
                }
            } elseif (strpos($nodeId, 'team_') === 0) {
                $teamId = str_replace('team_', '', $nodeId);
                $team = Team::where('box_id', $box->id)->find($teamId);
                if ($team) {
                    $team->update([
                        'name'               => $name,
                        'show_into_leads'    => $request->boolean('show_into_leads'),
                        'show_into_accounts' => $request->boolean('show_into_accounts'),
                        'updated_by'         => $userId,
                    ]);
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Team name updated successfully.',
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Node not found.',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error updating node name: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update node name.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function get_organization_structure(Request $request, $box_slug)
    {
        try {
            // Get box
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            // Fetch departments for this box
            $departments = Department::where('box_id', $box->id)
                ->defaultOrder()
                ->get();

            // Build organization structure data
            $orgData = [];
            
            // Add root organization node
            $orgData[] = [
                'id' => 0,
                'name' => $box->name ?? 'Organization',
                'parent' => 0,
                'type' => 'organization'
            ];

            // Add departments
            foreach ($departments as $dept) {
                $orgData[] = [
                    'id'                 => 'dept_' . $dept->id,
                    'name'               => $dept->name,
                    'parent'             => 0, // All departments are under organization
                    'type'               => 'department',
                    'original_id'        => $dept->id,
                    'show_into_leads'    => $dept->show_into_leads,
                    'show_into_accounts' => $dept->show_into_accounts,
                ];

                // Fetch teams for this department (only root teams)
                $rootTeams = Team::where('department_id', $dept->id)
                    ->whereIsRoot()
                    ->defaultOrder()
                    ->get();

                // Convert nested teams to flat structure
                $this->addTeamsToOrgData($rootTeams, 'dept_' . $dept->id, $orgData);
            }

            return response()->json([
                'success' => true,
                'data' => $orgData,
            ], 200);

        } catch (\Throwable $th) {
            \Log::error('Error fetching organization structure: ' . $th->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch organization structure.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function addTeamsToOrgData($teams, $parentId, &$orgData)
    {
        foreach ($teams as $team) {
            // Reload team with children relationship
            $teamWithChildren = Team::with('children')->find($team->id);
            
            $orgData[] = [
                'id'                 => 'team_' . $team->id,
                'name'               => $team->name,
                'parent'             => $parentId,
                'type'               => 'team',
                'original_id'        => $team->id,
                'department_id'      => $team->department_id,
                'show_into_leads'    => $team->show_into_leads,
                'show_into_accounts' => $team->show_into_accounts,
            ];

            // Recursively add child teams
            if ($teamWithChildren && $teamWithChildren->children && $teamWithChildren->children->count() > 0) {
                $this->addTeamsToOrgData($teamWithChildren->children, 'team_' . $team->id, $orgData);
            }
        }
    }

    public function update_organization_structure(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            // Get box
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Box not found.',
                ], 404);
            }

            // Validate the request
            $request->validate([
                'structure' => 'required|array',
                'structure.*.id' => 'required',
                'structure.*.name' => 'required|string',
                'structure.*.parent' => 'required',
            ]);

            $structure = $request->input('structure');
            $userId = Auth::id();

            // Process each node in the structure
            foreach ($structure as $node) {
                $nodeId = $node['id'];
                $parentId = $node['parent'];
                $nodeType = $node['type'] ?? 'team';

                // Skip root organization node
                if ($nodeId == 0 || $nodeType === 'organization') {
                    continue;
                }

                // Handle departments
                if (strpos($nodeId, 'dept_') === 0) {
                    $deptId = str_replace('dept_', '', $nodeId);
                    // Departments stay at root level (parent = 0)
                    // Just update if needed
                    continue;
                }

                // Handle teams
                if (strpos($nodeId, 'team_') === 0) {
                    $teamId = str_replace('team_', '', $nodeId);
                    $team = Team::where('box_id', $box->id)->find($teamId);
                    
                    if (!$team) {
                        continue;
                    }

                    // Get current parent from database
                    $currentParent = $team->parent_id ? 'team_' . $team->parent_id : 
                                    ($team->department_id ? 'dept_' . $team->department_id : 0);
                    
                    // Only update if parent changed
                    if ($currentParent != $parentId) {
                        // Determine new parent
                        $newParentTeam = null;
                        $newDepartmentId = $team->department_id;

                        if ($parentId == 0) {
                            // Moving to root level - find department from structure
                            // Teams at root level should be under their department
                            $newDepartmentId = $team->department_id; // Keep same department
                            $team->saveAsRoot();
                        } elseif (strpos($parentId, 'dept_') === 0) {
                            // Parent is a department - move to root level under that department
                            $newDepartmentId = str_replace('dept_', '', $parentId);
                            $team->saveAsRoot();
                        } elseif (strpos($parentId, 'team_') === 0) {
                            // Parent is another team
                            $parentTeamId = str_replace('team_', '', $parentId);
                            $newParentTeam = Team::where('box_id', $box->id)->find($parentTeamId);
                            
                            if ($newParentTeam) {
                                $newDepartmentId = $newParentTeam->department_id;
                                
                                // Move team to new parent using nested set
                                $team->appendToNode($newParentTeam)->save();
                            } else {
                                continue; // Skip if parent team not found
                            }
                        }

                        // Update department if changed
                        if ($newDepartmentId && $newDepartmentId != $team->department_id) {
                            $team->department_id = $newDepartmentId;
                        }
                        
                        $team->updated_by = $userId;
                        $team->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Organization structure updated successfully.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error updating organization structure: ' . $th->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update organization structure.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function company()
    {
        return view('frontend.superadmin.company');
    }

    // public function organization_structure()
    // {
    //     return view('frontend.superadmin.organization_structure');
    // }

    public function get_company(Request $request)
    {
        try {
            $query = Box::with('country', 'state', 'city', 'superadmin');
            if ($request->has('country_id') && is_numeric($request->country_id)) {
                $query->where('country_id', $request->country_id);
            }
            if ($request->has('state_id') && is_numeric($request->state_id)) {
                $query->where('state_id', $request->state_id);
            }
            if ($request->has('city_id') && is_numeric($request->city_id)) {
                $query->where('city_id', $request->city_id);
            }

            if ($request->has('country') && is_array($request->country)) {
                $query->whereIn('country', $request->country);
            }

            if ($request->has('owner') && is_array($request->owner)) {
                $query->whereIn('owner', $request->owner);
            }

            if ($request->has('status') && is_array($request->status)) {
                $query->whereIn('status', $request->status);
            }

            if ($request->has('rating') && is_array($request->rating)) {
                $query->whereIn('rating', $request->rating);
            }

            if ($request->has('tags') && is_array($request->tags)) {
                $query->where(function ($q) use ($request) {
                    foreach ($request->tags as $tag) {
                        $q->orWhere('tags', 'LIKE', '%' . $tag . '%');
                    }
                });
            }

            if ($request->has('sort_by')) {
                switch ($request->sort_by) {
                    case 'asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'desc':
                        $query->orderBy('name', 'desc');
                        break;
                    case 'recently_viewed':
                        $query->orderBy('last_viewed_at', 'desc');
                        break;
                    case 'recently_added':
                        $query->orderBy('created_at', 'desc');
                        break;
                }
            } else {
                $query->orderBy('id', 'desc');
            }

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch company data.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function subscription(\Illuminate\Http\Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\UserSubscription::with(['user', 'plan'])->select('user_subscriptions.*');
            
            if ($request->has('sort') && !empty($request->sort)) {
                if ($request->sort == 'Ascending') {
                    $query->orderBy('created_at', 'asc');
                } elseif ($request->sort == 'Descending') {
                    $query->orderBy('created_at', 'desc');
                } elseif ($request->sort == 'Recently Viewed') {
                    $query->orderBy('updated_at', 'desc');
                } elseif ($request->sort == 'Recently Added') {
                    $query->orderBy('created_at', 'desc');
                }
            }

            if ($request->has('date_range') && !empty($request->date_range)) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                        $end = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('created_at', [$start, $end]);
                    } catch (\Exception $e) {
                        try {
                            $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                            $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                            $query->whereBetween('created_at', [$start, $end]);
                        } catch (\Exception $e) {
                            // ignore if format fails
                        }
                    }
                }
            }
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhere('payment_method', 'like', "%{$search}%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('plan', function($pq) use ($search) {
                          $pq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('box', function($bq) use ($search) {
                          $bq->where('name', 'like', "%{$search}%")
                             ->orWhere('slug', 'like', "%{$search}%");
                      });
                });
            }
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('select', function($row) {
                    return '<label class="checkboxs"><input type="checkbox" name="ids[]" value="'.$row->id.'"><span class="checkmarks"></span></label>';
                })
                ->addColumn('star', function($row) {
                    return '<div class="set-star rating-select"><i class="fa fa-star"></i></div>';
                })
                ->addColumn('subscriber', function($row) {
                    $boxName = $row->user->name ?? 'Unknown';
                    if ($row->box_id) {
                        $box = \App\Models\Box::find($row->box_id);
                        if ($box) $boxName = $box->name;
                    }
                    $icon = asset('assets/img/icons/company-icon-01.svg');
                    return '<div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                    <img class="w-auto h-auto" src="'.$icon.'" alt="User Image">
                                </a>
                                <a href="javascript:void(0);" class="d-flex flex-column fw-medium">'.$boxName.'</a>
                            </div>';
                })
                ->addColumn('plan', function($row) {
                    $name = $row->plan->name ?? '-';
                    $type = ucfirst($row->plan->type ?? 'Monthly');
                    return $name . ' (' . $type . ')';
                })
                ->addColumn('billing_cycle', function($row) {
                    return ($row->plan && $row->plan->type == 'yearly') ? '365 Days' : '30 Days';
                })
                ->addColumn('payment_method', function($row) {
                    return $row->payment_method ? $row->payment_method : 'Razorpay';
                })
                ->addColumn('amount', function($row) {
                    $currency = (isset($row->plan->currency) && $row->plan->currency == 'INR') ? '₹ ' : '$ ';
                    $price = number_format($row->plan->price ?? 0);
                    return $currency . $price;
                })
                ->editColumn('starts_at', function($row) {
                    return $row->starts_at ? $row->starts_at->format('d M Y') : '-';
                })
                ->editColumn('ends_at', function($row) {
                    return $row->ends_at ? $row->ends_at->format('d M Y') : '-';
                })
                ->editColumn('status', function($row) {
                    if ($row->status == 'active') {
                        return '<span class="badge badge-pill badge-status bg-success">Active</span>';
                    } elseif ($row->status == 'cancelled') {
                        return '<span class="badge badge-pill badge-status bg-warning text-dark">Cancelled</span>';
                    } else {
                        return '<span class="badge badge-pill badge-status bg-danger">'.ucfirst($row->status).'</span>';
                    }
                })
                ->addColumn('action', function($row) {
                    return '<div class="dropdown table-action">
                                <a href="#" class="action-icon " data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item btn-preview" href="javascript:void(0);" data-id="' . $row->id . '">
                                        <i class="ti ti-eye text-blue-light"></i> Preview
                                    </a>
                                    <a class="dropdown-item btn-edit-status" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $row->status . '">
                                        <i class="ti ti-edit text-success"></i> Edit Status
                                    </a>
                                </div>
                            </div>';
                })
                ->rawColumns(['select', 'star', 'subscriber', 'status', 'action'])
                ->make(true);
        }

        // Initial page load metrics for 5 cards
        $totalSubscribers = \App\Models\UserSubscription::count();
        $activeSubscribers = \App\Models\UserSubscription::where('status', 'active')->count();
        $inactiveSubscribers = \App\Models\UserSubscription::whereNotIn('status', ['active', 'cancelled'])->count();
        $cancelledSubscribers = \App\Models\UserSubscription::where('status', 'cancelled')->count();
        $overdueSubscribers = \App\Models\UserSubscription::where('status', 'overdue')->orWhere('ends_at', '<', now())->count();
        
        // Total Paid Amount (Overall)
        $paidAmount = \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')->sum('plans.price');

        // Online Paid Amount
        $onlineAmount = \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')
            ->where(function($q) { $q->whereNull('user_subscriptions.payment_method')->orWhere('user_subscriptions.payment_method', '!=', 'Offline'); })
            ->sum('plans.price');

        // Offline Paid Amount
        $offlineAmount = \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')
            ->where('user_subscriptions.payment_method', 'Offline')
            ->sum('plans.price');

        // Weekly percentage changes
        $now = now();
        $oneWeekAgo = now()->subWeek();
        $twoWeeksAgo = now()->subWeeks(2);

        $calcPercent = function($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 2);
        };

        // Total
        $totalSubscribersPercent = $calcPercent(
            \App\Models\UserSubscription::whereBetween('created_at', [$oneWeekAgo, $now])->count(),
            \App\Models\UserSubscription::whereBetween('created_at', [$twoWeeksAgo, $oneWeekAgo])->count()
        );

        // Active
        $activeSubscribersPercent = $calcPercent(
            \App\Models\UserSubscription::where('status', 'active')->whereBetween('created_at', [$oneWeekAgo, $now])->count(),
            \App\Models\UserSubscription::where('status', 'active')->whereBetween('created_at', [$twoWeeksAgo, $oneWeekAgo])->count()
        );

        // Inactive
        $inactiveSubscribersPercent = $calcPercent(
            \App\Models\UserSubscription::whereNotIn('status', ['active', 'cancelled'])->whereBetween('created_at', [$oneWeekAgo, $now])->count(),
            \App\Models\UserSubscription::whereNotIn('status', ['active', 'cancelled'])->whereBetween('created_at', [$twoWeeksAgo, $oneWeekAgo])->count()
        );

        // Cancelled
        $cancelledSubscribersPercent = $calcPercent(
            \App\Models\UserSubscription::where('status', 'cancelled')->whereBetween('created_at', [$oneWeekAgo, $now])->count(),
            \App\Models\UserSubscription::where('status', 'cancelled')->whereBetween('created_at', [$twoWeeksAgo, $oneWeekAgo])->count()
        );

        // Overdue (we approximate the time range based on ends_at)
        $overdueSubscribersPercent = $calcPercent(
            \App\Models\UserSubscription::where(function($q) { $q->where('status', 'overdue')->orWhere('ends_at', '<', now()); })->whereBetween('created_at', [$oneWeekAgo, $now])->count(),
            \App\Models\UserSubscription::where(function($q) { $q->where('status', 'overdue')->orWhere('ends_at', '<', now()); })->whereBetween('created_at', [$twoWeeksAgo, $oneWeekAgo])->count()
        );

        // Total Amount Percent
        $paidAmountPercent = $calcPercent(
            \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')->whereBetween('user_subscriptions.created_at', [$oneWeekAgo, $now])->sum('plans.price'),
            \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')->whereBetween('user_subscriptions.created_at', [$twoWeeksAgo, $oneWeekAgo])->sum('plans.price')
        );

        // Online Amount Percent
        $onlineAmountPercent = $calcPercent(
            \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')
                ->where(function($q) { $q->whereNull('user_subscriptions.payment_method')->orWhere('user_subscriptions.payment_method', '!=', 'Offline'); })
                ->whereBetween('user_subscriptions.created_at', [$oneWeekAgo, $now])->sum('plans.price'),
            \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')
                ->where(function($q) { $q->whereNull('user_subscriptions.payment_method')->orWhere('user_subscriptions.payment_method', '!=', 'Offline'); })
                ->whereBetween('user_subscriptions.created_at', [$twoWeeksAgo, $oneWeekAgo])->sum('plans.price')
        );

        // Offline Amount Percent
        $offlineAmountPercent = $calcPercent(
            \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')
                ->where('user_subscriptions.payment_method', 'Offline')
                ->whereBetween('user_subscriptions.created_at', [$oneWeekAgo, $now])->sum('plans.price'),
            \App\Models\UserSubscription::join('plans', 'user_subscriptions.plan_id', '=', 'plans.id')
                ->where('user_subscriptions.payment_method', 'Offline')
                ->whereBetween('user_subscriptions.created_at', [$twoWeeksAgo, $oneWeekAgo])->sum('plans.price')
        );

        return view('frontend.superadmin.subscription', compact(
            'totalSubscribers', 'activeSubscribers', 'inactiveSubscribers', 'cancelledSubscribers', 'overdueSubscribers', 'paidAmount', 'onlineAmount', 'offlineAmount',
            'totalSubscribersPercent', 'activeSubscribersPercent', 'inactiveSubscribersPercent', 'cancelledSubscribersPercent', 'overdueSubscribersPercent', 'paidAmountPercent', 'onlineAmountPercent', 'offlineAmountPercent'
        ));
    }

    public function packages(Request $request, $box_slug)
    {
        $plans = Plan::all();
        $masterFeatures = PackageFeature::where('is_active', true)->get();
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'plans' => $plans,
                'masterFeatures' => $masterFeatures
            ]);
        }
        return view('frontend.superadmin.packages', compact('plans', 'masterFeatures'));
    }

    public function store_package(Request $request, $box_slug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:monthly,yearly',
            'price' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'payment_mode' => 'required|in:online,offline',
            'plan_type' => 'required|in:subscription,topup,whatsapp',
            'status' => 'required|in:active,inactive',
            'trial_period_days' => 'nullable|integer|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'marketing_msg_cost' => 'nullable|string|max:255',
            'utility_msg_cost' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'discount_badge' => 'nullable|string|max:255',
            'savings_text' => 'nullable|string|max:255',
            'original_price' => 'nullable|numeric',
            'billing_cycle' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->name . '-' . $request->type);
        if (Plan::where('slug', $slug)->exists()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['name' => ['A plan with this name and type already exists.']]], 422);
            }
            return redirect()->back()->withErrors(['name' => 'A plan with this name and type already exists.'])->withInput();
        }
        
        $plan = Plan::create([
            'name' => $request->name,
            'slug' => $slug,
            'type' => strtolower($request->type),
            'price' => $request->price,
            'currency' => $request->currency,
            'payment_mode' => $request->payment_mode,
            'plan_type' => $request->plan_type,
            'interval_count' => 1,
            'description' => $request->description,
            'status' => strtolower($request->status),
            'features' => $request->features ?: [],
            'trial_period_days' => (int) $request->input('trial_period_days', 0),
            'grace_period_days' => (int) $request->input('grace_period_days', 0),
            'marketing_msg_cost' => $request->marketing_msg_cost,
            'utility_msg_cost' => $request->utility_msg_cost,
            'button_text' => $request->button_text ?: 'Get Offer Now',
            'discount_badge' => $request->discount_badge,
            'savings_text' => $request->savings_text,
            'original_price' => $request->original_price,
            'billing_cycle' => $request->billing_cycle,
        ]);

        // Create Razorpay Plan
        try {
            $razorPlan = $this->razorpayService->createRazorpayPlan($plan);
            $plan->razorpay_plan_id = $razorPlan['id'];
            $plan->save();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to create Razorpay plan for {$plan->name}: " . $e->getMessage());
            $request->session()->flash('warning', 'Plan created locally but failed to sync with Razorpay: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Plan created successfully', 'plan' => $plan]);
        }

        return redirect()->back()->with('success', 'Plan created successfully');
    }

    public function update_package(Request $request, $box_slug, $id)
    {
        $plan = Plan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:monthly,yearly',
            'price' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'payment_mode' => 'required|in:online,offline',
            'plan_type' => 'required|in:subscription,topup,whatsapp',
            'status' => 'required|in:active,inactive',
            'trial_period_days' => 'nullable|integer|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'marketing_msg_cost' => 'nullable|string|max:255',
            'utility_msg_cost' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'discount_badge' => 'nullable|string|max:255',
            'savings_text' => 'nullable|string|max:255',
            'original_price' => 'nullable|numeric',
            'billing_cycle' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->name . '-' . $request->type);
        if (Plan::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['name' => ['A plan with this name and type already exists.']]], 422);
            }
            return redirect()->back()->withErrors(['name' => 'A plan with this name and type already exists.'])->withInput();
        }
        
        $plan->update([
            'name' => $request->name,
            'slug' => $slug,
            'type' => strtolower($request->type),
            'price' => $request->price,
            'currency' => $request->currency,
            'payment_mode' => $request->payment_mode,
            'plan_type' => $request->plan_type,
            'description' => $request->description,
            'status' => strtolower($request->status),
            'features' => $request->features ?: [],
            'trial_period_days' => (int) $request->input('trial_period_days', 0),
            'grace_period_days' => (int) $request->input('grace_period_days', 0),
            'marketing_msg_cost' => $request->marketing_msg_cost,
            'utility_msg_cost' => $request->utility_msg_cost,
            'button_text' => $request->button_text ?: 'Get Offer Now',
            'discount_badge' => $request->discount_badge,
            'savings_text' => $request->savings_text,
            'original_price' => $request->original_price,
            'billing_cycle' => $request->billing_cycle,
        ]);

        // Update or Re-create Razorpay Plan if amount/type changed
        try {
            if (!$plan->razorpay_plan_id || $plan->wasChanged('price') || $plan->wasChanged('type')) {
                $razorPlan = $this->razorpayService->createRazorpayPlan($plan);
                $plan->razorpay_plan_id = $razorPlan['id'];
                $plan->save();
            } else {
                $this->razorpayService->updateRazorpayPlan($plan);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to update Razorpay plan for {$plan->name}: " . $e->getMessage());
            $request->session()->flash('warning', 'Plan updated locally but failed to sync with Razorpay: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Plan updated successfully', 'plan' => $plan]);
        }

        return redirect()->back()->with('success', 'Plan updated successfully');
    }

    public function destroy_package(Request $request, $box_slug, $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Plan deleted successfully']);
        }

        return redirect()->back()->with('success', 'Plan deleted successfully');
    }

    public function bulk_destroy_package(Request $request, $box_slug)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:plans,id'
        ]);

        Plan::whereIn('id', $request->ids)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Selected plans deleted successfully']);
        }

        return redirect()->back()->with('success', 'Selected plans deleted successfully');
    }
    public function coupons(Request $request, $box_slug)
    {
        $coupons = Coupon::all();
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'coupons' => $coupons
            ]);
        }
        return view('frontend.superadmin.coupons', compact('coupons'));
    }

    public function store_coupon(Request $request, $box_slug)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code',
            'user_type' => 'required|in:all,new',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'valid_until' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $coupon = Coupon::create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon created successfully!', 'coupon' => $coupon]);
        }

        return redirect()->back()->with('success', 'Coupon created successfully!');
    }

    public function update_coupon(Request $request, $box_slug, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $id,
            'user_type' => 'required|in:all,new',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'valid_until' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $coupon->update($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon updated successfully!', 'coupon' => $coupon]);
        }

        return redirect()->back()->with('success', 'Coupon updated successfully!');
    }

    public function destroy_coupon(Request $request, $box_slug, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon deleted successfully']);
        }

        return redirect()->back()->with('success', 'Coupon deleted successfully');
    }

    public function package_features_page(Request $request, $box_slug)
    {
        $query = PackageFeature::query();
        if ($request->has('type') && in_array($request->type, ['crm', 'whatsapp'])) {
            $query->where('feature_type', $request->type);
        }
        $features = $query->orderBy('name', 'asc')->get();
        if ($request->ajax()) {
            return response()->json(['success' => true, 'features' => $features]);
        }
        return view('frontend.superadmin.package_features', compact('features', 'box_slug'));
    }

    public function toggle_feature_status(Request $request, $box_slug, $id)
    {
        $feature = PackageFeature::findOrFail($id);
        $feature->is_active = !$feature->is_active;
        $feature->save();

        return response()->json([
            'success' => true,
            'message' => 'Feature status updated successfully',
            'is_active' => $feature->is_active
        ]);
    }

    public function get_master_features(Request $request)
    {
        $features = PackageFeature::all();
        return response()->json(['success' => true, 'features' => $features]);
    }

    public function store_master_feature(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:package_features,name',
            'feature_type' => 'required|in:crm,whatsapp',
        ]);

        $feature = PackageFeature::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'feature_type' => $request->feature_type,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Feature added successfully', 'feature' => $feature]);
    }

    public function update_master_feature(Request $request, $box_slug, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:package_features,name,' . $id,
        ]);

        $feature = PackageFeature::findOrFail($id);
        $feature->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active') ? $request->is_active : $feature->is_active,
        ]);

        return response()->json(['success' => true, 'message' => 'Feature updated successfully', 'feature' => $feature]);
    }

    public function destroy_master_feature(Request $request, $box_slug, $id)
    {
        $feature = PackageFeature::findOrFail($id);
        $feature->delete();

        return response()->json(['success' => true, 'message' => 'Feature deleted successfully']);
    }

    public function domain()
    {
        return view('frontend.superadmin.domain');
    }

    public function purchase_transaction(\Illuminate\Http\Request $request)
    {
        $invoices = \Illuminate\Support\Facades\Cache::remember('superadmin_razorpay_invoices', 300, function () {
            try {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $invoicesResult = $api->invoice->all(['count' => 100]);
                return $invoicesResult['items'] ?? [];
            } catch (\Exception $e) {
                \Log::error('Razorpay invoices fetch failed: ' . $e->getMessage());
                return [];
            }
        });

        if ($request->ajax()) {
            $collection = collect($invoices);
            
            if ($request->has('date_range') && !empty($request->date_range)) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay()->timestamp;
                        $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay()->timestamp;
                        $collection = $collection->filter(function($item) use ($start, $end) {
                            $itemArr = is_array($item) ? $item : (method_exists($item, 'toArray') ? $item->toArray() : (array)$item);
                            $created_at = $itemArr['created_at'] ?? 0;
                            return $created_at >= $start && $created_at <= $end;
                        });
                    } catch (\Exception $e) {
                        try {
                            $start = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay()->timestamp;
                            $end = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay()->timestamp;
                            $collection = $collection->filter(function($item) use ($start, $end) {
                                $itemArr = is_array($item) ? $item : (method_exists($item, 'toArray') ? $item->toArray() : (array)$item);
                                $created_at = $itemArr['created_at'] ?? 0;
                                return $created_at >= $start && $created_at <= $end;
                            });
                        } catch (\Exception $e) { }
                    }
                }
            }

            if ($request->has('sort') && !empty($request->sort)) {
                if ($request->sort == 'Ascending') {
                    $collection = $collection->sortBy('created_at');
                } elseif ($request->sort == 'Descending' || $request->sort == 'Recently Added') {
                    $collection = $collection->sortByDesc('created_at');
                }
            }
            
            $emails = collect($collection)->map(function ($invoice) {
                $inv = is_array($invoice) ? $invoice : (method_exists($invoice, 'toArray') ? $invoice->toArray() : (array)$invoice);
                return $inv['customer_details']['email'] ?? null;
            })->filter()->unique()->toArray();

            $usersByEmail = \App\Models\User::whereIn('email', $emails)->with('boxes')->get()->keyBy('email');

            return \Yajra\DataTables\Facades\DataTables::of($collection)
                ->addColumn('select', function($row) {
                    return '<label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label>';
                })
                ->addColumn('company_name', function($row) use ($usersByEmail) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    $email = $rowArray['customer_details']['email'] ?? null;
                    
                    $name = $rowArray['customer_details']['name'] ?? 'Razorpay Customer';
                    if ($email && isset($usersByEmail[$email])) {
                        $box = $usersByEmail[$email]->boxes->first();
                        if ($box) {
                            $name = $box->name;
                        }
                    }

                    $icon = asset('assets/img/icons/company-icon-01.svg');
                    return '<div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                    <img class="w-auto h-auto" src="'.$icon.'" alt="User Image">
                                </a>
                                <a href="javascript:void(0);" class="d-flex flex-column fw-medium">'.$name.'</a>
                            </div>';
                })
                ->addColumn('company_email', function($row) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    return $rowArray['customer_details']['email'] ?? '-';
                })
                ->addColumn('box_slug', function($row) use ($usersByEmail) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    $email = $rowArray['customer_details']['email'] ?? null;
                    
                    if ($email && isset($usersByEmail[$email])) {
                        $box = $usersByEmail[$email]->boxes->first();
                        if ($box) {
                            return $box->slug;
                        }
                    }
                    return $rowArray['notes']['box_slug'] ?? $rowArray['notes']['tenant_id'] ?? '-';
                })
                ->addColumn('transaction_name', function($row) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    $desc = $rowArray['description'] ?? 'Subscription Invoice';
                    return '<a href="javascript:void(0);" class="text-primary">'.$desc.'</a>';
                })
                ->addColumn('document_number', function($row) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    return $rowArray['receipt'] ?? $rowArray['id'] ?? '-';
                })
                ->addColumn('goods_status', function($row) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    $status = $rowArray['status'] ?? '';
                    if ($status == 'paid') {
                        return '<span class="badge badge-pill badge-status bg-success">Paid</span>';
                    } elseif ($status == 'issued') {
                        return '<span class="badge badge-pill badge-status bg-warning text-dark">Issued</span>';
                    } else {
                        return '<span class="badge badge-pill badge-status bg-danger">'.ucfirst($status ?: 'Unknown').'</span>';
                    }
                })
                ->addColumn('amount', function($row) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    $currency = (isset($rowArray['currency']) && $rowArray['currency'] !== 'INR') ? '$' : '₹';
                    $amount = number_format(($rowArray['amount'] ?? 0) / 100, 2);
                    return $currency . $amount;
                })
                ->addColumn('created_date', function($row) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    return isset($rowArray['created_at']) ? date('d M Y', $rowArray['created_at']) : '-';
                })
                ->addColumn('action', function($row) {
                    $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);
                    $url = $rowArray['short_url'] ?? '';
                    $html = '<div class="dropdown table-action">
                                <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">';
                    if ($url) {
                        $html .= '<a class="dropdown-item" href="'.$url.'" target="_blank">
                                    <i class="ti ti-eye text-info"></i>View Invoice
                                  </a>';
                    }
                    $html .= '  </div>
                            </div>';
                    return $html;
                })
                ->rawColumns(['select', 'company_name', 'transaction_name', 'goods_status', 'action'])
                ->make(true);
        }

        $totalInvoices = count($invoices);

        return view('frontend.superadmin.purchase_transaction', compact('totalInvoices'));
    }

    public function realtime_logs()
    {
        return view('frontend.superadmin.realtime_logs');
    }

    public function get_realtime_logs_data(\Illuminate\Http\Request $request)
    {
        if ($request->ajax()) {
            $crmDb = \Illuminate\Support\Facades\DB::connection('mysql')->getDatabaseName();

            $logs = \App\Models\AttendanceRealtimeLogs::query()
                ->select(
                    \DB::raw('MAX(realtime_logs.id) as id'),
                    'realtime_logs.device_id',
                    'realtime_logs.user_id',
                    \DB::raw('LEFT(realtime_logs.io_time, 8) as punch_date'),
                    \DB::raw('COUNT(realtime_logs.id) as punch_count'),
                    \DB::raw('MAX(bu.first_name) as first_name'),
                    \DB::raw('MAX(bu.last_name) as last_name'),
                    \DB::raw('MAX(b.name) as box_name'),
                    \DB::raw('MAX(bu.employee_id) as employee_id'),
                    \DB::raw('MAX(bu.phone) as phone'),
                    \DB::raw('MAX(u.email) as email'),
                    \DB::raw('MAX(bm.machine_name) as machine_name')
                )
                ->leftJoin("{$crmDb}.biometric_machines as bm", 'realtime_logs.device_id', '=', 'bm.serial_no')
                ->leftJoin("{$crmDb}.user_machine_mappings as umm", function($join) {
                    $join->on('realtime_logs.user_id', '=', 'umm.machine_user_id')
                         ->on('bm.id', '=', 'umm.biometric_machine_id');
                })
                ->leftJoin("{$crmDb}.box_users as bu", 'umm.box_user_id', '=', 'bu.id')
                ->leftJoin("{$crmDb}.users as u", 'bu.user_id', '=', 'u.id')
                ->leftJoin("{$crmDb}.boxes as b", 'umm.box_id', '=', 'b.id')
                ->groupBy('realtime_logs.device_id', 'realtime_logs.user_id', \DB::raw('LEFT(realtime_logs.io_time, 8)'));

            return \Yajra\DataTables\Facades\DataTables::of($logs)
                ->addColumn('full_name', function ($row) {
                    if ($row->first_name || $row->last_name) {
                        return trim($row->first_name . ' ' . $row->last_name);
                    }
                    return '-';
                })
                ->addColumn('punch_date_formatted', function($row) {
                    if(strlen($row->punch_date) == 8) {
                        return substr($row->punch_date, 0, 4) . '-' . substr($row->punch_date, 4, 2) . '-' . substr($row->punch_date, 6, 2);
                    }
                    return $row->punch_date;
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary view-punches" data-device="' . $row->device_id . '" data-user="' . $row->user_id . '" data-date="' . $row->punch_date . '" data-name="'. trim($row->first_name . ' ' . $row->last_name) .'"><i class="ti ti-eye"></i> View</button>';
                })
                ->filterColumn('full_name', function($query, $keyword) {
                    $query->havingRaw("MAX(CONCAT(bu.first_name, ' ', bu.last_name)) LIKE ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return response()->json(['error' => 'Unauthorized access'], 403);
    }

    public function realtime_logs_history(\Illuminate\Http\Request $request)
    {
        $deviceId = $request->device_id;
        $userId = $request->user_id;
        $date = $request->date; // YYYYMMDD string format
        
        $logs = \App\Models\AttendanceRealtimeLogs::where('device_id', $deviceId)
            ->where('user_id', $userId)
            ->where(\DB::raw('LEFT(io_time, 8)'), $date)
            ->orderBy('io_time', 'asc')
            ->get();
            
        return response()->json(['success' => true, 'data' => $logs]);
    }

    // OrgChart simple methods
    public function orgchart_data(Request $request, $box_slug)
    {
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json([], 404);
            }

            $departments = Department::where('box_id', $box->id)->defaultOrder()->get();
            $orgData = [];
            $idMap = []; // Map: numeric_id => ['type' => 'dept'|'team', 'original_id' => X]
            $nextId = 1;
            
            // Add root organization node
            $orgData[] = ['id' => 0, 'name' => $box->name ?? 'Organization', 'parent' => 0, '_type' => 'org', '_original_id' => 0];
            $idMap[0] = ['type' => 'org', 'original_id' => 0];
            
            // Add departments
            foreach ($departments as $dept) {
                $numericId = $nextId++;
                $orgData[] = ['id' => $numericId, 'name' => $dept->name, 'parent' => 0, '_type' => 'dept', '_original_id' => $dept->id];
                $idMap[$numericId] = ['type' => 'dept', 'original_id' => $dept->id];
                
                $teams = Team::where('department_id', $dept->id)->defaultOrder()->get();
                $this->addTeamsToOrgChartData($teams, $numericId, $orgData, $idMap, $nextId);
            }

            // Return data as simple array (matching user's pattern)
            return response()->json($orgData);
        } catch (\Throwable $th) {
            \Log::error('Error fetching orgchart data: ' . $th->getMessage());
            return response()->json([], 500);
        }
    }

    private function addTeamsToOrgChartData($teams, $parentNumericId, &$orgData, &$idMap, &$nextId)
    {
        foreach ($teams as $team) {
            $numericId = $nextId++;
            $orgData[] = ['id' => $numericId, 'name' => $team->name, 'parent' => $parentNumericId, '_type' => 'team', '_original_id' => $team->id];
            $idMap[$numericId] = ['type' => 'team', 'original_id' => $team->id];
            
            if ($team->children && $team->children->count() > 0) {
                $this->addTeamsToOrgChartData($team->children, $numericId, $orgData, $idMap, $nextId);
            }
        }
    }

    public function orgchart_add(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json(['success' => false], 404);
            }

            $parentNumericId = $request->input('parent');
            $name = $request->input('name');
            $userId = Auth::id();

            // Get parent info from mapping (we need to fetch it from database)
            $parentInfo = $this->getNodeInfoByNumericId($box->id, $parentNumericId);
            
            if (!$parentInfo) {
                return response()->json(['success' => false], 404);
            }

            if ($parentInfo['type'] === 'dept') {
                $deptId = $parentInfo['original_id'];
                $department = Department::where('box_id', $box->id)->find($deptId);
                
                if (!$department) {
                    return response()->json(['success' => false], 404);
                }

                // Generate sequential name if not provided
                if (!$name) {
                    $childName = $this->generateSequentialChildName($department->name, $department->id, null, $box->id);
                } else {
                    $childName = $name;
                }
                
                $team = new Team([
                    'department_id' => $department->id,
                    'box_id' => $box->id,
                    'name' => $name ?: $childName,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
                $team->saveAsRoot();
                $team->refresh();

                // Rebuild mapping to get correct numeric ID for new node
                $numericId = $this->getNumericIdForNode($box->id, 'team', $team->id);

                DB::commit();
                return response()->json([
                    'id' => $numericId,
                    'parent' => $parentNumericId,
                    'name' => $team->name
                ]);
            } elseif ($parentInfo['type'] === 'team') {
                $teamId = $parentInfo['original_id'];
                $parentTeam = Team::where('box_id', $box->id)->find($teamId);
                
                if (!$parentTeam) {
                    return response()->json(['success' => false], 404);
                }

                // Generate sequential name if not provided
                if (!$name) {
                    $childName = $this->generateSequentialChildName($parentTeam->name, $parentTeam->department_id, $parentTeam, $box->id);
                } else {
                    $childName = $name;
                }
                
                $team = new Team([
                    'department_id' => $parentTeam->department_id,
                    'box_id' => $box->id,
                    'name' => $name ?: $childName,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
                $team->appendToNode($parentTeam)->save();
                $team->refresh();

                // Rebuild mapping to get correct numeric ID for new node
                $numericId = $this->getNumericIdForNode($box->id, 'team', $team->id);

                DB::commit();
                return response()->json([
                    'id' => $numericId,
                    'parent' => $parentNumericId,
                    'name' => $team->name
                ]);
            } else {
                // Root level (parent = 0) - create department
                $department = Department::create([
                    'name' => $name,
                    'box_id' => $box->id,
                    'user_id' => $userId,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);

                // Rebuild mapping to get correct numeric ID for new node
                $numericId = $this->getNumericIdForNode($box->id, 'dept', $department->id);

                DB::commit();
                return response()->json([
                    'id' => $numericId,
                    'parent' => 0,
                    'name' => $department->name
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error adding orgchart node: ' . $th->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    private function getNodeInfoByNumericId($boxId, $numericId)
    {
        if ($numericId == 0) {
            return ['type' => 'org', 'original_id' => 0];
        }

        if ($numericId >= self::TEAM_ID_OFFSET) {
            return ['type' => 'team', 'original_id' => $numericId - self::TEAM_ID_OFFSET];
        } else {
            return ['type' => 'dept', 'original_id' => $numericId];
        }
    }

    private function findTeamInMapping($teams, $targetNumericId, $startId)
    {
        $currentId = $startId;
        foreach ($teams as $team) {
            if ($currentId == $targetNumericId) {
                return ['type' => 'team', 'original_id' => $team->id, 'next_id' => $currentId + 1, 'found' => true];
            }
            $currentId++;
            
            if ($team->children && $team->children->count() > 0) {
                $result = $this->findTeamInMapping($team->children, $targetNumericId, $currentId);
                if (isset($result['found']) && $result['found']) {
                    return $result;
                }
                $currentId = $result['next_id'] ?? $currentId;
            }
        }
        return ['next_id' => $currentId, 'found' => false];
    }

    private function getNumericIdForNode($boxId, $type, $originalId)
    {
        // Rebuild mapping to find numeric ID for a specific node
        $departments = Department::where('box_id', $boxId)->defaultOrder()->get();
        $currentId = 1; // Start from 1 (0 is org root)
        
        foreach ($departments as $dept) {
            if ($type === 'dept' && $dept->id == $originalId) {
                return $currentId;
            }
            $currentId++;
            
            $teams = Team::where('department_id', $dept->id)->defaultOrder()->get();
            $result = $this->findTeamNumericId($teams, $type, $originalId, $currentId);
            if ($result['found']) {
                return $result['id'];
            }
            $currentId = $result['next_id'];
        }
        
        return $currentId; // Fallback
    }

    private function findTeamNumericId($teams, $targetType, $targetOriginalId, $startId)
    {
        $currentId = $startId;
        foreach ($teams as $team) {
            if ($targetType === 'team' && $team->id == $targetOriginalId) {
                return ['found' => true, 'id' => $currentId, 'next_id' => $currentId + 1];
            }
            $currentId++;
            
            if ($team->children && $team->children->count() > 0) {
                $result = $this->findTeamNumericId($team->children, $targetType, $targetOriginalId, $currentId);
                if ($result['found']) {
                    return $result;
                }
                $currentId = $result['next_id'];
            }
        }
        return ['found' => false, 'next_id' => $currentId];
    }

    public function orgchart_delete(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json(['success' => false], 404);
            }

            $numericId = $request->input('id');
            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $numericId);

            if ($nodeInfo && $nodeInfo['type'] === 'dept') {
                $department = Department::where('box_id', $box->id)->find($nodeInfo['original_id']);
                if ($department) {
                    $department->delete();
                }
            } elseif ($nodeInfo && $nodeInfo['type'] === 'team') {
                $team = Team::where('box_id', $box->id)->find($nodeInfo['original_id']);
                if ($team) {
                    $team->delete();
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error deleting orgchart node: ' . $th->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function orgchart_update(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json(['success' => false], 404);
            }

            $numericId = $request->input('id');
            $name = $request->input('name');
            $userId = Auth::id();
            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $numericId);

            if ($nodeInfo && $nodeInfo['type'] === 'dept') {
                $department = Department::where('box_id', $box->id)->find($nodeInfo['original_id']);
                if ($department) {
                    $department->update(['name' => $name, 'updated_by' => $userId]);
                }
            } elseif ($nodeInfo && $nodeInfo['type'] === 'team') {
                $team = Team::where('box_id', $box->id)->find($nodeInfo['original_id']);
                if ($team) {
                    $team->update(['name' => $name, 'updated_by' => $userId]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error updating orgchart node: ' . $th->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function orgchart_move(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json(['success' => false], 404);
            }

            $numericId = $request->input('id');
            $newParentNumericId = $request->input('parent');
            $userId = Auth::id();

            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $numericId);
            $parentInfo = $this->getNodeInfoByNumericId($box->id, $newParentNumericId);

            if ($nodeInfo && $nodeInfo['type'] === 'team') {
                $team = Team::where('box_id', $box->id)->find($nodeInfo['original_id']);
                
                if (!$team) {
                    return response()->json(['success' => false], 404);
                }

                if ($newParentNumericId == 0 || ($parentInfo && $parentInfo['type'] === 'dept')) {
                    // Move to root or under department
                    $deptId = $newParentNumericId == 0 ? $team->department_id : $parentInfo['original_id'];
                    $team->department_id = $deptId;
                    $team->saveAsRoot();
                } elseif ($parentInfo && $parentInfo['type'] === 'team') {
                    // Move under another team
                    $parentTeam = Team::where('box_id', $box->id)->find($parentInfo['original_id']);
                    
                    if ($parentTeam) {
                        $team->department_id = $parentTeam->department_id;
                        $team->appendToNode($parentTeam)->save();
                    }
                }
                
                $team->updated_by = $userId;
                $team->save();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error moving orgchart node: ' . $th->getMessage());
            return response()->json(['success' => false], 500);
        }
    }



    public function get_node_permissions(Request $request, $box_slug)
    {
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $numericId = $request->input('id');
            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $numericId);

            if (!$nodeInfo) {
                return response()->json(['success' => false, 'message' => 'Node not found'], 404);
            }

            $permissions = [];
            if ($nodeInfo['type'] === 'dept') {
                $dept = Department::find($nodeInfo['original_id']);
                $permissions = $dept ? $dept->permissions->pluck('name')->toArray() : [];
            } elseif ($nodeInfo['type'] === 'team') {
                $team = Team::find($nodeInfo['original_id']);
                $permissions = $team ? $team->permissions->pluck('name')->toArray() : [];
            }

            return response()->json([
                'success' => true,
                'permissions' => $permissions
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function save_node_permissions(Request $request, $box_slug)
    {
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) {
                return response()->json(['success' => false, 'message' => 'Box not found'], 404);
            }

            $numericId = $request->input('id');
            $permissions = $request->input('permissions', []);
            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $numericId);

            if (!$nodeInfo) {
                return response()->json(['success' => false, 'message' => 'Node not found'], 404);
            }

            if ($nodeInfo['type'] === 'dept') {
                $dept = Department::find($nodeInfo['original_id']);
                if ($dept) {
                    $dept->syncPermissions($permissions);
                }
            } elseif ($nodeInfo['type'] === 'team') {
                $team = Team::find($nodeInfo['original_id']);
                if ($team) {
                    $team->syncPermissions($permissions);
                }
            }

            return response()->json(['success' => true, 'message' => 'Permissions updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function get_box_employees(Request $request, $box_slug)
    {
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

            $employees = BoxUser::with('user')
                ->where('box_id', $box->id)
                ->where('status', 'Enabled')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $employees->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'user_id' => $emp->user_id,
                        'name' => $emp->name ?? ($emp->user->name ?? $emp->username),
                        'email' => $emp->user->email ?? '',
                        'phone' => $emp->phone ?? ($emp->user->mobile ?? ''),
                        'department' => $emp->department,
                        'designation' => $emp->designation,
                        'reporting_manager_id' => $emp->reporting_manager_id
                    ];
                })
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function get_node_personnel(Request $request, $box_slug)
    {
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

            $numericId = $request->input('id');
            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $numericId);
            
            if (!$nodeInfo) return response()->json(['success' => false, 'message' => 'Node not found'], 404);

            $query = BoxUser::with('user', 'reportingManager')
                ->where('box_id', $box->id)
                ->where('status', 'Enabled');

            if ($nodeInfo['type'] === 'dept') {
                $query->where('department', $nodeInfo['original_id'])
                      ->whereNull('designation'); // Only at dept level
            } elseif ($nodeInfo['type'] === 'team') {
                $query->where('designation', $nodeInfo['original_id']);
            } else {
                return response()->json(['success' => true, 'data' => []]);
            }

            $personnel = $query->get();

            return response()->json([
                'success' => true,
                'data' => $personnel->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->name ?? ($emp->user->name ?? $emp->username),
                        'email' => $emp->user->email ?? '',
                        'phone' => $emp->phone ?? ($emp->user->mobile ?? ''),
                        'reporting_manager' => $emp->reportingManager ? ($emp->reportingManager->name ?? $emp->reportingManager->username) : 'Direct Box'
                    ];
                })
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function assign_personnel(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

            $id = $request->input('id'); // Node ID (numeric)
            $employeeId = $request->input('employee_id'); // BoxUser ID
            
            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $id);
            if (!$nodeInfo) return response()->json(['success' => false, 'message' => 'Node not found'], 404);

            $boxUser = BoxUser::where('box_id', $box->id)->find($employeeId);
            if (!$boxUser) return response()->json(['success' => false, 'message' => 'Employee not found'], 404);

            if ($nodeInfo['type'] === 'dept') {
                $boxUser->department = $nodeInfo['original_id'];
                $boxUser->designation = null; // Clear designation if assigned to department level
            } elseif ($nodeInfo['type'] === 'team') {
                $team = Team::find($nodeInfo['original_id']);
                if ($team) {
                    $boxUser->department = $team->department_id;
                    $boxUser->designation = $team->id;
                }
            }

            // Reporting Manager logic
            if ($request->has('reporting_manager_id')) {
                $boxUser->reporting_manager_id = $request->input('reporting_manager_id');
            }

            $boxUser->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee assigned successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
    public function unassign_personnel(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            $box = Box::where('slug', $box_slug)->first();
            if (!$box) return response()->json(['success' => false, 'message' => 'Box not found'], 404);

            $id = $request->input('id'); // Node ID (numeric)
            $employeeId = $request->input('employee_id'); // BoxUser ID
            
            $nodeInfo = $this->getNodeInfoByNumericId($box->id, $id);
            if (!$nodeInfo) return response()->json(['success' => false, 'message' => 'Node not found'], 404);

            $boxUser = BoxUser::where('box_id', $box->id)->find($employeeId);
            if (!$boxUser) return response()->json(['success' => false, 'message' => 'Employee not found'], 404);

            if ($nodeInfo['type'] === 'dept') {
                if ($boxUser->department == $nodeInfo['original_id']) {
                    $boxUser->department = null;
                }
            } elseif ($nodeInfo['type'] === 'team') {
                if ($boxUser->designation == $nodeInfo['original_id']) {
                    $boxUser->designation = null;
                }
            }

            $boxUser->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee unassigned successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function master_industry(Request $request, $box_slug)
    {
        if (MasterIndustry::count() === 0) {
            $seeder = new \Database\Seeders\MasterIndustrySeeder();
            $seeder->run();
        }
        return view('frontend.superadmin.industry', compact('box_slug'));
    }

    public function get_master_industry_list(Request $request, $box_slug)
    {
        try {
            $query = MasterIndustry::orderBy('created_at', 'desc');
            
            $data = $query->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'created_at' => $item->created_at ? $item->created_at->format('d M Y, h:i a') : '-',
                    'status' => $item->status ? 'Active' : 'Inactive',
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch master industry list.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store_master_industry(Request $request, $box_slug)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    \Illuminate\Validation\Rule::unique('master_industry', 'name')->whereNull('deleted_at')
                ],
                'status' => 'required|in:1,0,active,inactive'
            ]);

            $status = in_array($request->status, ['1', 'active']) ? 1 : 0;

            $industry = MasterIndustry::create([
                'name' => $request->name,
                'status' => $status,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Master Industry created successfully.',
                'data' => $industry
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create master industry.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update_master_industry(Request $request, $box_slug, $id)
    {
        try {
            $industry = MasterIndustry::findOrFail($id);

            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    \Illuminate\Validation\Rule::unique('master_industry', 'name')->ignore($id)->whereNull('deleted_at')
                ],
                'status' => 'required|in:1,0,active,inactive'
            ]);

            $status = in_array($request->status, ['1', 'active']) ? 1 : 0;

            $industry->update([
                'name' => $request->name,
                'status' => $status,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Master Industry updated successfully.',
                'data' => $industry
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update master industry.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy_master_industry(Request $request, $box_slug, $id)
    {
        try {
            $industry = MasterIndustry::findOrFail($id);
            $industry->delete();

            return response()->json([
                'success' => true,
                'message' => 'Master Industry deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete master industry.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function previewSubscription(\Illuminate\Http\Request $request, $box_slug, $id)
    {
        $subscription = \App\Models\UserSubscription::with(['user', 'plan'])->findOrFail($id);

        $paidCount = $subscription->raw_data['paid_count'] ?? 0;
        $planPrice = $subscription->plan ? $subscription->plan->price : 0;
        $totalAmount = $paidCount * $planPrice;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $subscription->id,
                'user_name' => $subscription->user ? $subscription->user->name : 'N/A',
                'user_email' => $subscription->user ? $subscription->user->email : 'N/A',
                'user_contact' => $subscription->user ? $subscription->user->mobile_no : 'N/A',
                'plan_name' => $subscription->plan ? $subscription->plan->name : 'N/A',
                'plan_price' => $planPrice,
                'status' => ucfirst($subscription->status),
                'starts_at' => $subscription->starts_at ? $subscription->starts_at->format('d M Y') : 'N/A',
                'ends_at' => $subscription->ends_at ? $subscription->ends_at->format('d M Y') : 'N/A',
                'next_billing_at' => $subscription->next_billing_at ? $subscription->next_billing_at->format('d M Y') : 'N/A',
                'paid_count' => $paidCount,
                'total_amount' => $totalAmount
            ]
        ]);
    }

    public function updateSubscriptionStatus(\Illuminate\Http\Request $request, $box_slug, $id)
    {
        $request->validate(['status' => 'required|in:active,inactive,cancelled,expired,authenticated,overdue']);
        
        $subscription = \App\Models\UserSubscription::findOrFail($id);
        $subscription->status = $request->status;
        if ($request->status == 'cancelled') {
            $subscription->cancelled_at = now();
        }
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription status updated successfully to ' . ucfirst($request->status) . '!'
        ]);
    }
    public function getCompaniesAndPlans(\Illuminate\Http\Request $request)
    {
        $latestSubs = \App\Models\UserSubscription::where('status', 'active')
            ->orderBy('ends_at', 'desc')
            ->get()
            ->groupBy('box_id');

        $boxes = \App\Models\Box::with('superadmin')->get()->map(function($box) use ($latestSubs) {
            $subs = $latestSubs->get($box->id);
            $lastSub = $subs ? $subs->first() : null;
            $lastEndsAt = ($lastSub && $lastSub->ends_at && $lastSub->ends_at->isFuture()) ? $lastSub->ends_at->addDay()->format('Y-m-d') : null;

            return [
                'id' => $box->id,
                'name' => $box->name . ' (' . ($box->slug ?? 'N/A') . ') - ' . ($box->superadmin->name ?? 'Unknown'),
                'last_active_end_date' => $lastEndsAt
            ];
        });

        $plans = \App\Models\Plan::where('payment_mode', 'offline')->get()->map(function($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name . ' - ' . ucfirst($plan->type) . ' (' . ($plan->currency == 'INR' ? '₹' : '$') . $plan->price . ')',
                'type' => strtolower($plan->type)
            ];
        });

        return response()->json([
            'success' => true,
            'boxes' => $boxes,
            'plans' => $plans
        ]);
    }

    public function assignOfflinePlan(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'box_id' => 'required|exists:boxes,id',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'status' => 'required|in:active,inactive,cancelled,expired,authenticated,overdue'
        ]);

        $box = \App\Models\Box::findOrFail($request->box_id);
        $plan = \App\Models\Plan::findOrFail($request->plan_id);

        $startsAt = \Carbon\Carbon::parse($request->starts_at)->startOfDay();
        $endsAt = \Carbon\Carbon::parse($request->ends_at)->endOfDay();

        $userId = $box->superadmin_id ?? \Illuminate\Support\Facades\Auth::id();
        $boxUserId = \App\Models\BoxUser::where('box_id', $box->id)->where('user_id', $userId)->value('id');

        $subscription = new \App\Models\UserSubscription([
            'user_id' => $userId,
            'box_id' => $box->id,
            'box_user_id' => $boxUserId,
            'plan_id' => $plan->id,
            'status' => $request->status,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'next_billing_at' => $endsAt,
            'payment_method' => 'Offline'
        ]);
        
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Offline plan assigned successfully!'
        ]);
    }
    public function bulkAssignOfflinePlan(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,inactive,cancelled,expired,authenticated,overdue'
        ]);

        $plan = \App\Models\Plan::findOrFail($request->plan_id);
        $boxes = \App\Models\Box::all();

        $successCount = 0;
        foreach ($boxes as $box) {
            // Check if it has start_date and end_date
            if (empty($box->start_date) || empty($box->end_date)) {
                continue; // Skip boxes without valid dates
            }

            $startsAt = \Carbon\Carbon::parse($box->start_date)->startOfDay();
            $endsAt = \Carbon\Carbon::parse($box->end_date)->endOfDay();
            
            $userId = $box->superadmin_id ?? \Illuminate\Support\Facades\Auth::id();
            $boxUserId = \App\Models\BoxUser::where('box_id', $box->id)->where('user_id', $userId)->value('id');

            $subscription = new \App\Models\UserSubscription([
                'user_id' => $userId,
                'box_id' => $box->id,
                'box_user_id' => $boxUserId,
                'plan_id' => $plan->id,
                'status' => $request->status,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'next_billing_at' => $endsAt,
                'payment_method' => 'Offline'
            ]);
            
            $subscription->save();
            $successCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Offline plan bulk assigned successfully to {$successCount} companies!"
        ]);
    }

}