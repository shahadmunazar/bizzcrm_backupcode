<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function getDepartment(Request $request)
{
    try {

        $team = BoxUser::where('department', $request->team_id)->select('user_id','id','department','designation')->get();
        return response()->json(['status' => 200, 'data' => $team]);
    } catch (\Throwable $th) {
        return response()->json(['status' => 500, 'message' => 'Server Error'], 500);
    }
}

    public function GetDepartmentTeams(Request $request)
    {
        try {
            $user_id = Auth::id();
            $boxUser = BoxUser::where('user_id', $user_id)->first();
            $box_id = $boxUser ? $boxUser->box_id : null;

            $departments = Department::where('box_id', $box_id)->get();

            $departmentsWithTeams = $departments->map(function ($department) {
                $department->teams = Team::where('department_id', $department->id)
                    ->defaultOrder()
                    ->get()
                    ->toTree();

                return $department;
            });

            return response()->json([
                'success' => true,
                'data'    => $departmentsWithTeams,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function ManageDepartmentWithTeams(Request $request, $box_slug)
    {
        DB::beginTransaction();
        try {
            // Verify box belongs to authenticated user
            $box = Box::where('superadmin_id', Auth::id())
                ->where('slug', $box_slug)
                ->first();

            $data   = $request->all();
            $userId = Auth::id();

            if ($data['action'] === 'create') {
                return $this->handleCreate($data, $box, $userId);
            } elseif ($data['action'] === 'update') {
                return $this->handleUpdate($data, $box, $userId);
            } elseif ($data['action'] === 'delete') {
                return $this->handleDelete($data, $box, $userId);
            }
            throw new \Exception('Invalid action specified');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Operation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    protected function handleCreate($data, $box, $userId)
    {
        if (! isset($data['department']['id'])) {
            $department = Department::create([
                'name'               => $data['department']['name'],
                'box_id'             => $box->id,
                'user_id'            => $userId,
                'show_into_leads'    => $data['department']['show_into_leads'] ?? false,
                'show_into_accounts' => $data['department']['show_into_accounts'] ?? false,
                'created_by'         => $userId,
                'updated_by'         => $userId,
            ]);
        } else {
            $department = Department::where('box_id', $box->id)
                ->findOrFail($data['department']['id']);
        }
        $createdTeams = [];
        if (! empty($data['teams']) && is_array($data['teams'])) {
            foreach ($data['teams'] as $teamData) {
                $team = new Team([
                    'department_id'      => $department->id,
                    'box_id'             => $box->id,
                    'created_by'         => $userId,
                    'updated_by'         => $userId,
                    'user_id'            => $teamData['user_id'] ?? null,
                    'show_into_leads'    => $teamData['show_into_leads'] ?? false,
                    'show_into_accounts' => $teamData['show_into_accounts'] ?? false,
                ]);
                $baseName = trim($teamData['name']);
                if (! empty($teamData['parent_id'])) {
                    $parentTeam = Team::where('department_id', $department->id)
                        ->findOrFail($teamData['team_id']);
                    $childNames = $parentTeam->children()->pluck('name')->toArray();

                    $suffixes = [];
                    foreach ($childNames as $name) {
                        if (preg_match('/^' . preg_quote($parentTeam->name) . '\.(\d+)$/', $name, $matches)) {
                            $suffixes[] = (int) $matches[1];
                        }
                    }
                    $nextSuffix = empty($suffixes) ? 1 : max($suffixes) + 1;
                    $team->name = $parentTeam->name . '.' . $nextSuffix;
                    $team->appendToNode($parentTeam)->save();
                } else {
                    // Add as root if no parent_id
                    $existingRootTeams = Team::where('department_id', $department->id)
                        ->whereIsRoot()
                        ->where('name', 'REGEXP', '^' . preg_quote($baseName) . ' [0-9]+$')
                        ->pluck('name');

                    $numbers = $existingRootTeams->map(function ($name) use ($baseName) {
                        if (preg_match('/^' . preg_quote($baseName) . ' (\d+)$/', $name, $matches)) {
                            return (int) $matches[1];
                        }
                        return 0;
                    })->toArray();
                    $nextNumber = empty($numbers) ? 1 : max($numbers) + 1;
                    $team->name = $baseName . ' ' . $nextNumber;
                    $team->saveAsRoot();
                }

                $createdTeams[] = $team;
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully',
            'data'    => [
                'department' => $department,
                'teams'      => $createdTeams,
            ],
        ]);
    }

    protected function generateNumberedName(Team $parent)
    {
        // Count existing children
        $childCount = Team::where('parent_id', $parent->id)->count();

        // Generate new child name
        return $parent->name . '.' . ($childCount + 1);
    }

    protected function extractNumbersFromName($name)
    {
        preg_match_all('/\d+/', $name, $matches);
        return $matches[0] ?? [];
    }

    protected function handleUpdate($data, $box, $userId)
    {
        // Update department
        if (isset($data['department']['id'])) {
            $department = Department::where('box_id', $box->id)
                ->findOrFail($data['department']['id']);

            $department->update([
                'name'               => $data['department']['name'],
                'show_into_leads'    => $data['department']['show_into_leads'] ?? $department->show_into_leads,
                'show_into_accounts' => $data['department']['show_into_accounts'] ?? $department->show_into_accounts,
                'updated_by'         => $userId,
            ]);
        }

        // Update teams
        if (isset($data['teams'])) {
            foreach ($data['teams'] as $teamData) {
                $team = Team::where('department_id', $department->id ?? $data['department']['id'])
                    ->findOrFail($teamData['id']);

                $team->update([
                    'name'               => $teamData['name'],
                    'parent_id'          => $teamData['parent_id'] ?? null,
                    'updated_by'         => $userId,
                    'user_id'            => $teamData['user_id'] ?? $team->user_id,
                    'show_into_leads'    => $teamData['show_into_leads'] ?? $team->show_into_leads,
                    'show_into_accounts' => $teamData['show_into_accounts'] ?? $team->show_into_accounts,
                ]);
            }
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
        ]);
    }

 protected function handleDelete($data, $box, $userId)
{
    if (!isset($data['deleted_team_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'No team selected for deletion.',
        ], 400);
    }

    $team = Team::whereHas('department', function ($q) use ($box) {
        $q->where('box_id', $box->id);
    })->findOrFail($data['deleted_team_id']);

    // Get all descendant teams using nested set model
    $descendantTeams = Team::where('_lft', '>', $team->_lft)
        ->where('_rgt', '<', $team->_rgt)
        ->pluck('id');

    $hasChildTeams = $descendantTeams->isNotEmpty();

    // Get current team and its children
    $teamIds = Team::where('_lft', '>=', $team->_lft)
        ->where('_rgt', '<=', $team->_rgt)
        ->pluck('id');

    // Check for users in this team or child teams
    $hasEmployees = User::whereIn('team_id', $teamIds)->exists();

    if ($hasChildTeams || $hasEmployees) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot delete: This team has sub-teams or assigned users.',
        ], 403);
    }

    // Mark as deleted
    Team::whereIn('id', $teamIds)->update(['deleted_by' => $userId]);

    // Now delete the team (only the selected one, since no children exist)
    $team->delete();

    DB::commit();

    return response()->json([
        'success' => true,
        'message' => 'Deleted successfully',
    ]);
}



}
