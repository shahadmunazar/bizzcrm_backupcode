<?php

namespace App\Helpers;

use App\Models\BoxUser;
use Illuminate\Support\Facades\Auth;

class BoxPermissionHelper
{
    /**
     * Check if the authenticated user (via BoxUser) has a specific permission for their box
     * Also checks for "all.permissions" master permission
     *
     * @param string $permissionName
     * @param int|null $boxId Optional box_id, defaults to user's box_id
     * @return bool
     */
    public static function hasPermission(string $permissionName, ?int $boxId = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) return false;

        return self::boxUserHasPermission($boxUser, $permissionName);
    }

    /**
     * Check if the BoxUser has a specific permission for their box
     * Also checks for "all.permissions" master permission
     *
     * @param BoxUser $boxUser
     * @param string $permissionName
     * @param int|null $boxId Optional box_id, defaults to boxUser's box_id
     * @return bool
     */
    public static function boxUserHasPermission(BoxUser $boxUser, string $permissionName, ?int $boxId = null): bool
    {
        // 0. Admin Check (Global Role or Staff Role)
        if (strtolower($boxUser->staff_role ?? '') === 'admin') return true;
        
        $user = $boxUser->user;
        if (!$user) return false;
        
        if ($user->hasRole('admin', 'web') || $user->id == 1) return true;
        
        // 1. Aggregate all permissions (Union model)
        $userPermissions = $user->permissions()->pluck('name')->toArray();
        $userPermissions = array_merge($userPermissions, $user->getPermissionsViaRoles()->pluck('name')->toArray());
        
        if ($boxUser->departmentRelation) {
            $userPermissions = array_merge($userPermissions, $boxUser->departmentRelation->permissions()->pluck('name')->toArray());
        }
        if ($boxUser->designationRelation) {
            $userPermissions = array_merge($userPermissions, $boxUser->designationRelation->permissions()->pluck('name')->toArray());
        }
        $userPermissions = array_unique($userPermissions);

        if (in_array('all.permissions', $userPermissions)) {
            return true;
        }
        
        // Check specific permission
        if (in_array($permissionName, $userPermissions)) {
            return true;
        }

        // Check module-level "all" or specific action fallback
        $parts = explode('.', $permissionName);
        if (count($parts) >= 2) {
            $module = $parts[0];
            if (in_array("{$module}.all", $userPermissions) || in_array("{$module}.read", $userPermissions)) {
                // Warning: module.read should only fallback for check-only purposes, 
                // but since this is box-level check, we follow the established pattern.
                if (in_array("{$module}.all", $userPermissions)) return true;
                
                // If checking for read, module.read is enough
                if ($parts[1] === 'read' && in_array("{$module}.read", $userPermissions)) return true;
            }
        }

        return false;
    }

    /**
     * Get all permissions for a BoxUser scoped by their box_id
     *
     * @param BoxUser $boxUser
     * @return \Illuminate\Support\Collection
     */
    public static function getBoxUserPermissions(BoxUser $boxUser)
    {
        $user = $boxUser->user;
        if (!$user) {
            return collect([]);
        }
        return $user->permissions;
    }
}

