<?php
namespace App\Http\Controllers;
use App\Models\{Box, Department, Team, BoxUser};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log, Validator};
use InvalidArgumentException;

class OrgChartController extends Controller
{
    private const TEAM_ID_OFFSET = 1000000;
    private const USER_ID_OFFSET = 2000000;

    /**
     * Get organization chart data for a specific box
     *
     * @param Request $request
     * @param string $box_slug
     * @return JsonResponse
     */
    public function getOrgChartData(Request $request, string $box_slug): JsonResponse
    {
        try {
            $box = Box::where('slug', $box_slug)->firstOrFail();

            $departments = Department::where('box_id', $box->id)
                ->orderBy('_lft')
                ->get(['id', 'name', 'parent_id', 'user_id', 'show_into_leads', 'show_into_accounts']);

            $teams = Team::where('box_id', $box->id)
                ->orderBy('_lft')
                ->get(['id', 'name', 'parent_id', 'department_id', 'user_id', 'show_into_leads', 'show_into_accounts']);

            $tree = [];
            $existingNodeIds = [0];

            // Add box as root node
            $tree[] = [
                'id' => 0,
                'name' => $box->name ?? 'Organization',
                'parent' => 0,
            ];

            // Build department map for quick lookup
            $departmentMap = $departments->keyBy('id');
            $teamMap = $teams->keyBy('id');

            // Add departments
            foreach ($departments as $department) {
                $existingNodeIds[] = $department->id;
                $parentId = 0;

                if ($department->parent_id) {
                    if (isset($departmentMap[$department->parent_id])) {
                        $parentId = $department->parent_id;
                    }
                    elseif (isset($teamMap[$department->parent_id])) {
                        $parentId = self::TEAM_ID_OFFSET + $department->parent_id;
                    }
                }

                $tree[] = [
                    'id' => $department->id,
                    'name' => $department->name,
                    'parent' => $parentId,
                    'user_id' => $department->user_id,
                    'type' => 'dept',
                    'show_into_leads' => $department->show_into_leads,
                    'show_into_accounts' => $department->show_into_accounts,
                ];
            }

            // Add teams
            foreach ($teams as $team) {
                $teamId = self::TEAM_ID_OFFSET + $team->id;
                $parentId = $team->parent_id
                    ? (self::TEAM_ID_OFFSET + $team->parent_id)
                    : $team->department_id;

                if ($team->parent_id || in_array($team->department_id, $existingNodeIds)) {
                    $tree[] = [
                        'id' => $teamId,
                        'name' => $team->name,
                        'parent' => $parentId,
                        'user_id' => $team->user_id,
                        'type' => 'team',
                        'department_id' => $team->department_id,
                        'show_into_leads' => $team->show_into_leads,
                        'show_into_accounts' => $team->show_into_accounts,
                    ];
                }
                else {
                    Log::warning('Team has invalid department parent', [
                        'team_id' => $team->id,
                        'team_name' => $team->name,
                        'department_id' => $team->department_id,
                    ]);
                }
            }

            /*
            // ADD PERSONNEL HIERARCHY
            $users = BoxUser::where('box_id', $box->id)
                ->where('status', 1)
                ->with('user:id,name,email')
                ->get();

            foreach ($users as $user) {
                // Determine Parent ID
                // 1. Reporting Manager (Priority)
                // 2. Designation (Team)
                // 3. Department
                $parentId = 0;
                if ($user->reporting_manager_id) {
                    $parentId = self::USER_ID_OFFSET + $user->reporting_manager_id;
                } elseif ($user->designation_id) {
                    $parentId = self::TEAM_ID_OFFSET + $user->designation_id;
                } elseif ($user->department_id) {
                    $parentId = $user->department_id;
                }

                $tree[] = [
                    'id' => self::USER_ID_OFFSET + $user->id,
                    'name' => $user->user->name ?? 'Unknown',
                    'title' => $user->designation ?? 'Employee',
                    'email' => $user->user->email ?? '',
                    'parent' => $parentId,
                    'type' => 'user',
                    'user_id' => $user_id = $user->user_id,
                ];
            }
            */


            return response()->json(['data' => $tree]);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Box not found',
            ], 404);
        }
        catch (\Exception $e) {
            Log::error('Error fetching org chart data', [
                'box_slug' => $box_slug,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load organization structure',
            ], 500);
        }
    }

    /**
     * Create a new organization unit (department or team)
     *
     * @param Request $request
     * @param string $box_slug
     * @return JsonResponse
     */
    public function addNode(Request $request, string $box_slug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'parent' => 'required|integer|min:0',
            'name' => 'required|string|max:255|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $box = Box::where('slug', $box_slug)->firstOrFail();
            $userId = Auth::id();
            $parent = (int)$request->parent;
            $name = trim($request->name);

            $showIntoLeads = $request->boolean('show_into_leads');
            $showIntoAccounts = $request->boolean('show_into_accounts');

            return DB::transaction(function () use ($box, $userId, $parent, $name, $showIntoLeads, $showIntoAccounts) {
                if ($parent === 0) {
                    return $this->createDepartment($box, $userId, $name, $showIntoLeads, $showIntoAccounts);
                }

                if ($parent < self::TEAM_ID_OFFSET) {
                    return $this->createTeamUnderDepartment($box, $userId, $parent, $name, $showIntoLeads, $showIntoAccounts);
                }

                return $this->createSubTeam($box, $userId, $parent, $name, $showIntoLeads, $showIntoAccounts);
            });
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Box or parent not found',
            ], 404);
        }
        catch (\Exception $e) {
            Log::error('Error creating organization unit', [
                'box_slug' => $box_slug,
                'request' => $request->all(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to create organization unit',
            ], 500);
        }
    }

    /**
     * Update organization unit name
     *
     * @param Request $request
     * @param string $box_slug
     * @return JsonResponse
     */
    public function updateNode(Request $request, string $box_slug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|max:255|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $box = Box::where('slug', $box_slug)->firstOrFail();
            $userId = Auth::id();
            $id = (int)$request->id;
            $name = trim($request->name);

            $showIntoLeads = $request->boolean('show_into_leads');
            $showIntoAccounts = $request->boolean('show_into_accounts');

            if ($id < self::TEAM_ID_OFFSET) {
                $department = Department::where('box_id', $box->id)->findOrFail($id);
                $department->update([
                    'name' => $name,
                    'show_into_leads' => $showIntoLeads,
                    'show_into_accounts' => $showIntoAccounts,
                    'updated_by' => $userId,
                ]);

                return response()->json(['success' => true]);
            }

            $teamId = $id - self::TEAM_ID_OFFSET;
            $team = Team::where('box_id', $box->id)->findOrFail($teamId);
            $team->update([
                'name' => $name,
                'show_into_leads' => $showIntoLeads,
                'show_into_accounts' => $showIntoAccounts,
                'updated_by' => $userId,
            ]);

            return response()->json(['success' => true]);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Organization unit not found',
            ], 404);
        }
        catch (\Exception $e) {
            Log::error('Error updating organization unit', [
                'box_slug' => $box_slug,
                'request' => $request->all(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update organization unit',
            ], 500);
        }
    }

    /**
     * Move a node to a new parent
     *
     * @param Request $request
     * @param string $box_slug
     * @return JsonResponse
     */
    public function moveNode(Request $request, string $box_slug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'parent' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $box = Box::where('slug', $box_slug)->firstOrFail();
            $userId = Auth::id();
            $id = (int)$request->id;
            $newParent = (int)$request->parent;

            if ($id >= self::USER_ID_OFFSET) {
                return $this->moveEmployee($box, $id, $newParent, $userId);
            }

            if ($id === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Owner/Box node cannot be moved',
                ], 422);
            }

            if ($id < self::TEAM_ID_OFFSET) {
                return $this->moveDepartment($box, $id, $newParent, $userId);
            }

            if ($newParent === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot move team to root level',
                ], 422);
            }

            return DB::transaction(function () use ($box, $userId, $id, $newParent) {
                $teamId = $id - self::TEAM_ID_OFFSET;
                $team = Team::where('box_id', $box->id)->findOrFail($teamId);

                $this->validateMoveOperation($box, $team, $newParent);

                if ($newParent < self::TEAM_ID_OFFSET) {
                    return $this->moveTeamToDepartment($team, $newParent, $userId);
                }

                return $this->moveTeamToTeam($box, $team, $newParent, $userId);
            });
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Node or parent not found',
            ], 404);
        }
        catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
        catch (\Exception $e) {
            Log::error('Error moving node', [
                'box_slug' => $box_slug,
                'request' => $request->all(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to reorganize structure',
            ], 500);
        }
    }

    /**
     * Delete an organization unit
     *
     * @param Request $request
     * @param string $box_slug
     * @return JsonResponse
     */
    public function deleteNode(Request $request, string $box_slug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $box = Box::where('slug', $box_slug)->firstOrFail();
            $id = (int)$request->id;

            return DB::transaction(function () use ($box, $id) {
                if ($id < self::TEAM_ID_OFFSET) {
                    return $this->deleteDepartment($box, $id);
                }

                return $this->deleteTeam($box, $id);
            });
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Organization unit not found',
            ], 404);
        }
        catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'has_children' => true,
            ], 422);
        }
        catch (\Exception $e) {
            Log::error('Error deleting organization unit', [
                'box_slug' => $box_slug,
                'request' => $request->all(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to remove organization unit',
            ], 500);
        }
    }

    /**
     * Move an employee to a new reporting line or team
     *
     * @param Box $box
     * @param int $id
     * @param int $newParent
     * @param int $userId
     * @return JsonResponse
     */
    private function moveEmployee(Box $box, int $id, int $newParent, int $userId): JsonResponse
    {
        $boxUserId = $id - self::USER_ID_OFFSET;
        $boxUser = BoxUser::where('box_id', $box->id)->findOrFail($boxUserId);

        return DB::transaction(function () use ($box, $boxUser, $newParent) {
            if ($newParent >= self::USER_ID_OFFSET) {
                // Reporting to another manager
                $managerBoxUserId = $newParent - self::USER_ID_OFFSET;
                
                // Prevent self-reporting
                if ($managerBoxUserId === $boxUser->id) {
                    throw new InvalidArgumentException('Cannot report to self');
                }

                $manager = BoxUser::findOrFail($managerBoxUserId);
                $boxUser->reporting_manager_id = $manager->id;
                
                // Optional: Inherit manager's team/dept for consistency
                $boxUser->designation_id = $manager->designation_id;
                $boxUser->department_id = $manager->department_id;
            } 
            elseif ($newParent >= self::TEAM_ID_OFFSET) {
                // Moved to a team root level
                $boxUser->reporting_manager_id = null;
                $boxUser->designation_id = $newParent - self::TEAM_ID_OFFSET;
                
                $team = Team::findOrFail($boxUser->designation_id);
                $boxUser->department_id = $team->department_id;
            } 
            elseif ($newParent > 0) {
                // Moved to a department root level
                $boxUser->reporting_manager_id = null;
                $boxUser->designation_id = null;
                $boxUser->department_id = $newParent;
            }
            else {
                // Moved to Box root level? Usually not allowed for users, but let's clear relationships
                $boxUser->reporting_manager_id = null;
                $boxUser->designation_id = null;
                $boxUser->department_id = null;
            }

            $boxUser->save();
            return response()->json(['success' => true]);
        });
    }

    /**
     * Create a new department
     *
     * @param Box $box
     * @param int $userId
     * @param string $name
     * @return JsonResponse
     */
    private function createDepartment(Box $box, int $userId, string $name, bool $showIntoLeads = false, bool $showIntoAccounts = false): JsonResponse
    {
        $department = Department::create([
            'box_id' => $box->id,
            'name' => $name,
            'user_id' => $userId,
            'show_into_leads' => $showIntoLeads,
            'show_into_accounts' => $showIntoAccounts,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return response()->json([
            'id' => $department->id,
            'parent' => 0,
            'name' => $department->name,
            'show_into_leads' => $department->show_into_leads,
            'show_into_accounts' => $department->show_into_accounts,
        ]);
    }

    /**
     * Create a team under a department
     *
     * @param Box $box
     * @param int $userId
     * @param int $departmentId
     * @param string $name
     * @return JsonResponse
     */
    private function createTeamUnderDepartment(Box $box, int $userId, int $departmentId, string $name, bool $showIntoLeads = false, bool $showIntoAccounts = false): JsonResponse
    {
        Department::where('box_id', $box->id)->findOrFail($departmentId);

        $team = new Team([
            'box_id' => $box->id,
            'department_id' => $departmentId,
            'name' => $name,
            'show_into_leads' => $showIntoLeads,
            'show_into_accounts' => $showIntoAccounts,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $team->saveAsRoot();
        $team->refresh();

        // Inherit permissions: prioritize sibling root team, then fallback to department
        $siblingRootTeam = Team::where('department_id', $departmentId)
            ->whereIsRoot()
            ->where('id', '!=', $team->id)
            ->orderByDesc('id')
            ->first();

        $department = Department::find($departmentId);

        if ($siblingRootTeam && $siblingRootTeam->permissions->count() > 0) {
            $team->syncPermissions($siblingRootTeam->permissions->pluck('name')->toArray());
        }
        elseif ($department && $department->permissions->count() > 0) {
            $team->syncPermissions($department->permissions->pluck('name')->toArray());
        }

        return response()->json([
            'id' => self::TEAM_ID_OFFSET + $team->id,
            'parent' => $departmentId,
            'name' => $team->name,
            'show_into_leads' => $team->show_into_leads,
            'show_into_accounts' => $team->show_into_accounts,
        ]);
    }

    /**
     * Create a sub-team under another team
     *
     * @param Box $box
     * @param int $userId
     * @param int $parentId
     * @param string $name
     * @return JsonResponse
     */
    private function createSubTeam(Box $box, int $userId, int $parentId, string $name, bool $showIntoLeads = false, bool $showIntoAccounts = false): JsonResponse
    {
        $parentTeamId = $parentId - self::TEAM_ID_OFFSET;
        $parentTeam = Team::where('box_id', $box->id)->findOrFail($parentTeamId);

        $team = new Team([
            'box_id' => $box->id,
            'department_id' => $parentTeam->department_id,
            'name' => $name,
            'show_into_leads' => $showIntoLeads,
            'show_into_accounts' => $showIntoAccounts,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $team->appendToNode($parentTeam)->save();
        $team->refresh();

        // Inherit permissions: prioritize sibling team, then fallback to parent team
        $siblingTeam = Team::where('parent_id', $parentTeam->id)
            ->where('id', '!=', $team->id)
            ->orderByDesc('id')
            ->first();
        if ($siblingTeam && $siblingTeam->permissions->count() > 0) {
            $team->syncPermissions($siblingTeam->permissions->pluck('name')->toArray());
        }
        elseif ($parentTeam->permissions->count() > 0) {
            $team->syncPermissions($parentTeam->permissions->pluck('name')->toArray());
        }
        return response()->json([
            'id' => self::TEAM_ID_OFFSET + $team->id,
            'parent' => $parentId,
            'name' => $team->name,
            'show_into_leads' => $team->show_into_leads,
            'show_into_accounts' => $team->show_into_accounts,
        ]);
    }

    /**
     * Validate move operation to prevent circular references
     *
     * @param Box $box
     * @param Team $team
     * @param int $newParent
     * @return void
     * @throws InvalidArgumentException
     */
    private function validateMoveOperation(Box $box, Team $team, int $newParent): void
    {
        if ($newParent >= self::TEAM_ID_OFFSET) {
            $potentialParentId = $newParent - self::TEAM_ID_OFFSET;

            if ($potentialParentId === $team->id) {
                throw new InvalidArgumentException('Cannot move team to itself');
            }

            $potentialParent = Team::where('box_id', $box->id)->find($potentialParentId);

            if ($potentialParent && $this->isDescendant($potentialParent, $team)) {
                throw new InvalidArgumentException('Cannot move team to its own descendant');
            }
        }
    }

    /**
     * Check if a team is a descendant of another team
     *
     * @param Team $potentialParent
     * @param Team $team
     * @return bool
     */
    private function isDescendant(Team $potentialParent, Team $team): bool
    {
        return $potentialParent->_lft >= $team->_lft && $potentialParent->_rgt <= $team->_rgt;
    }

    /**
     * Move team to a department root level
     *
     * @param Team $team
     * @param int $departmentId
     * @param int $userId
     * @return JsonResponse
     */
    private function moveTeamToDepartment(Team $team, int $departmentId, int $userId): JsonResponse
    {
        $department = Department::where('box_id', $team->box_id)->findOrFail($departmentId);

        $team->parent_id = null;
        $team->department_id = $departmentId;
        $team->updated_by = $userId;
        $team->saveAsRoot();
        $team->refresh();

        // Inherit permissions from new location: prioritize sibling root team, then fallback to department
        $siblingRootTeam = Team::where('department_id', $departmentId)
            ->whereIsRoot()
            ->where('id', '!=', $team->id)
            ->orderByDesc('id')
            ->first();

        if ($siblingRootTeam && $siblingRootTeam->permissions->count() > 0) {
            $team->syncPermissions($siblingRootTeam->permissions->pluck('name')->toArray());
        }
        elseif ($department->permissions->count() > 0) {
            $team->syncPermissions($department->permissions->pluck('name')->toArray());
        }

        return response()->json([
            'success' => true,
            'message' => 'Organization structure updated successfully',
        ]);
    }

    /**
     * Move team under another team
     *
     * @param Box $box
     * @param Team $team
     * @param int $newParentId
     * @param int $userId
     * @return JsonResponse
     */
    private function moveTeamToTeam(Box $box, Team $team, int $newParentId, int $userId): JsonResponse
    {
        $parentTeamId = $newParentId - self::TEAM_ID_OFFSET;
        $parentTeam = Team::where('box_id', $box->id)->findOrFail($parentTeamId);

        $team->department_id = $parentTeam->department_id;
        $team->updated_by = $userId;
        $team->appendToNode($parentTeam)->save();
        $team->refresh();

        // Inherit permissions from new location: prioritize sibling team, then fallback to parent team
        $siblingTeam = Team::where('parent_id', $parentTeam->id)
            ->where('id', '!=', $team->id)
            ->orderByDesc('id')
            ->first();

        if ($siblingTeam && $siblingTeam->permissions->count() > 0) {
            $team->syncPermissions($siblingTeam->permissions->pluck('name')->toArray());
        }
        elseif ($parentTeam->permissions->count() > 0) {
            $team->syncPermissions($parentTeam->permissions->pluck('name')->toArray());
        }

        return response()->json([
            'success' => true,
            'message' => 'Organization structure updated successfully',
        ]);
    }

    /**
     * Move a department to a new parent
     *
     * @param Box $box
     * @param int $departmentId
     * @param int $newParent
     * @param int $userId
     * @return JsonResponse
     */
    private function moveDepartment(Box $box, int $departmentId, int $newParent, int $userId): JsonResponse
    {
        return DB::transaction(function () use ($box, $departmentId, $newParent, $userId) {
            $department = Department::where('box_id', $box->id)->findOrFail($departmentId);

            if ($newParent === 0) {
                $department->parent_id = null;
                $department->updated_by = $userId;
                $department->saveAsRoot();
                $department->refresh();

                // Inherit permissions from a sibling root department
                $siblingRootDept = Department::where('box_id', $box->id)
                    ->whereIsRoot()
                    ->where('id', '!=', $department->id)
                    ->orderByDesc('id')
                    ->first();

                if ($siblingRootDept && $siblingRootDept->permissions->count() > 0) {
                    $department->syncPermissions($siblingRootDept->permissions->pluck('name')->toArray());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Organization structure updated successfully',
                ]);
            }

            if ($newParent < self::TEAM_ID_OFFSET) {
                $parentDepartment = Department::where('box_id', $box->id)->findOrFail($newParent);

                if ($department->id === $parentDepartment->id) {
                    throw new InvalidArgumentException('Cannot move department to itself');
                }

                if ($this->isDepartmentDescendant($parentDepartment, $department)) {
                    throw new InvalidArgumentException('Cannot move department to its own descendant');
                }

                $department->updated_by = $userId;
                $department->appendToNode($parentDepartment)->save();
                $department->refresh();

                // Inherit permissions: prioritize sibling department, then fallback to parent department
                $siblingDept = Department::where('parent_id', $parentDepartment->id)
                    ->where('id', '!=', $department->id)
                    ->orderByDesc('id')
                    ->first();

                if ($siblingDept && $siblingDept->permissions->count() > 0) {
                    $department->syncPermissions($siblingDept->permissions->pluck('name')->toArray());
                }
                elseif ($parentDepartment->permissions->count() > 0) {
                    $department->syncPermissions($parentDepartment->permissions->pluck('name')->toArray());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Organization structure updated successfully',
                ]);
            }

            $parentTeamId = $newParent - self::TEAM_ID_OFFSET;
            Team::where('box_id', $box->id)->findOrFail($parentTeamId);

            $department->parent_id = $parentTeamId;
            $department->updated_by = $userId;
            $department->saveAsRoot();
            $department->refresh();

            // Inherit permissions: prioritize sibling department, then fallback to parent team
            $siblingRootDept = Department::where('box_id', $box->id)
                ->where('parent_id', $parentTeamId)
                ->where('id', '!=', $department->id)
                ->orderByDesc('id')
                ->first();

            if ($siblingRootDept && $siblingRootDept->permissions->count() > 0) {
                $department->syncPermissions($siblingRootDept->permissions->pluck('name')->toArray());
            }
            elseif ($parentTeam->permissions->count() > 0) {
                $department->syncPermissions($parentTeam->permissions->pluck('name')->toArray());
            }

            return response()->json([
                'success' => true,
                'message' => 'Organization structure updated successfully',
            ]);
        });
    }

    /**
     * Check if a department is a descendant of another department
     *
     * @param Department $potentialParent
     * @param Department $department
     * @return bool
     */
    private function isDepartmentDescendant(Department $potentialParent, Department $department): bool
    {
        return $potentialParent->_lft >= $department->_lft && $potentialParent->_rgt <= $department->_rgt;
    }

    /**
     * Delete a department
     *
     * @param Box $box
     * @param int $departmentId
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    private function deleteDepartment(Box $box, int $departmentId): JsonResponse
    {
        $department = Department::where('box_id', $box->id)->findOrFail($departmentId);

        $teamsCount = Team::where('box_id', $box->id)
            ->where('department_id', $departmentId)
            ->count();

        if ($teamsCount > 0) {
            throw new InvalidArgumentException(
                "Cannot remove department. It has {$teamsCount} team(s). Please remove all teams first."
                );
        }

        if ($department->permissions()->count() > 0) {
            throw new InvalidArgumentException(
                "Cannot remove department. It has permissions assigned to it."
                );
        }

        $isAssignedToUser = BoxUser::where('box_id', $box->id)
            ->where('department', $department->id)
            ->exists();

        if ($isAssignedToUser) {
            throw new InvalidArgumentException(
                "Cannot remove department. It is assigned to one or more users."
                );
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department removed successfully',
        ]);
    }

    /**
     * Delete a team
     *
     * @param Box $box
     * @param int $teamIdWithOffset
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    private function deleteTeam(Box $box, int $teamIdWithOffset): JsonResponse
    {
        $teamId = $teamIdWithOffset - self::TEAM_ID_OFFSET;
        $team = Team::where('box_id', $box->id)->findOrFail($teamId);

        $childrenCount = $team->children()->count();

        if ($childrenCount > 0) {
            throw new InvalidArgumentException(
                "Cannot remove team. It has {$childrenCount} child team(s). Please remove all child teams first."
                );
        }

        if ($team->permissions()->count() > 0) {
            throw new InvalidArgumentException(
                "Cannot remove team. It has permissions assigned to it."
                );
        }

        $isAssignedToUser = BoxUser::where('box_id', $box->id)
            ->where('designation', $team->id)
            ->exists();

        if ($isAssignedToUser) {
            throw new InvalidArgumentException(
                "Cannot remove team. It is assigned to one or more users as a designation."
                );
        }

        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team removed successfully',
        ]);
    }
}
