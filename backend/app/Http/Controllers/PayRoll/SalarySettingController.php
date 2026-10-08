<?php

namespace App\Http\Controllers\PayRoll;

use App\Helpers\SalarySettingHelper;
use App\Http\Controllers\Controller;
use App\Models\BoxUser;
use App\Models\SalarySetting;
use App\Models\SystemField;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SalarySettingController extends Controller
{
    public function getAll(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view salary settings.'
            ], 403);
        }

        if (!$boxUser) {
            return response()->json([
                'success' => false,
                'message' => 'Box user not found.',
            ], 404);
        }

        // Query from base table (standard fields)
        $salarySettings = SalarySetting::where('box_id', $boxUser->box_id)
            ->where('status', 1)
            ->select('id', 'frequency', 'salary_structure_name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $salarySettings,
        ]);
    }

    public function getDetails(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:salary_settings,id',
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view salary settings.'
            ], 403);
        }

        // Query from base table (standard fields)
        $salarySetting = SalarySetting::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->firstOrFail();

        // Get custom fields data from tenant table
        $customData = SalarySetting::getCustomFieldsData($salarySetting->id, $boxUser->box_id);
        $data = $salarySetting->toArray();
        foreach ($customData as $key => $value) {
            if (
                $key !== 'id' && $key !== 'salary_setting_id' && $key !== 'box_id' &&
                $key !== 'created_at' && $key !== 'updated_at' && $key !== 'deleted_at'
            ) {
                $data[$key] = $value;
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view salary settings.'
            ], 403);
        }

        // Clear any default ordering to avoid "name" column errors
        // Query from base table (standard fields)
        $salarySettings = SalarySetting::where('box_id', $boxUser->box_id);

        return DataTables::of($salarySettings)
            ->addColumn('status_label', function ($row) {
                return $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->rawColumns(['status_label'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canCreate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'create', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canCreate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create salary settings.'
            ], 403);
        }

        $request->validate([
            'frequency'             => 'required|string|max:255',
            'salary_structure_name' => 'required|string|max:255',
            'monthly_option'        => 'nullable|in:calendar,specific',
            'start_date'            => 'nullable',
            'end_date'              => 'nullable',
        ]);

        $data = $request->except(['_token']);

        // Handle start_date and end_date - they come as day numbers (1-31) but DB expects dates or null
        // If monthly_option is not 'specific', set them to null
        // If monthly_option is 'specific' but values are just day numbers, set to null (database expects dates)
        if ($request->monthly_option !== 'specific') {
            $data['start_date'] = null;
            $data['end_date'] = null;
        } else {
            // If specific but values are just day numbers (not proper dates), set to null
            // The database expects date format, so we'll store day numbers in a different way or set to null
            // For now, setting to null to avoid SQL errors - these can be stored as day numbers in separate fields if needed
            $data['start_date'] = null;
            $data['end_date'] = null;
        }

        // Extract component types into array (model handles JSON casting)
        $componentTypes = [];
        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_type')) {
                $field = str_replace('_type', '', $key);
                $componentTypes[$field] = $value;
                unset($data[$key]);
            }
        }

        // Validate percentage values (max 100)
        foreach ($componentTypes as $fieldName => $type) {
            if ($type === 'percentage' && isset($data[$fieldName])) {
                $value = floatval($data[$fieldName]);
                if ($value > 100) {
                    return response()->json([
                        'success' => false,
                        'message' => "Percentage value for {$fieldName} cannot exceed 100%"
                    ], 422);
                }
            }
        }

        $data['component_types'] = $componentTypes;

        // Separate static max amounts from custom field max amounts
        $customFieldsMaxAmounts = [];
        $standardFields = [
            'basic',
            'da',
            'hra',
            'conveyance',
            'allowance',
            'medical_allowance',
            'special_allowance',
            'leave_travel_allowance',
            'city_compensatory_allowance',
            'sales_target_incentives',
            'shift_allowance',
            'transport_allowance',
            'food_allowance',
            'overtime_allowance',
            'uniform_allowance',
            'mobile_internet_allowance',
            'project_allowance',
            'education_allowance',
            'professional_allowance',
            'arrears_earnings',
            'others',
            'travel_reimbursement',
            'fuel_reimbursement',
            'telephone_reimbursement',
            'driver_reimbursement',
            'medical_reimbursement',
            'uniform_reimbursement',
            'employer_pf',
            'employer_esi',
            'gratuity',
            'superannuation',
            'employer_lwf',
            'employee_pf',
            'employee_esi',
            'professional_tax',
            'tds',
            'salary_advance_recovery',
            'loan_emi_recovery',
            'canteen_deduction',
            'attendance_loss',
            'notice_period_recovery',
            'arrears_recovery',
            'insurance_premium_employee',
            'joining_bonus',
            'retention_bonus',
            'performance_bonus',
            'ex_gratia',
            'full_final_settlement',
            'leave_encashment',
            'gratuity_payout',
            'severance_compensation'
        ];

        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_max')) {
                $fieldName = str_replace('_max', '', $key);

                // Check if the field type is fixed - if so, set max to 0/null
                $fieldType = $componentTypes[$fieldName] ?? null;
                if ($fieldType === 'fixed') {
                    $convertedValue = 0;
                } else {
                    $convertedValue = ($value === '' || $value === null) ? 0 : floatval($value);
                }

                if (in_array($fieldName, $standardFields)) {
                    // Static field - save to dedicated column
                    $data[$key] = $convertedValue;
                } else {
                    // Custom field - store in JSON
                    $customFieldsMaxAmounts[$fieldName] = $convertedValue;
                    unset($data[$key]); // Remove from data array
                }
            }
        }

        // Store custom fields max amounts in JSON column
        if (!empty($customFieldsMaxAmounts)) {
            $data['custom_fields_max_amounts'] = $customFieldsMaxAmounts;
        }

        $data['box_id']      = $boxUser->box_id;
        $data['box_user_id'] = $boxUser->id;
        $data['created_by']  = $user->id;
        $data['status']      = $request->has('status') ? 1 : 0;

        // Separate standard fields from custom fields
        // IMPORTANT: Custom field max amounts need to be added back to $data
        // so they can be saved to the tenant table as {fieldName}_max columns
        // They are stored in JSON for base table, but also need to be in tenant table
        foreach ($customFieldsMaxAmounts as $fieldName => $maxAmount) {
            $data[$fieldName . '_max'] = $maxAmount;
        }

        $standardFields = [
            'id',
            'box_id',
            'box_user_id',
            'frequency',
            'salary_structure_name',
            'comp_off_type',
            'monthly_option',
            'start_date',
            'end_date',
            'status',
            'basic',
            'da',
            'hra',
            'conveyance',
            'allowance',
            'medical_allowance',
            'special_allowance',
            'leave_travel_allowance',
            'city_compensatory_allowance',
            'sales_target_incentives',
            'shift_allowance',
            'transport_allowance',
            'food_allowance',
            'overtime_allowance',
            'uniform_allowance',
            'mobile_internet_allowance',
            'project_allowance',
            'education_allowance',
            'professional_allowance',
            'arrears_earnings',
            'others',
            'travel_reimbursement',
            'fuel_reimbursement',
            'telephone_reimbursement',
            'driver_reimbursement',
            'medical_reimbursement',
            'uniform_reimbursement',
            'employer_pf',
            'employer_esi',
            'gratuity',
            'superannuation',
            'employer_lwf',
            'employee_pf',
            'employee_esi',
            'professional_tax',
            'tds',
            'salary_advance_recovery',
            'loan_emi_recovery',
            'canteen_deduction',
            'attendance_loss',
            'notice_period_recovery',
            'arrears_recovery',
            'insurance_premium_employee',
            'joining_bonus',
            'retention_bonus',
            'performance_bonus',
            'ex_gratia',
            'full_final_settlement',
            'leave_encashment',
            'gratuity_payout',
            'severance_compensation',
            'variable_amount',
            'variable_amount_type',
            'variable_amount_max',
            'variable_label',
            'component_types',
            'custom_fields_max_amounts',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        // Get standard field base names (without _type or _max)
        $standardFieldBaseNames = SalarySettingHelper::getStandardFieldNames();

        // Get custom field names for this box
        $customFieldNames = \App\Models\BoxCustomField::where('box_id', $boxUser->box_id)
            ->where('object', 'like', 'salary_%')
            ->pluck('col_name')
            ->toArray();

        $standardData = [];
        $customData = [];

        // First, add custom field _type values to customData from componentTypes
        // These were extracted earlier but need to be saved to tenant table
        foreach ($componentTypes as $fieldName => $type) {
            if (in_array($fieldName, $customFieldNames)) {
                // This is a custom field type - add it to customData
                $customData[$fieldName . '_type'] = $type;
            }
        }

        foreach ($data as $key => $value) {
            // Get base key (without _type or _max suffix)
            $baseKey = str_replace('_type', '', str_replace('_max', '', $key));

            // First, check if it's definitely a standard field
            $isStandardField = (
                in_array($baseKey, $standardFieldBaseNames) ||
                in_array($key, $standardFields) ||
                (str_ends_with($key, '_max') && in_array($baseKey, $standardFieldBaseNames)) ||
                (str_ends_with($key, '_type') && in_array($baseKey, $standardFieldBaseNames))
            );

            if ($isStandardField) {
                $standardData[$key] = $value;
            } else {
                // Not a standard field - check if it's a known custom field
                $isCustomField = (
                    in_array($key, $customFieldNames) ||
                    in_array($baseKey, $customFieldNames) ||
                    (str_ends_with($key, '_type') && in_array($baseKey, $customFieldNames)) ||
                    (str_ends_with($key, '_max') && in_array($baseKey, $customFieldNames))
                );

                if ($isCustomField) {
                    // This is a known custom field
                    $customData[$key] = $value;
                } else {
                    // Unknown field - if it's not in standard fields list, treat as custom
                    if (!in_array($baseKey, $standardFieldBaseNames) && !in_array($key, $standardFields)) {
                        $customData[$key] = $value;
                    } else {
                        // Shouldn't happen, but skip unexpected fields
                        continue;
                    }
                }
            }
        }

        // Filter standardData to only include fields that actually exist in the base table
        $baseTableColumns = \Illuminate\Support\Facades\Schema::getColumnListing('salary_settings');
        $filteredStandardData = [];
        foreach ($standardData as $key => $value) {
            if (in_array($key, $baseTableColumns)) {
                $filteredStandardData[$key] = $value;
            } else {
                // Field doesn't exist in base table, move to custom data
                $customData[$key] = $value;
            }
        }

        // Save standard fields to base table
        $salarySetting = SalarySetting::create($filteredStandardData);

        // Save custom fields to tenant-specific table
        if (!empty($customData)) {
            SalarySetting::saveCustomFieldsData($salarySetting->id, $boxUser->box_id, $customData);
        }

        return response()->json(['success' => 'Salary structure created successfully!']);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:salary_settings,id',
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'update', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit salary settings.'
            ], 403);
        }

        // Query from base table (standard fields)
        $salarySetting = SalarySetting::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->firstOrFail();

        // Get custom fields data from tenant table
        $customData = SalarySetting::getCustomFieldsData($salarySetting->id, $boxUser->box_id);
        $data = $salarySetting->toArray();

        // Merge custom fields data
        foreach ($customData as $key => $value) {
            if (
                $key !== 'id' && $key !== 'salary_setting_id' && $key !== 'box_id' &&
                $key !== 'created_at' && $key !== 'updated_at' && $key !== 'deleted_at'
            ) {
                $data[$key] = $value;
            }
        }

        // Expand custom_fields_max_amounts JSON into individual fields for frontend
        if (!empty($data['custom_fields_max_amounts']) && is_array($data['custom_fields_max_amounts'])) {
            foreach ($data['custom_fields_max_amounts'] as $fieldName => $maxAmount) {
                $data[$fieldName . '_max'] = $maxAmount;
            }
        }

        // Return data directly (not wrapped) for edit form compatibility
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'                    => 'required|integer',
            'frequency'             => 'required|string|max:255',
            'salary_structure_name' => 'required|string|max:255',
            'monthly_option'        => 'nullable|in:calendar,specific',
            'start_date'            => 'nullable',
            'end_date'              => 'nullable',
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canUpdate = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'update', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update salary settings.'
            ], 403);
        }
        // Query from base table (standard fields)
        $salarySetting = SalarySetting::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->firstOrFail();

        // Get custom fields data from tenant table (for reference only, don't merge into model)
        $existingCustomData = SalarySetting::getCustomFieldsData($salarySetting->id, $boxUser->box_id);

        $data = $request->except(['_token', 'id']);

        // Handle start_date and end_date - they come as day numbers (1-31) but DB expects dates or null
        // If monthly_option is not 'specific', set them to null
        // If monthly_option is 'specific' but values are just day numbers, set to null (database expects dates)
        if ($request->monthly_option !== 'specific') {
            $data['start_date'] = null;
            $data['end_date'] = null;
        } else {
            // If specific but values are just day numbers (not proper dates), set to null
            // The database expects date format, so we'll store day numbers in a different way or set to null
            // For now, setting to null to avoid SQL errors - these can be stored as day numbers in separate fields if needed
            $data['start_date'] = null;
            $data['end_date'] = null;
        }

        // Extract component types into array (model handles JSON casting)
        $componentTypes = [];
        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_type')) {
                $field = str_replace('_type', '', $key);
                $componentTypes[$field] = $value;
                unset($data[$key]);
            }
        }

        // Validate percentage values (max 100)
        foreach ($componentTypes as $fieldName => $type) {
            if ($type === 'percentage' && isset($data[$fieldName])) {
                $value = floatval($data[$fieldName]);
                if ($value > 100) {
                    return response()->json([
                        'success' => false,
                        'message' => "Percentage value for {$fieldName} cannot exceed 100%"
                    ], 422);
                }
            }
        }

        $data['component_types'] = $componentTypes;

        // Separate static max amounts from custom field max amounts
        $customFieldsMaxAmounts = [];
        $staticMaxAmounts = [];

        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_max')) {
                $fieldName = str_replace('_max', '', $key);

                // If field type is fixed, set max amount to 0 (clear it when switching from percentage to fixed)
                $fieldType = $componentTypes[$fieldName] ?? null;
                if ($fieldType === 'fixed') {
                    $convertedValue = 0;
                } else {
                    $convertedValue = ($value === '' || $value === null) ? 0 : floatval($value);
                }

                // Check if this is a custom field (not in standard fields list)
                $standardFields = [
                    'basic',
                    'da',
                    'hra',
                    'conveyance',
                    'allowance',
                    'medical_allowance',
                    'special_allowance',
                    'leave_travel_allowance',
                    'city_compensatory_allowance',
                    'sales_target_incentives',
                    'shift_allowance',
                    'transport_allowance',
                    'food_allowance',
                    'overtime_allowance',
                    'uniform_allowance',
                    'mobile_internet_allowance',
                    'project_allowance',
                    'education_allowance',
                    'professional_allowance',
                    'arrears_earnings',
                    'others',
                    'travel_reimbursement',
                    'fuel_reimbursement',
                    'telephone_reimbursement',
                    'driver_reimbursement',
                    'medical_reimbursement',
                    'uniform_reimbursement',
                    'employer_pf',
                    'employer_esi',
                    'gratuity',
                    'superannuation',
                    'employer_lwf',
                    'employee_pf',
                    'employee_esi',
                    'professional_tax',
                    'tds',
                    'salary_advance_recovery',
                    'loan_emi_recovery',
                    'canteen_deduction',
                    'attendance_loss',
                    'notice_period_recovery',
                    'arrears_recovery',
                    'insurance_premium_employee',
                    'joining_bonus',
                    'retention_bonus',
                    'performance_bonus',
                    'ex_gratia',
                    'full_final_settlement',
                    'leave_encashment',
                    'gratuity_payout',
                    'severance_compensation'
                ];

                if (in_array($fieldName, $standardFields)) {
                    // Static field - save to dedicated column
                    $staticMaxAmounts[$key] = $convertedValue;
                    $data[$key] = $convertedValue;
                } else {
                    // Custom field - store in JSON
                    $customFieldsMaxAmounts[$fieldName] = $convertedValue;
                    unset($data[$key]); // Remove from data array
                }
            }
        }

        // Store custom fields max amounts in JSON column (for base table)
        if (!empty($customFieldsMaxAmounts)) {
            $data['custom_fields_max_amounts'] = $customFieldsMaxAmounts;
        } elseif (isset($data['custom_fields_max_amounts']) && empty($customFieldsMaxAmounts)) {
            // If no custom max amounts, set to empty array
            $data['custom_fields_max_amounts'] = [];
        }

        $data['updated_by'] = $user->id;
        $data['status']     = $request->has('status') ? 1 : 0;

        // Separate standard fields from custom fields
        // IMPORTANT: Custom field max amounts need to be added back to $data
        // so they can be saved to the tenant table as {fieldName}_max columns
        // They are stored in JSON for base table, but also need to be in tenant table
        foreach ($customFieldsMaxAmounts as $fieldName => $maxAmount) {
            $data[$fieldName . '_max'] = $maxAmount;
        }
        $standardFields = [
            'id',
            'box_id',
            'box_user_id',
            'frequency',
            'salary_structure_name',
            'comp_off_type',
            'monthly_option',
            'start_date',
            'end_date',
            'status',
            'basic',
            'da',
            'hra',
            'conveyance',
            'allowance',
            'medical_allowance',
            'special_allowance',
            'leave_travel_allowance',
            'city_compensatory_allowance',
            'sales_target_incentives',
            'shift_allowance',
            'transport_allowance',
            'food_allowance',
            'overtime_allowance',
            'uniform_allowance',
            'mobile_internet_allowance',
            'project_allowance',
            'education_allowance',
            'professional_allowance',
            'arrears_earnings',
            'others',
            'travel_reimbursement',
            'fuel_reimbursement',
            'telephone_reimbursement',
            'driver_reimbursement',
            'medical_reimbursement',
            'uniform_reimbursement',
            'employer_pf',
            'employer_esi',
            'gratuity',
            'superannuation',
            'employer_lwf',
            'employee_pf',
            'employee_esi',
            'professional_tax',
            'tds',
            'salary_advance_recovery',
            'loan_emi_recovery',
            'canteen_deduction',
            'attendance_loss',
            'notice_period_recovery',
            'arrears_recovery',
            'insurance_premium_employee',
            'joining_bonus',
            'retention_bonus',
            'performance_bonus',
            'ex_gratia',
            'full_final_settlement',
            'leave_encashment',
            'gratuity_payout',
            'severance_compensation',
            'variable_amount',
            'variable_amount_type',
            'variable_amount_max',
            'variable_label',
            'component_types',
            'custom_fields_max_amounts',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        // Get standard field base names (without _type or _max)
        $standardFieldBaseNames = SalarySettingHelper::getStandardFieldNames();

        // Get custom field names for this box
        $customFieldNames = \App\Models\BoxCustomField::where('box_id', $boxUser->box_id)
            ->where('object', 'like', 'salary_%')
            ->pluck('col_name')
            ->toArray();

        // Separate standard and custom data
        $standardData = [];
        $customData = [];

        // First, add custom field _type values to customData from componentTypes
        // These were extracted earlier but need to be saved to tenant table
        foreach ($componentTypes as $fieldName => $type) {
            if (in_array($fieldName, $customFieldNames)) {
                // This is a custom field type - add it to customData
                $customData[$fieldName . '_type'] = $type;
            }
        }

        foreach ($data as $key => $value) {
            // Get base key (without _type or _max suffix)
            $baseKey = str_replace('_type', '', str_replace('_max', '', $key));

            // First, check if it's definitely a standard field
            $isStandardField = (
                in_array($baseKey, $standardFieldBaseNames) ||
                in_array($key, $standardFields) ||
                (str_ends_with($key, '_max') && in_array($baseKey, $standardFieldBaseNames)) ||
                (str_ends_with($key, '_type') && in_array($baseKey, $standardFieldBaseNames))
            );

            if ($isStandardField) {
                $standardData[$key] = $value;
            } else {
                // Not a standard field - check if it's a known custom field
                $isCustomField = (
                    in_array($key, $customFieldNames) ||
                    in_array($baseKey, $customFieldNames) ||
                    (str_ends_with($key, '_type') && in_array($baseKey, $customFieldNames)) ||
                    (str_ends_with($key, '_max') && in_array($baseKey, $customFieldNames))
                );

                if ($isCustomField) {
                    // This is a known custom field
                    $customData[$key] = $value;
                } else {
                    // Unknown field - if it's not in standard fields list, treat as custom
                    // This handles deleted custom fields that still have data
                    if (!in_array($baseKey, $standardFieldBaseNames) && !in_array($key, $standardFields)) {
                        $customData[$key] = $value;
                    } else {
                        // Shouldn't happen, but if it does, skip it to avoid errors
                        // Log or handle unexpected fields
                        continue;
                    }
                }
            }
        }

        // Filter standardData to only include fields that actually exist in the base table
        // Get the fillable/guarded attributes or check against actual table columns
        $baseTableColumns = \Illuminate\Support\Facades\Schema::getColumnListing('salary_settings');
        $filteredStandardData = [];
        foreach ($standardData as $key => $value) {
            if (in_array($key, $baseTableColumns)) {
                $filteredStandardData[$key] = $value;
            } else {
                // Field doesn't exist in base table, move to custom data
                $customData[$key] = $value;
            }
        }

        // Clear any attributes that might have been set on the model that aren't in filteredStandardData
        // This prevents custom fields from being included in the update
        $modelAttributes = $salarySetting->getAttributes();
        foreach ($modelAttributes as $key => $value) {
            if (!in_array($key, $baseTableColumns) && !in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                unset($salarySetting->$key);
            }
        }

        // Update standard fields in base table using fill() and save() for better control
        $salarySetting->fill($filteredStandardData);
        $salarySetting->save();

        // Save custom fields to tenant-specific table
        if (!empty($customData)) {
            SalarySetting::saveCustomFieldsData($salarySetting->id, $boxUser->box_id, $customData);
        }

        return response()->json(['success' => 'Salary structure updated successfully!']);
    }

    public function calculateSalary(Request $request)
    {
        $request->validate([
            'ctc'               => 'required|numeric|min:0',
            'period'            => 'required|in:monthly,yearly',
            'salary_setting_id' => 'required_without:salary_settings_snapshot|integer',
            'salary_settings_snapshot' => 'sometimes|array', // Optional: can pass snapshot directly
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canRead = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'read', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canRead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to calculate salary.'
            ], 403);
        }

        $salarySetting = null;

        // Priority 1: Use snapshot if provided (for viewing existing salary)
        $customData = null;
        if ($request->has('salary_settings_snapshot') && is_array($request->salary_settings_snapshot)) {
            // Create a mock SalarySetting object from snapshot
            $snapshot = $request->salary_settings_snapshot;

            // Store snapshot for later use (contains all custom fields)
            $customData = $snapshot;

            // Remove metadata and definitions from snapshot for setting object
            $snapshotForSetting = $snapshot;
            unset($snapshotForSetting['_snapshot_metadata'], $snapshotForSetting['custom_fields_definitions']);

            // Create a SalarySetting instance from snapshot data
            $salarySetting = new SalarySetting();
            foreach ($snapshotForSetting as $key => $value) {
                if (!in_array($key, ['id', 'salary_setting_id', 'box_id', 'created_at', 'updated_at', 'deleted_at'])) {
                    $salarySetting->$key = $value;
                }
            }

            // Ensure component_types and custom_fields_max_amounts are properly set
            if (isset($snapshot['component_types'])) {
                $salarySetting->component_types = is_array($snapshot['component_types'])
                    ? $snapshot['component_types']
                    : json_decode($snapshot['component_types'], true) ?? [];
            }
            if (isset($snapshot['custom_fields_max_amounts'])) {
                $salarySetting->custom_fields_max_amounts = is_array($snapshot['custom_fields_max_amounts'])
                    ? $snapshot['custom_fields_max_amounts']
                    : json_decode($snapshot['custom_fields_max_amounts'], true) ?? [];
            }
        }
        // Priority 2: Fetch from database
        elseif ($request->salary_setting_id) {
            $salarySetting = SalarySetting::where('id', $request->salary_setting_id)
                ->where('box_id', $boxUser->box_id)
                ->firstOrFail();

            // Get custom fields data from tenant table
            $customData = SalarySetting::getCustomFieldsData($salarySetting->id, $boxUser->box_id);

            // Merge custom data into salary setting for calculation
            foreach ($customData as $key => $value) {
                if (
                    $key !== 'id' && $key !== 'salary_setting_id' && $key !== 'box_id' &&
                    $key !== 'created_at' && $key !== 'updated_at' && $key !== 'deleted_at'
                ) {
                    $salarySetting->$key = $value;
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Either salary_setting_id or salary_settings_snapshot must be provided.',
            ], 400);
        }

        /* =============================
       CTC NORMALIZATION
    ============================= */
        $ctc    = floatval($request->ctc);
        $period = strtolower(trim($request->period));

        // IMPORTANT: CTC input is ALWAYS treated as YEARLY amount
        // When "monthly" period is selected, it means: divide yearly CTC by 12 to get monthly
        // When "yearly" period is selected, it means: use yearly CTC as is
        // ALL calculations (Basic, DA, HRA, etc.) are done on monthly basis
        $yearlyCtc  = $ctc;           // CTC input is always yearly
        $monthlyCtc = $ctc / 12;      // Always divide by 12 to get monthly CTC for calculations
        /* =============================
       COMPONENT TYPES
    ============================= */
        $componentTypes = $salarySetting->component_types ?? [];

        if (is_string($componentTypes)) {
            $componentTypes = json_decode($componentTypes, true) ?? [];
        }

        // Merge custom field types from custom data or snapshot (they are stored as {field_name}_type)
        $dataSource = $customData ?? [];
        foreach ($dataSource as $key => $value) {
            if (str_ends_with($key, '_type') && $key !== 'variable_amount_type' && $key !== '_snapshot_metadata' && $key !== 'custom_fields_definitions') {
                $fieldName = str_replace('_type', '', $key);
                if ($value !== null && $value !== '') {
                    $componentTypes[$fieldName] = strtolower(trim($value));
                }
            }
        }

        $componentTypes = array_map(fn($v) => strtolower(trim($v)), $componentTypes);

        /* =============================
       GET CUSTOM FIELDS
    ============================= */
        $customFieldDefs = \App\Models\BoxCustomField::where('box_id', $boxUser->box_id)
            ->where('object', 'like', 'salary_%')
            ->get();

        // Get custom fields max amounts from JSON column
        $customFieldsMaxAmounts = $salarySetting->custom_fields_max_amounts ?? [];
        if (is_string($customFieldsMaxAmounts)) {
            $customFieldsMaxAmounts = json_decode($customFieldsMaxAmounts, true) ?? [];
        }

        // Get attributes for value checking (needed before custom fields loop)
        // If using snapshot, get attributes from snapshot; otherwise merge custom data
        if ($customData && $request->has('salary_settings_snapshot')) {
            // Use snapshot data directly (exclude metadata)
            $attributes = $customData;
            unset($attributes['_snapshot_metadata'], $attributes['custom_fields_definitions']);
        } else {
            // Merge custom data into attributes so custom fields are accessible
            $attributes = array_merge($salarySetting->getAttributes(), $customData ?? []);
        }

        /* =============================
       RESULT STRUCTURE
    ============================= */
        $results = [
            'earnings'      => [],
            'reimbursements' => [],
            'contributions' => [],
            'deductions'    => [],
            'onetime'       => [],
            'totals'        => [
                'monthly_gross'                  => 0,
                'yearly_gross'                   => 0,
                'monthly_deductions'             => 0,
                'yearly_deductions'              => 0,
                'monthly_employer_contributions' => 0,
                'yearly_employer_contributions'  => 0,
                'monthly_net'                    => 0,
                'yearly_net'                     => 0,
            ],
            'metadata'      => [
                'input_ctc'    => $ctc,
                'input_period' => $period,
                // monthly_ctc and yearly_ctc will be calculated later as Gross + Contributions
            ],
        ];

        /* =============================
       FIELD GROUPS (STANDARD FIELDS)
    ============================= */
        $earningsFields = [
            'basic',
            'da',
            'hra',
            'conveyance',
            'allowance',
            'medical_allowance',
            'special_allowance',
            'leave_travel_allowance',
            'city_compensatory_allowance',
            'sales_target_incentives',
            'shift_allowance',
            'transport_allowance',
            'food_allowance',
            'overtime_allowance',
            'uniform_allowance',
            'mobile_internet_allowance',
            'project_allowance',
            'education_allowance',
            'professional_allowance',
            'arrears_earnings',
            'others'
        ];

        $contributionsFields = ['employer_pf', 'employer_esi', 'gratuity', 'superannuation', 'employer_lwf'];
        $deductionsFields    = ['employee_pf', 'employee_esi', 'professional_tax', 'tds', 'salary_advance_recovery', 'loan_emi_recovery', 'canteen_deduction', 'attendance_loss', 'notice_period_recovery', 'arrears_recovery', 'insurance_premium_employee'];
        $reimbursementsFields = ['travel_reimbursement', 'fuel_reimbursement', 'telephone_reimbursement', 'driver_reimbursement', 'medical_reimbursement', 'uniform_reimbursement'];
        $onetimeFields = ['joining_bonus', 'retention_bonus', 'performance_bonus', 'ex_gratia', 'full_final_settlement', 'leave_encashment', 'gratuity_payout', 'severance_compensation'];

        /* =============================
       ADD CUSTOM FIELDS TO APPROPRIATE GROUPS
    ============================= */
        foreach ($customFieldDefs as $customField) {
            $fieldName = $customField->col_name;
            $objectType = $customField->object;

            // Check if this custom field has a value or type defined
            $hasValue = isset($attributes[$fieldName]) && ($attributes[$fieldName] !== null && $attributes[$fieldName] !== '');
            $hasType = isset($componentTypes[$fieldName]);

            if ($hasValue || $hasType) {
                switch ($objectType) {
                    case 'salary_earnings':
                        if (!in_array($fieldName, $earningsFields)) {
                            $earningsFields[] = $fieldName;
                        }
                        break;
                    case 'salary_reimbursements':
                        if (!in_array($fieldName, $reimbursementsFields)) {
                            $reimbursementsFields[] = $fieldName;
                        }
                        break;
                    case 'salary_contributions':
                        if (!in_array($fieldName, $contributionsFields)) {
                            $contributionsFields[] = $fieldName;
                        }
                        break;
                    case 'salary_deductions':
                        if (!in_array($fieldName, $deductionsFields)) {
                            $deductionsFields[] = $fieldName;
                        }
                        break;
                    case 'salary_onetime':
                        if (!in_array($fieldName, $onetimeFields)) {
                            $onetimeFields[] = $fieldName;
                        }
                        break;
                }
            }
        }

        /* =============================
       VALUE HELPER
    ============================= */
        $valueOf = fn($f) => isset($attributes[$f]) && $attributes[$f] !== null && $attributes[$f] !== '' ? floatval($attributes[$f]) : 0;

        /* =============================
       MAX AMOUNT HELPER
       Gets max amount for BOTH standard fields and custom fields

       Sources checked (in order):
       1. Standard fields: {fieldName}_max column in base table (via $attributes)
       2. Custom fields: {fieldName}_max column in tenant table (via $attributes, merged from $customData)
       3. Custom fields: custom_fields_max_amounts JSON column in base table
    ============================= */
        $maxAmountOf = function ($fieldName) use ($attributes, $customFieldsMaxAmounts) {
            $maxKey = $fieldName . '_max';

            // Check 1: Standard fields OR custom fields with _max column in tenant table
            // Both are accessible via $attributes (customData is merged into attributes)
            // Example: employee_pf_max, basic_max (standard) OR advance_max (custom from tenant table)
            if (isset($attributes[$maxKey])) {
                $maxValue = $attributes[$maxKey];
                // Handle null, empty string, or 0
                if ($maxValue !== null && $maxValue !== '' && $maxValue !== '0') {
                    $maxValue = floatval($maxValue);
                    if ($maxValue > 0) {
                        return $maxValue;
                    }
                }
            }

            // Check 2: Custom fields max amounts from JSON column in base table
            // Example: {"new_test": 1255, "advance": 1500}
            if (isset($customFieldsMaxAmounts[$fieldName])) {
                $maxValue = $customFieldsMaxAmounts[$fieldName];
                if ($maxValue !== null && $maxValue !== '' && $maxValue !== '0') {
                    $maxValue = floatval($maxValue);
                    if ($maxValue > 0) {
                        return $maxValue;
                    }
                }
            }

            return null; // No max amount set for this field
        };

        /* =============================
       CALCULATE FIELD AMOUNT WITH MAX LIMIT HELPER
       This function applies max amount limits to ALL fields:
       - Standard fields (basic, da, hra, employee_pf, etc.)
       - Custom fields (new_test, advance, etc.)

       Examples:
       - Employee PF: 12% = ₹22,000 but max is ₹12,000 → returns ₹12,000
       - Custom field "new_test": 10% = ₹18,333 but max is ₹1,255 → returns ₹1,255
    ============================= */
        $calculateFieldAmount = function ($fieldName, $monthlyCtc) use ($valueOf, $componentTypes, $maxAmountOf) {
            $val = $valueOf($fieldName);
            $type = $componentTypes[$fieldName] ?? 'fixed';

            // Calculate amount based on type
            $calculatedAmount = 0;
            if ($type === 'percentage') {
                // Percentage: Calculate from CTC
                // Example: 12% of ₹1,83,333 (monthly CTC) = ₹22,000
                // Example: Custom field "new_test" 10% of ₹1,83,333 = ₹18,333
                $calculatedAmount = ($monthlyCtc * $val) / 100;
            } else {
                // Fixed: Use value directly from database
                // Example: Fixed amount of ₹5,000
                $calculatedAmount = $val;
            }

            // Apply max amount limit if set (for both percentage and fixed types)
            // This ensures calculated amount never exceeds the max limit
            // Works for BOTH standard fields (from _max columns) and custom fields (from JSON)
            $maxAmount = $maxAmountOf($fieldName);
            if ($maxAmount !== null && $maxAmount > 0) {
                if ($calculatedAmount > $maxAmount) {
                    // Cap the amount at max limit
                    // Standard field example: ₹22,000 > ₹12,000 (employee_pf_max) → return ₹12,000
                    // Custom field example: ₹18,333 > ₹1,255 (new_test max from JSON) → return ₹1,255
                    $calculatedAmount = $maxAmount;
                }
            }

            return $calculatedAmount;
        };

        /* =============================
       BASIC SALARY
    ============================= */
        $basicValue = $valueOf('basic');
        $basicType  = $componentTypes['basic'] ?? 'fixed';

        $basicAmount = $basicType === 'percentage'
            ? ($monthlyCtc * $basicValue) / 100
            : $basicValue;

        // Apply max amount limit if set
        $basicMaxAmount = $maxAmountOf('basic');
        if ($basicMaxAmount !== null && $basicAmount > $basicMaxAmount) {
            $basicAmount = $basicMaxAmount;
        }

        $results['earnings']['basic'] = [
            'monthly' => round($basicAmount, 2),
            'yearly'  => round($basicAmount * 12, 2)
        ];

        $results['totals']['monthly_gross'] += $basicAmount;
        $results['totals']['yearly_gross']  += $basicAmount * 12;

        /* =============================
       OTHER EARNINGS
    ============================= */
        foreach ($earningsFields as $field) {
            if ($field === 'basic') continue; // Basic already calculated

            $amount = $calculateFieldAmount($field, $monthlyCtc);

            if ($amount > 0) {
                $monthlyAmount = round($amount, 2);
                $yearlyAmount = round($amount * 12, 2);

                $results['earnings'][$field] = [
                    'monthly' => $monthlyAmount,
                    'yearly'  => $yearlyAmount,
                ];
                $results['totals']['monthly_gross'] += $monthlyAmount;
                $results['totals']['yearly_gross']  += $yearlyAmount;
            }
        }

        /* =============================
       EMPLOYER CONTRIBUTIONS
    ============================= */
        foreach ($contributionsFields as $field) {
            $amount = $calculateFieldAmount($field, $monthlyCtc);

            if ($amount > 0) {
                $monthlyAmount = round($amount, 2);
                $yearlyAmount = round($amount * 12, 2);

                $results['contributions'][$field] = [
                    'monthly' => $monthlyAmount,
                    'yearly'  => $yearlyAmount,
                ];
                $results['totals']['monthly_employer_contributions'] += $monthlyAmount;
                $results['totals']['yearly_employer_contributions']  += $yearlyAmount;
            }
        }

        /* =============================
       VERIFY CTC BALANCE
       Check if Monthly Gross + Monthly Contributions = Monthly CTC
       Note: special_allowance is included as a normal earnings field from database
    ============================= */
        $finalGross = $results['totals']['monthly_gross'];
        $finalContributions = $results['totals']['monthly_employer_contributions'];
        $finalTotal = $finalGross + $finalContributions;
        $difference = round($monthlyCtc - $finalTotal, 2);

        // Flag if total doesn't equal CTC (for information purposes)
        if (abs($difference) > 0.01) {
            $results['metadata']['ctc_balance_difference'] = $difference;
            $results['metadata']['ctc_balanced'] = false;
        } else {
            $results['metadata']['ctc_balanced'] = true;
        }

        /* =============================
       DEDUCTIONS
    ============================= */
        foreach ($deductionsFields as $field) {
            $amount = $calculateFieldAmount($field, $monthlyCtc);

            if ($amount > 0) {
                $monthlyAmount = round($amount, 2);
                $yearlyAmount = round($amount * 12, 2);

                $results['deductions'][$field] = [
                    'monthly' => $monthlyAmount,
                    'yearly'  => $yearlyAmount,
                ];
                $results['totals']['monthly_deductions'] += $monthlyAmount;
                $results['totals']['yearly_deductions']  += $yearlyAmount;
            }
        }

        /* =============================
       REIMBURSEMENTS
    ============================= */
        foreach ($reimbursementsFields as $field) {
            $amount = $calculateFieldAmount($field, $monthlyCtc);

            if ($amount > 0) {
                $monthlyAmount = round($amount, 2);
                $yearlyAmount = round($amount * 12, 2);

                $results['reimbursements'][$field] = [
                    'monthly' => $monthlyAmount,
                    'yearly'  => $yearlyAmount,
                ];
                $results['totals']['monthly_gross'] += $monthlyAmount;
                $results['totals']['yearly_gross']  += $yearlyAmount;
            }
        }

        /* =============================
       ONE-TIME COMPONENTS
    ============================= */
        foreach ($onetimeFields as $field) {
            $amount = $calculateFieldAmount($field, $monthlyCtc);

            if ($amount > 0) {
                $monthlyAmount = round($amount, 2);
                $yearlyAmount = round($amount * 12, 2);

                $results['onetime'][$field] = [
                    'monthly' => $monthlyAmount,
                    'yearly'  => $yearlyAmount,
                ];
            }
        }

        /* =============================
       CALCULATE SECTION TOTALS
       (After auto-balance to ensure all adjustments are included)
    ============================= */
        // Total Earnings (sum of all earnings including adjusted special_allowance)
        $results['totals']['monthly_total_earnings'] = 0;
        $results['totals']['yearly_total_earnings'] = 0;
        foreach ($results['earnings'] as $earning) {
            $results['totals']['monthly_total_earnings'] += $earning['monthly'];
            $results['totals']['yearly_total_earnings'] += $earning['yearly'];
        }
        $results['totals']['monthly_total_earnings'] = round($results['totals']['monthly_total_earnings'], 2);
        $results['totals']['yearly_total_earnings'] = round($results['totals']['yearly_total_earnings'], 2);

        // Total Reimbursements
        $results['totals']['monthly_total_reimbursements'] = 0;
        $results['totals']['yearly_total_reimbursements'] = 0;
        foreach ($results['reimbursements'] as $reimbursement) {
            $results['totals']['monthly_total_reimbursements'] += $reimbursement['monthly'];
            $results['totals']['yearly_total_reimbursements'] += $reimbursement['yearly'];
        }
        $results['totals']['monthly_total_reimbursements'] = round($results['totals']['monthly_total_reimbursements'], 2);
        $results['totals']['yearly_total_reimbursements'] = round($results['totals']['yearly_total_reimbursements'], 2);

        // Total Contributions (already calculated, but ensure it's there)
        $results['totals']['monthly_total_contributions'] = $results['totals']['monthly_employer_contributions'];
        $results['totals']['yearly_total_contributions'] = $results['totals']['yearly_employer_contributions'];

        // Total Deductions (already calculated)
        $results['totals']['monthly_total_deductions'] = $results['totals']['monthly_deductions'];
        $results['totals']['yearly_total_deductions'] = $results['totals']['yearly_deductions'];

        // Total One-Time
        $results['totals']['monthly_total_onetime'] = 0;
        $results['totals']['yearly_total_onetime'] = 0;
        foreach ($results['onetime'] as $onetime) {
            $results['totals']['monthly_total_onetime'] += $onetime['monthly'];
            $results['totals']['yearly_total_onetime'] += $onetime['yearly'];
        }
        $results['totals']['monthly_total_onetime'] = round($results['totals']['monthly_total_onetime'], 2);
        $results['totals']['yearly_total_onetime'] = round($results['totals']['yearly_total_onetime'], 2);

        // Verify monthly_gross = monthly_total_earnings + monthly_total_reimbursements
        $calculatedMonthlyGross = $results['totals']['monthly_total_earnings'] + $results['totals']['monthly_total_reimbursements'];
        if (abs($calculatedMonthlyGross - $results['totals']['monthly_gross']) > 0.01) {
            // Adjust if there's a discrepancy (should not happen, but just in case)
            $results['totals']['monthly_gross'] = round($calculatedMonthlyGross, 2);
        }

        /* =============================
       NET SALARY
    ============================= */
        $results['totals']['monthly_net'] =
            round($results['totals']['monthly_gross'] - $results['totals']['monthly_deductions'], 2);

        // Ensure yearly totals are exactly 12 times monthly totals for consistency
        $results['totals']['yearly_gross'] = round($results['totals']['monthly_gross'] * 12, 2);
        $results['totals']['yearly_total_earnings'] = round($results['totals']['monthly_total_earnings'] * 12, 2);
        $results['totals']['yearly_total_reimbursements'] = round($results['totals']['monthly_total_reimbursements'] * 12, 2);
        $results['totals']['yearly_employer_contributions'] = round($results['totals']['monthly_employer_contributions'] * 12, 2);
        $results['totals']['yearly_deductions'] = round($results['totals']['monthly_deductions'] * 12, 2);
        $results['totals']['yearly_net'] = round($results['totals']['monthly_net'] * 12, 2);

        // Add basic calculation details to metadata
        $results['metadata']['basic_amount'] = round($basicAmount, 2);
        $results['metadata']['basic_type']   = $basicType;
        $results['metadata']['basic_value']  = $basicValue;

        // Calculate CTC as: Monthly Gross + Monthly Contributions
        // This is the actual calculated CTC based on salary components
        $calculatedMonthlyCtc = round($results['totals']['monthly_gross'] + $results['totals']['monthly_employer_contributions'], 2);
        $calculatedYearlyCtc = round($calculatedMonthlyCtc * 12, 2);

        // Store calculated CTC in metadata (this is what should be displayed)
        $results['metadata']['monthly_ctc'] = $calculatedMonthlyCtc;
        $results['metadata']['yearly_ctc'] = $calculatedYearlyCtc;

        // Also store the input CTC for reference
        $results['metadata']['input_monthly_ctc'] = round($monthlyCtc, 2);
        $results['metadata']['input_yearly_ctc'] = round($yearlyCtc, 2);

        // Verify CTC = Gross + Contributions (should always be true)
        $results['metadata']['monthly_gross_plus_contributions'] = $calculatedMonthlyCtc;
        $results['metadata']['yearly_gross_plus_contributions'] = $calculatedYearlyCtc;

        // Add salary_setting_id for form population
        $results['metadata']['salary_setting_id'] = $salarySetting->id;
        $results['metadata']['input_ctc_value'] = $ctc;
        $results['metadata']['input_period'] = $period;

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }



    // public function calculateSalary(Request $request)
    // {
    //     $request->validate([
    //         'ctc'                => 'required|numeric|min:0',
    //         'period'             => 'required|in:monthly,yearly',
    //         'salary_setting_id'  => 'required|integer|exists:salary_settings,id',
    //     ]);

    //     $user    = Auth::id();
    //     $boxUser = BoxUser::where('user_id', $user)->firstOrFail();

    //     $salarySetting = SalarySetting::where('id', $request->salary_setting_id)
    //         ->where('box_id', $boxUser->box_id)
    //         ->firstOrFail();

    //     $ctc = floatval($request->ctc);
    //     $period = $request->period;

    //     // Get component types - ensure it's an array
    //     $componentTypes = $salarySetting->component_types ?? [];
    //     if (is_string($componentTypes)) {
    //         $componentTypes = json_decode($componentTypes, true) ?? [];
    //     }
    //     if (is_array($componentTypes)) {
    //         $componentTypes = array_map(function ($type) {
    //             return is_string($type) ? strtolower(trim($type)) : $type;
    //         }, $componentTypes);
    //     } else {
    //         $componentTypes = [];
    //     }

    //     // Convert CTC to monthly basis for all calculations
    //     // If user enters yearly CTC (e.g., 600000), divide by 12 to get monthly (50000)
    //     // If user enters monthly CTC (e.g., 50000), use it directly
    //     // ALL calculations (Basic, DA, HRA, etc.) are done on monthly basis
    //     if ($period === 'yearly') {
    //         $monthlyCtc = $ctc / 12;  // Yearly CTC divided by 12 months = Monthly CTC
    //         $yearlyCtc = $ctc;        // Yearly CTC as entered
    //     } else {
    //         $monthlyCtc = $ctc;       // Monthly CTC as entered
    //         $yearlyCtc = $ctc * 12;   // Monthly CTC multiplied by 12 = Yearly CTC
    //     }

    //     $results = [
    //         'earnings'      => [],
    //         'reimbursements' => [],
    //         'contributions' => [],
    //         'deductions'    => [],
    //         'onetime'       => [],
    //         'totals'        => [
    //             'monthly_gross'      => 0,
    //             'yearly_gross'       => 0,
    //             'monthly_deductions' => 0,
    //             'yearly_deductions'  => 0,
    //             'monthly_net'        => 0,
    //             'yearly_net'         => 0,
    //         ],
    //     ];

    //     // Define all salary components (Standard Fields matches Blade file)
    //     $earningsFields = ['basic', 'da', 'hra', 'conveyance', 'allowance', 'medical_allowance', 'special_allowance', 'leave_travel_allowance', 'city_compensatory_allowance', 'sales_target_incentives', 'shift_allowance', 'transport_allowance', 'food_allowance', 'overtime_allowance', 'uniform_allowance', 'mobile_internet_allowance', 'project_allowance', 'education_allowance', 'professional_allowance', 'arrears_earnings', 'others'];
    //     $reimbursementsFields = ['travel_reimbursement', 'fuel_reimbursement', 'telephone_reimbursement', 'driver_reimbursement', 'medical_reimbursement', 'uniform_reimbursement'];
    //     $contributionsFields = ['employer_pf', 'employer_esi', 'gratuity', 'superannuation', 'employer_lwf'];
    //     $deductionsFields = ['employee_pf', 'employee_esi', 'professional_tax', 'tds', 'salary_advance_recovery', 'loan_emi_recovery', 'canteen_deduction', 'attendance_loss', 'notice_period_recovery', 'arrears_recovery', 'insurance_premium_employee'];
    //     $onetimeFields = ['joining_bonus', 'retention_bonus', 'performance_bonus', 'ex_gratia', 'full_final_settlement', 'leave_encashment', 'gratuity_payout', 'severance_compensation'];

    //     // Dynamic Field Handling: Identify and categorize custom fields
    //     $allConfiguredFields = array_keys($componentTypes);
    //     // Create a flat list of all standard fields to compare against
    //     $allStandardFields = array_merge($earningsFields, $reimbursementsFields, $contributionsFields, $deductionsFields, $onetimeFields);
    //     // Find fields that are in component_types but not in our standard lists
    //     $extraFields = array_diff($allConfiguredFields, $allStandardFields);

    //     if (!empty($extraFields)) {
    //         // Fetch metadata for these fields from SystemField to determine their category (object)
    //         $systemFields = SystemField::where('box_id', $boxUser->box_id)
    //             ->whereIn('field_name', $extraFields)
    //             ->get();

    //         foreach ($systemFields as $sf) {
    //             $fieldName = $sf->field_name;
    //             // Add to the appropriate array based on the 'object' column
    //             switch ($sf->object) {
    //                 case 'salary_earnings':
    //                     if (!in_array($fieldName, $earningsFields)) $earningsFields[] = $fieldName;
    //                     break;
    //                 case 'salary_reimbursements':
    //                     if (!in_array($fieldName, $reimbursementsFields)) $reimbursementsFields[] = $fieldName;
    //                     break;
    //                 case 'salary_contributions':
    //                     if (!in_array($fieldName, $contributionsFields)) $contributionsFields[] = $fieldName;
    //                     break;
    //                 case 'salary_deductions':
    //                     if (!in_array($fieldName, $deductionsFields)) $deductionsFields[] = $fieldName;
    //                     break;
    //                 case 'salary_onetime':
    //                     if (!in_array($fieldName, $onetimeFields)) $onetimeFields[] = $fieldName;
    //                     break;
    //             }
    //         }

    //         // Fallback: If any extra fields were not found in SystemField (e.g. legacy or manual DB inserts),
    //         // assume they are Earnings by default so they are at least calculated.
    //         foreach ($extraFields as $extra) {
    //             $found = false;
    //             foreach ($systemFields as $sf) {
    //                 if ($sf->field_name === $extra) {
    //                     $found = true;
    //                     break;
    //                 }
    //             }
    //             if (!$found) {
    //                 if (!in_array($extra, $earningsFields)) $earningsFields[] = $extra;
    //             }
    //         }
    //     }

    //     // Get all attributes from the model (including all columns)
    //     $allAttributes = $salarySetting->getAttributes();

    //     // Helper function to get field value from salary setting (handles both old and new column names)
    //     $getFieldValue = function ($fieldName) use ($salarySetting, $allAttributes) {
    //         // Try new column name first - check in attributes array
    //         if (array_key_exists($fieldName, $allAttributes)) {
    //             $value = $allAttributes[$fieldName];
    //             if ($value !== null && $value !== '') {
    //                 return floatval($value);
    //             }
    //         }

    //         // Also try direct attribute access
    //         if (isset($salarySetting->$fieldName) && $salarySetting->$fieldName !== null && $salarySetting->$fieldName !== '') {
    //             return floatval($salarySetting->$fieldName);
    //         }

    //         // Try old column name mappings if new name doesn't exist
    //         $oldNameMap = [
    //             'basic' => 'basic_salary',
    //             'da' => 'dearness_allowance',
    //             'hra' => 'house_rent_allowance',
    //             'conveyance' => 'conveyance_allowance',
    //             'fuel_reimbursement' => 'petrol_reimbursement',
    //             'driver_reimbursement' => 'driver_salary_reimbursement',
    //             'uniform_reimbursement' => 'uniform_equipment_reimbursement',
    //             'employer_lwf' => 'lwf_employer',
    //             'canteen_deduction' => 'food_canteen_deduction',
    //             'attendance_loss' => 'late_coming_loss',
    //             'insurance_premium_employee' => 'insurance_premium',
    //             'performance_bonus' => 'performance_bonus_one_time',
    //             'severance_compensation' => 'severance_pay',
    //         ];

    //         if (isset($oldNameMap[$fieldName])) {
    //             $oldName = $oldNameMap[$fieldName];
    //             // Check in attributes array
    //             if (array_key_exists($oldName, $allAttributes)) {
    //                 $value = $allAttributes[$oldName];
    //                 if ($value !== null && $value !== '') {
    //                     return floatval($value);
    //                 }
    //             }
    //             // Also try direct access
    //             if (isset($salarySetting->$oldName) && $salarySetting->$oldName !== null && $salarySetting->$oldName !== '') {
    //                 return floatval($salarySetting->$oldName);
    //             }
    //         }

    //         return 0;
    //     };

    //     // Calculate Basic first (needed for percentage calculations)
    //     // IMPORTANT: All calculations are done on MONTHLY basis
    //     // If user entered yearly CTC (600000), monthlyCtc = 50000 (600000/12)
    //     // Basic is calculated from monthly CTC
    //     $basicAmount = 0;
    //     $basicValue = $getFieldValue('basic');
    //     $basicType = $componentTypes['basic'] ?? 'fixed';

    //     if ($basicValue > 0 || isset($componentTypes['basic'])) {
    //         if ($basicType === 'percentage') {
    //             // Basic as percentage of monthly CTC
    //             // Example: If monthlyCtc = 50000 and basicValue = 20%, then basicAmount = 10000
    //             $basicAmount = ($monthlyCtc * $basicValue) / 100;
    //         } else {
    //             // Basic as fixed amount (monthly) - use value directly from database
    //             $basicAmount = $basicValue;
    //         }
    //     } else {
    //         // If basic is not set, default to 0
    //         $basicAmount = 0;
    //     }

    //     // Always include Basic in earnings (even if 0)
    //     if ($basicAmount > 0) {
    //         $results['earnings']['basic'] = [
    //             'monthly' => round($basicAmount, 2),
    //             'yearly'  => round($basicAmount * 12, 2),
    //         ];
    //         $results['totals']['monthly_gross'] += $basicAmount;
    //         $results['totals']['yearly_gross'] += ($basicAmount * 12);
    //     }

    //     // Calculate Earnings (excluding basic as it's already calculated)
    //     foreach ($earningsFields as $field) {
    //         // Skip basic as it's already calculated above
    //         if ($field === 'basic') {
    //             continue;
    //         }

    //         $value = $getFieldValue($field);
    //         $type = isset($componentTypes[$field]) ? strtolower(trim($componentTypes[$field])) : 'fixed';
    //         $calculatedValue = 0;

    //         // Calculate if component type is defined in component_types
    //         // All calculations are on MONTHLY basis
    //         if (isset($componentTypes[$field])) {
    //             if ($type === 'percentage') {
    //                 // Percentage components are calculated from Basic (monthly)
    //                 // Example: If basicAmount = 120000 and value = 15% (HRA), then calculatedValue = 18000
    //                 // value here is the percentage number (e.g., 15 means 15%)
    //                 if ($basicAmount > 0 && $value > 0) {
    //                     $calculatedValue = ($basicAmount * $value) / 100;
    //                 }
    //             } else {
    //                 // Fixed amount components - use the value from database (already monthly)
    //                 $calculatedValue = $value;
    //             }
    //         } elseif ($value > 0) {
    //             // If not in component_types but has a value, treat as fixed (monthly)
    //             $calculatedValue = $value;
    //         }

    //         // Include in results if calculated value is greater than 0
    //         if ($calculatedValue > 0) {
    //             $results['earnings'][$field] = [
    //                 'monthly' => round($calculatedValue, 2),
    //                 'yearly'  => round($calculatedValue * 12, 2),
    //             ];
    //             $results['totals']['monthly_gross'] += $calculatedValue;
    //             $results['totals']['yearly_gross'] += ($calculatedValue * 12);
    //         }
    //     }

    //     // Calculate Reimbursements
    //     foreach ($reimbursementsFields as $field) {
    //         $value = $getFieldValue($field);
    //         $type = $componentTypes[$field] ?? 'fixed';
    //         $calculatedValue = 0;

    //         if (isset($componentTypes[$field])) {
    //             if ($type === 'percentage') {
    //                 $calculatedValue = ($basicAmount * $value) / 100;
    //             } else {
    //                 $calculatedValue = $value;
    //             }
    //         } elseif ($value > 0) {
    //             $calculatedValue = $value;
    //         }

    //         if ($calculatedValue > 0) {
    //             $results['reimbursements'][$field] = [
    //                 'monthly' => round($calculatedValue, 2),
    //                 'yearly'  => round($calculatedValue * 12, 2),
    //             ];
    //             $results['totals']['monthly_gross'] += $calculatedValue;
    //             $results['totals']['yearly_gross'] += ($calculatedValue * 12);
    //         }
    //     }

    //     // Calculate Contributions (employer side - not deducted from employee)
    //     foreach ($contributionsFields as $field) {
    //         $value = $getFieldValue($field);
    //         $type = $componentTypes[$field] ?? 'fixed';
    //         $calculatedValue = 0;

    //         if (isset($componentTypes[$field])) {
    //             if ($type === 'percentage') {
    //                 $calculatedValue = ($basicAmount * $value) / 100;
    //             } else {
    //                 $calculatedValue = $value;
    //             }
    //         } elseif ($value > 0) {
    //             $calculatedValue = $value;
    //         }

    //         if ($calculatedValue > 0) {
    //             $results['contributions'][$field] = [
    //                 'monthly' => round($calculatedValue, 2),
    //                 'yearly'  => round($calculatedValue * 12, 2),
    //             ];
    //         }
    //     }

    //     // Calculate Deductions
    //     foreach ($deductionsFields as $field) {
    //         $value = $getFieldValue($field);
    //         $type = $componentTypes[$field] ?? 'fixed';
    //         $calculatedValue = 0;

    //         if (isset($componentTypes[$field])) {
    //             if ($type === 'percentage') {
    //                 $calculatedValue = ($basicAmount * $value) / 100;
    //             } else {
    //                 $calculatedValue = $value;
    //             }
    //         } elseif ($value > 0) {
    //             $calculatedValue = $value;
    //         }

    //         if ($calculatedValue > 0) {
    //             $results['deductions'][$field] = [
    //                 'monthly' => round($calculatedValue, 2),
    //                 'yearly'  => round($calculatedValue * 12, 2),
    //             ];
    //             $results['totals']['monthly_deductions'] += $calculatedValue;
    //             $results['totals']['yearly_deductions'] += ($calculatedValue * 12);
    //         }
    //     }

    //     // Calculate One-Time
    //     foreach ($onetimeFields as $field) {
    //         $value = $getFieldValue($field);
    //         $type = $componentTypes[$field] ?? 'fixed';
    //         $calculatedValue = 0;

    //         if (isset($componentTypes[$field])) {
    //             if ($type === 'percentage') {
    //                 $calculatedValue = ($basicAmount * $value) / 100;
    //             } else {
    //                 $calculatedValue = $value;
    //             }
    //         } elseif ($value > 0) {
    //             $calculatedValue = $value;
    //         }

    //         if ($calculatedValue > 0) {
    //             $results['onetime'][$field] = [
    //                 'monthly' => round($calculatedValue, 2),
    //                 'yearly'  => round($calculatedValue * 12, 2),
    //             ];
    //         }
    //     }

    //     // Calculate Net Salary
    //     $results['totals']['monthly_net'] = round($results['totals']['monthly_gross'] - $results['totals']['monthly_deductions'], 2);
    //     $results['totals']['yearly_net'] = round($results['totals']['yearly_gross'] - $results['totals']['yearly_deductions'], 2);
    //     $results['totals']['monthly_gross'] = round($results['totals']['monthly_gross'], 2);
    //     $results['totals']['yearly_gross'] = round($results['totals']['yearly_gross'], 2);
    //     $results['totals']['monthly_deductions'] = round($results['totals']['monthly_deductions'], 2);
    //     $results['totals']['yearly_deductions'] = round($results['totals']['yearly_deductions'], 2);

    //     // Add calculation metadata for debugging
    //     $results['metadata'] = [
    //         'input_ctc' => $ctc,
    //         'input_period' => $period,
    //         'monthly_ctc' => round($monthlyCtc, 2),
    //         'yearly_ctc' => round($yearlyCtc, 2),
    //         'basic_amount' => round($basicAmount, 2),
    //         'basic_type' => $basicType,
    //         'basic_value' => $basicValue,
    //     ];

    //     return response()->json([
    //         'success' => true,
    //         'data'   => $results,
    //     ]);
    // }

    /**
     * Check for custom fields before deletion
     */
    public function checkDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:salary_settings,id',
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'delete', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete salary settings.'
            ], 403);
        }

        // Query from base table (standard fields)
        $salarySetting = SalarySetting::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->firstOrFail();

        // Get custom fields data from tenant table
        $customData = SalarySetting::getCustomFieldsData($salarySetting->id, $boxUser->box_id);

        // Get custom field definitions for this box
        $customFieldDefs = \App\Models\BoxCustomField::where('box_id', $boxUser->box_id)
            ->where('object', 'like', 'salary_%')
            ->get();

        // Extract custom field names that have data
        $customFieldsWithData = [];
        $hasData = false;

        foreach ($customData as $key => $value) {
            // Skip system fields
            if (in_array($key, ['id', 'salary_setting_id', 'box_id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            // Get base field name (without _type or _max)
            $baseKey = str_replace('_type', '', str_replace('_max', '', $key));

            // Check if this is a custom field (not standard)
            $standardFieldNames = SalarySettingHelper::getStandardFieldNames();
            if (!in_array($baseKey, $standardFieldNames)) {
                // This is a custom field
                if (!isset($customFieldsWithData[$baseKey])) {
                    $fieldDef = $customFieldDefs->firstWhere('col_name', $baseKey);
                    $customFieldsWithData[$baseKey] = [
                        'name' => $fieldDef ? $fieldDef->name : $baseKey,
                        'col_name' => $baseKey,
                        'has_value' => false,
                        'has_type' => false,
                        'has_max' => false
                    ];
                }

                // Check if field has actual data (not null, not 0, not empty string)
                if ($key === $baseKey) {
                    if ($value !== null && $value !== '' && $value !== 0) {
                        $customFieldsWithData[$baseKey]['has_value'] = true;
                        $hasData = true;
                    }
                } elseif (str_ends_with($key, '_type')) {
                    $customFieldsWithData[$baseKey]['has_type'] = true;
                } elseif (str_ends_with($key, '_max')) {
                    if ($value !== null && $value !== '' && $value !== 0) {
                        $customFieldsWithData[$baseKey]['has_max'] = true;
                        $hasData = true;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'has_custom_fields' => !empty($customFieldsWithData),
            'has_data' => $hasData,
            'custom_fields' => array_values($customFieldsWithData),
            'salary_structure_name' => $salarySetting->salary_structure_name
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:salary_settings,id',
            'force_delete' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
        $boxUser = BoxUser::where('user_id', $user->id)->firstOrFail();
        $isBoxAdmin = $boxUser && strtolower($boxUser->staff_role ?? '') === 'admin';
        $canDelete = \App\Helpers\PermissionHelper::hasModulePermission('payroll', 'delete', 'setting');

        if (!$isGlobalAdmin && !$isBoxAdmin && !$canDelete) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete salary settings.'
            ], 403);
        }

        // Query from base table (standard fields)
        $salarySetting = SalarySetting::where('id', $request->id)
            ->where('box_id', $boxUser->box_id)
            ->firstOrFail();

        $forceDelete = $request->has('force_delete') && $request->force_delete;

        // If force delete, remove custom field columns
        if ($forceDelete) {
            // Get custom fields data from tenant table
            $customData = SalarySetting::getCustomFieldsData($salarySetting->id, $boxUser->box_id);

            // Get custom field definitions for this box
            $customFieldDefs = \App\Models\BoxCustomField::where('box_id', $boxUser->box_id)
                ->where('object', 'like', 'salary_%')
                ->get();

            // Extract unique custom field base names
            $customFieldNames = [];
            foreach ($customData as $key => $value) {
                $baseKey = str_replace('_type', '', str_replace('_max', '', $key));
                $standardFieldNames = SalarySettingHelper::getStandardFieldNames();
                if (!in_array($baseKey, $standardFieldNames)) {
                    $customFieldNames[$baseKey] = true;
                }
            }

            // Remove custom field columns from tenant table
            foreach (array_keys($customFieldNames) as $fieldName) {
                // Remove main field
                SalarySettingHelper::removeCustomColumn($boxUser->box_id, $fieldName);
                // Remove _type field
                SalarySettingHelper::removeCustomColumn($boxUser->box_id, $fieldName . '_type');
                // Remove _max field
                SalarySettingHelper::removeCustomColumn($boxUser->box_id, $fieldName . '_max');
            }
        }

        // Delete the salary setting (this will cascade delete custom fields data due to foreign key)
        $salarySetting->delete();

        return response()->json(['success' => 'Salary setting deleted successfully!']);
    }
}
