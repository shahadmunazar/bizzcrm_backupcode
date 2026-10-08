<?php
namespace App\Http\Controllers\Api;
use App\Helpers\BoxPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermissionController extends Controller
{
    /**
     * Get current user's permissions comprehensively
     */
    public function getUserPermissions(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['permissions' => []]);
        }

        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json(['permissions' => []]);
        }

        // 1. Admin shortcut
        if (strtolower($boxUser->staff_role ?? '') === 'admin' || $user->hasRole('admin', 'web') || $user->id == 1) {
            return response()->json([
                'permissions' => ['all.permissions', 'is_admin']
            ]);
        }

        // 2. Aggregate all permissions
        $userPermissions = $user->permissions()->pluck('name')->toArray();
        $userPermissions = array_merge($userPermissions, $user->getPermissionsViaRoles()->pluck('name')->toArray());
        
        if ($boxUser->departmentRelation) {
            $userPermissions = array_merge($userPermissions, $boxUser->departmentRelation->permissions()->pluck('name')->toArray());
        }
        if ($boxUser->designationRelation) {
            $userPermissions = array_merge($userPermissions, $boxUser->designationRelation->permissions()->pluck('name')->toArray());
        }

        $permissions = array_values(array_unique($userPermissions));

        return response()->json([
            'permissions' => $permissions
        ]);
    }
}