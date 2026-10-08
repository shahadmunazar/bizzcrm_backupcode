<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\BoxPermissionHelper;
use App\Http\Controllers\BaseController;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermissionController extends BaseController
{
    /**
     * Get current user's permissions
     */
    public function getUserPermissions(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'permissions' => []
            ]);
        }

        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json([
                'permissions' => []
            ]);
        }

        // Get DIRECT permissions for this user (not box-scoped)
        $user = $boxUser->user;
        $directPermissions = $user ? $user->permissions->pluck('id')->toArray() : [];

        // Get INHERITED permissions
        $inheritedPermissions = [];
        
        // Merge Department Permissions
        if ($boxUser->departmentRelation) {
            $deptPerms = $boxUser->departmentRelation->permissions->pluck('id')->toArray();
            $inheritedPermissions = array_unique(array_merge($inheritedPermissions, $deptPerms));
        }

        // Merge Designation (Team) Permissions
        if ($boxUser->designationRelation) {
            $teamPerms = $boxUser->designationRelation->permissions->pluck('id')->toArray();
            $inheritedPermissions = array_unique(array_merge($inheritedPermissions, $teamPerms));
        }
        
        // ... check admin status logic ...
        $isAdmin = false;
        if (strtolower($boxUser->staff_role ?? '') === 'admin') {
            $isAdmin = true;
        } elseif ($user && $user->hasRole('admin', 'web')) {
            $isAdmin = true;
        }

        return response()->json([
            'permissions' => [
                'direct' => $directPermissions,
                'inherited' => $inheritedPermissions
            ],
            'is_admin' => $isAdmin,
            'staff_role' => $boxUser->staff_role ?? null,
        ]);
    }
}

