<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) return $next($request);

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $boxUser = \App\Models\BoxUser::where('user_id', $user->id)->first();
            $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';

            $hasAllowanceTypes = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_allowance_types', 'read');
            $hasExpenseCategories = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_expense_categories', 'read');
            $hasApprovalRules = \App\Helpers\PermissionHelper::hasModulePermission('finance_settings_approval_rules', 'read');

            if (!($isGlobalAdmin || $isBoxAdmin || $hasAllowanceTypes || $hasExpenseCategories || $hasApprovalRules)) {
                abort(403, 'You do not have permission to access this module.');
            }

            return $next($request);
        });

    }

    public function index(Request $request, $box_slug)
    {
        $box = \App\Models\Box::where('slug', $box_slug)->firstOrFail();
        return view('finance.settings.index', compact('box'));
    }
}
