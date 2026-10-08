<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\Project;
use App\Exports\ProjectsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProjectType;
use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ProjectController extends Controller
{
    private function getBoxData()
    {
        $userId = Auth::id();
        $boxUser = BoxUser::where('user_id', $userId)->first();
        if (!$boxUser) {
            return false;
        }

        return [
            'box_id' => $boxUser->box_id,
            'box_user_id' => $boxUser->id
        ];
    }

    public function index(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             abort(403, 'Unauthorized');
        }

        if ($request->ajax()) {
            $query = Project::where('box_id', $boxData['box_id'])
                ->with(['projectType', 'projectCategory', 'teamMembers', 'contacts']);

            if ($request->has('date_range') && !empty($request->date_range)) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $startDate = \Carbon\Carbon::parse(trim($dates[0]))->startOfDay();
                        $endDate = \Carbon\Carbon::parse(trim($dates[1]))->endOfDay();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    } catch (\Exception $e) {}
                }
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'desc':
                        $query->orderBy('name', 'desc');
                        break;
                    case 'recent':
                        $query->orderBy('updated_at', 'desc');
                        break;
                    case 'added':
                        $query->orderBy('created_at', 'desc');
                        break;
                }
            } else {
                $query->orderBy('id', 'desc');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('contacts', function($row) {
                    $count = $row->contacts->count();
                    if ($count == 0) return '<span class="badge badge-light-secondary">No Contacts</span>';
                    
                    $names = $row->contacts->pluck('name')->implode(', ');
                    return '<span class="badge badge-light-info" data-bs-toggle="tooltip" title="'.$names.'">'.$count.' Contacts</span>';
                })
                
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center"><a href="javascript:void(0);" class="btn btn-primary btn-sm edit-project-btn me-2" data-id="' . $row->id . '"><i class="ti ti-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm delete-project-btn" data-id="' . $row->id . '"><i class="ti ti-trash"></i></a></div>';
                    return $btn;
                })
                ->addColumn('name_link', function($row) {
                    return '<a href="#">' . $row->name . '</a>';
                })
                ->addColumn('client_name', function($row) {
                    return $row->client_name ?? 'N/A';
                })
                ->addColumn('priority', function($row) {
                    if($row->priority == 'High') return '<div><i class="ti ti-circle-filled text-danger fs-10 me-2"></i>High</div>';
                    if($row->priority == 'Medium') return '<div><i class="ti ti-circle-filled text-warning fs-10 me-2"></i>Medium</div>';
                    return '<div><i class="ti ti-circle-filled text-success fs-10 me-2"></i>Low</div>';
                })
                ->addColumn('status', function($row) {
                    return $row->status == 'Active' ? '<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>' : '<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>';
                })
                ->addColumn('team_members', function($row) {
                    $html = '<div class="avatar-list-stacked avatar-group-sm">';
                    foreach($row->teamMembers as $member) {
                        $html .= '<span class="avatar avatar-rounded"><img class="border border-white" src="https://ui-avatars.com/api/?name='.urlencode($member->first_name).'" alt="img"></span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('due_date', function($row) {
                    return $row->due_date ? Carbon::parse($row->due_date)->format('d M Y') : 'N/A';
                })
                ->rawColumns(['action', 'name_link', 'priority', 'status', 'team_members', 'contacts'])
                ->make(true);
        }

        $totalProjects = Project::where('box_id', $boxData['box_id'])->count();
        return view('user_management.projects', compact('totalProjects'));
    }

    public function store(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'project_custom_id' => 'nullable|string|max:255',
            'project_type_id' => 'nullable|exists:project_types,id',
            'project_category_id' => 'nullable|exists:project_categories,id',
            'client_name' => 'nullable|string|max:255',
            'project_timing' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255|distinct',
            'contacts.*.phone' => 'nullable|string|max:255|distinct',
            'contacts.*.designation' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $project = Project::create([
            'box_id' => $boxData['box_id'],
            'box_user_id' => $boxData['box_user_id'],
            'name' => $request->name,
            'project_custom_id' => $request->project_custom_id,
            'project_type_id' => $request->project_type_id,
            'project_category_id' => $request->project_category_id,
            'client_name' => $request->client_name,
            'project_timing' => $request->project_timing,
            'price' => $request->price,
            'amount' => $request->amount,
            'total' => $request->total,
            'start_date' => $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null,
            'due_date' => $request->due_date ? Carbon::parse($request->due_date)->format('Y-m-d') : null,
            'priority' => $request->priority ?? 'Low',
            'status' => $request->status ?? 'Active',
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        if ($request->has('responsible_persons')) {
            foreach ($request->responsible_persons as $userId) {
                $project->teamMembers()->attach($userId, ['role' => 'responsible_person']);
            }
        }

        if ($request->has('team_leaders')) {
            foreach ($request->team_leaders as $userId) {
                $project->teamMembers()->attach($userId, ['role' => 'team_leader']);
            }
        }

        if ($request->has('contacts') && is_array($request->contacts)) {
            foreach ($request->contacts as $contactData) {
                if (!empty($contactData['name'])) {
                    $project->contacts()->create([
                        'name' => $contactData['name'],
                        'email' => $contactData['email'] ?? null,
                        'phone' => $contactData['phone'] ?? null,
                        'designation' => $contactData['designation'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Project created successfully.']);
    }

    public function getContactsJson($box_slug, $id)
    {
        $boxData = $this->getBoxData();
        if (!$boxData) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $project = Project::where('box_id', $boxData['box_id'])->findOrFail($id);
        $contacts = $project->contacts()->select('id', 'name', 'email', 'phone', 'designation')->get();
        return response()->json(['success' => true, 'data' => $contacts]);
    }

    public function show($box_slug, $id)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $project = Project::where('box_id', $boxData['box_id'])->with(['teamMembers', 'contacts'])->findOrFail($id);
        
        $responsiblePersons = $project->teamMembers->where('pivot.role', 'responsible_person')->pluck('id')->toArray();
        $teamLeaders = $project->teamMembers->where('pivot.role', 'team_leader')->pluck('id')->toArray();

        return response()->json([
            'status' => true,
            'data' => $project,
            'contacts' => $project->contacts,
            'responsible_persons' => $responsiblePersons,
            'team_leaders' => $teamLeaders
        ]);
    }

    public function update(Request $request, $box_slug, $id)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'project_custom_id' => 'nullable|string|max:255',
            'project_type_id' => 'nullable|exists:project_types,id',
            'project_category_id' => 'nullable|exists:project_categories,id',
            'client_name' => 'nullable|string|max:255',
            'project_timing' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255|distinct',
            'contacts.*.phone' => 'nullable|string|max:255|distinct',
            'contacts.*.designation' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $project = Project::where('box_id', $boxData['box_id'])->findOrFail($id);
        
        $project->update([
            'name' => $request->name,
            'project_custom_id' => $request->project_custom_id,
            'project_type_id' => $request->project_type_id,
            'project_category_id' => $request->project_category_id,
            'client_name' => $request->client_name,
            'project_timing' => $request->project_timing,
            'price' => $request->price,
            'amount' => $request->amount,
            'total' => $request->total,
            'start_date' => $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null,
            'due_date' => $request->due_date ? Carbon::parse($request->due_date)->format('Y-m-d') : null,
            'priority' => $request->priority ?? 'Low',
            'status' => $request->status ?? 'Active',
            'description' => $request->description,
            'updated_by' => Auth::id(),
        ]);

        // Sync team members
        $syncData = [];
        if ($request->has('responsible_persons')) {
            foreach ($request->responsible_persons as $userId) {
                $syncData[$userId] = ['role' => 'responsible_person'];
            }
        }
        if ($request->has('team_leaders')) {
            foreach ($request->team_leaders as $userId) {
                // If a user is both, they will just be recorded as team leader here as it overrides
                $syncData[$userId] = ['role' => 'team_leader'];
            }
        }
        if ($request->has('contacts') && is_array($request->contacts)) {
            $submittedIds = [];
            
            foreach ($request->contacts as $contactData) {
                if (!empty($contactData['name'])) {
                    if (!empty($contactData['id'])) {
                        // Update existing
                        $contact = $project->contacts()->find($contactData['id']);
                        if ($contact) {
                            $contact->update([
                                'name' => $contactData['name'],
                                'email' => $contactData['email'] ?? null,
                                'phone' => $contactData['phone'] ?? null,
                                'designation' => $contactData['designation'] ?? null,
                            ]);
                            $submittedIds[] = $contact->id;
                        }
                    } else {
                        // Create new
                        $newContact = $project->contacts()->create([
                            'name' => $contactData['name'],
                            'email' => $contactData['email'] ?? null,
                            'phone' => $contactData['phone'] ?? null,
                            'designation' => $contactData['designation'] ?? null,
                        ]);
                        $submittedIds[] = $newContact->id;
                    }
                }
            }
            
            // Delete contacts that were removed from the UI
            $project->contacts()->whereNotIn('id', $submittedIds)->delete();

        } else {
             $project->contacts()->delete();
        }

        $project->teamMembers()->sync($syncData);

        return response()->json(['status' => true, 'message' => 'Project updated successfully.']);
    }

    public function destroy($box_slug, $id)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }
        $project = Project::where('box_id', $boxData['box_id'])->findOrFail($id);
        $project->update(['deleted_by' => Auth::id()]);
        $project->delete();
        return response()->json(['status' => true, 'message' => 'Project deleted successfully.']);
    }

    public function getProjectTypes(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) return response()->json(['success' => false], 403);
        $data = ProjectType::where('box_id', $boxData['box_id'])->where('status', 1)->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getProjectCategories(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) return response()->json(['success' => false], 403);
        $data = ProjectCategory::where('box_id', $boxData['box_id'])->where('status', 1)->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getUsers(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) return response()->json(['success' => false], 403);
        $data = User::whereHas('box_user_new', function($q) use ($boxData) {
            $q->where('box_id', $boxData['box_id']);
        })->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getLeads(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) return response()->json(['success' => false], 403);
        
        $query = \Illuminate\Support\Facades\DB::table('leads')
            ->where('box_id', $boxData['box_id'])
            ->select('id', 'name', 'last_name');

        if ($request->has('q') && !empty($request->q)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $request->q . '%');
            });
        }

        $data = $query->limit(20)->get();
        
        return response()->json(['success' => true, 'data' => $data]);
    }


    public function exportExcel(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             abort(403, 'Unauthorized');
        }
        return Excel::download(new ProjectsExport($boxData['box_id']), 'projects.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             abort(403, 'Unauthorized');
        }
        $projects = Project::with(['projectType', 'projectCategory', 'teamMembers'])->where('box_id', $boxData['box_id'])->get();
        $pdf = Pdf::loadView('user_management.exports.projects_pdf', compact('projects'));
        return $pdf->download('projects.pdf');
    }
    public function contactsIndex(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             abort(403, 'Unauthorized');
        }

        if ($request->ajax()) {
            $query = \App\Models\ProjectContact::with('project')
                ->whereHas('project', function($q) use ($boxData) {
                    $q->where('box_id', $boxData['box_id']);
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('project_name', function($row) {
                    return $row->project ? $row->project->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center"><a href="javascript:void(0);" class="btn btn-primary btn-sm edit-contact-btn me-2" data-id="' . $row->id . '"><i class="ti ti-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm delete-contact-btn" data-id="' . $row->id . '"><i class="ti ti-trash"></i></a></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $totalContacts = \App\Models\ProjectContact::whereHas('project', function($q) use ($boxData) {
            $q->where('box_id', $boxData['box_id']);
        })->count();
        
        $projects = \App\Models\Project::where('box_id', $boxData['box_id'])->get();

        return view('user_management.project_contacts', compact('totalContacts', 'projects'));
    }
    public function storeContact(Request $request)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_contacts')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id)->whereNull('deleted_at');
                })
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_contacts')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id)->whereNull('deleted_at');
                })
            ],
            'designation' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'This email is already in use by another contact in this project.',
            'phone.unique' => 'This phone number is already in use by another contact in this project.'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        // Verify project belongs to box
        $project = \App\Models\Project::where('box_id', $boxData['box_id'])
            ->where('id', $request->project_id)
            ->first();

        if (!$project) {
             return response()->json(['status' => false, 'message' => 'Invalid Project.'], 400);
        }

        \App\Models\ProjectContact::create([
            'project_id' => $project->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
        ]);

        return response()->json(['status' => true, 'message' => 'Contact added successfully.']);
    }

    public function destroyContact(Request $request, $box_slug, $id)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $contact = \App\Models\ProjectContact::whereHas('project', function($q) use ($boxData) {
            $q->where('box_id', $boxData['box_id']);
        })->findOrFail($id);

        $contact->delete();

        return response()->json(['status' => true, 'message' => 'Contact deleted successfully.']);
    }

    public function showContact(Request $request, $box_slug, $id)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $contact = \App\Models\ProjectContact::whereHas('project', function($q) use ($boxData) {
            $q->where('box_id', $boxData['box_id']);
        })->findOrFail($id);

        return response()->json(['status' => true, 'data' => $contact]);
    }

    public function updateContact(Request $request, $box_slug, $id)
    {
        $boxData = $this->getBoxData();
        if ($boxData === false) {
             return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $contact = \App\Models\ProjectContact::whereHas('project', function($q) use ($boxData) {
            $q->where('box_id', $boxData['box_id']);
        })->findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_contacts')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id)->whereNull('deleted_at');
                })->ignore($contact->id)
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('project_contacts')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id)->whereNull('deleted_at');
                })->ignore($contact->id)
            ],
            'designation' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'This email is already in use by another contact in this project.',
            'phone.unique' => 'This phone number is already in use by another contact in this project.'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $project = \App\Models\Project::where('box_id', $boxData['box_id'])
            ->where('id', $request->project_id)
            ->first();

        if (!$project) {
             return response()->json(['status' => false, 'message' => 'Invalid Project.'], 400);
        }

        $contact->update([
            'project_id' => $project->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
        ]);

        return response()->json(['status' => true, 'message' => 'Contact updated successfully.']);
    }
}