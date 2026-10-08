<?php

namespace App\Http\Controllers;

use App\Helpers\BoxPermissionHelper;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    /**
     * Check if the authenticated user has a specific permission
     * 
     * @param string $permission
     * @return bool
     */
    protected function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return false;
        }

        // Check if user is admin (via role or staff_role) - admins have all permissions
        if (strtolower($boxUser->staff_role ?? '') === 'admin' || $user->hasRole('admin', 'web')) {
            return true;
        }

        return BoxPermissionHelper::boxUserHasPermission($boxUser, $permission);
    }

    /**
     * Check if the authenticated user has any of the given permissions
     * 
     * @param array $permissions
     * @return bool
     */
    protected function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the authenticated user has all of the given permissions
     * 
     * @param array $permissions
     * @return bool
     */
    protected function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Abort if user doesn't have permission
     * 
     * @param string $permission
     * @param string $message
     * @return void
     */
    protected function requirePermission(string $permission, string $message = 'You do not have permission to perform this action.'): void
    {
        if (!$this->hasPermission($permission)) {
            abort(403, $message);
        }
    }
}

