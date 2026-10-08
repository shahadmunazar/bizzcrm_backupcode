<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class SalarySettingHelper
{
    /**
     * Get the table name for a specific box_id
     *
     * @param int $boxId
     * @return string
     */
    public static function getTableName($boxId)
    {
        return "salary_settings_box_{$boxId}";
    }

    /**
     * Ensure the tenant-specific table exists with minimal structure
     * This table only stores custom fields, not standard fields
     *
     * @param int $boxId
     * @return bool
     */
    public static function ensureTableExists($boxId)
    {
        $tableName = self::getTableName($boxId);
        
        if (!Schema::hasTable($tableName)) {
            try {
                Schema::create($tableName, function ($table) {
                    // Minimal structure - only linking fields
                    // Standard fields are in base salary_settings table
                    $table->id();
                    $table->unsignedBigInteger('salary_setting_id')->unique();
                    $table->unsignedBigInteger('box_id');
                    
                    // Timestamps
                    $table->timestamps();
                    $table->softDeletes();
                    
                    // Add indexes
                    $table->index('salary_setting_id');
                    $table->index('box_id');
                    
                    // Foreign key to base salary_settings table
                    $table->foreign('salary_setting_id')
                        ->references('id')
                        ->on('salary_settings')
                        ->onDelete('cascade');
                });
                
                return true;
            } catch (\Exception $e) {
                // If table creation fails (e.g., already exists), just return false
                // This can happen in race conditions
                if (str_contains($e->getMessage(), 'already exists') || 
                    str_contains($e->getMessage(), 'Duplicate')) {
                    return false;
                }
                throw $e;
            }
        }
        
        return false;
    }

    /**
     * Add a custom column to the tenant-specific table
     *
     * @param int $boxId
     * @param string $columnName
     * @param string $type
     * @return bool
     */
    public static function addCustomColumn($boxId, $columnName, $type)
    {
        $tableName = self::getTableName($boxId);
        self::ensureTableExists($boxId);
        $added = false;
        
        if (!Schema::hasColumn($tableName, $columnName)) {
            try {
                Schema::table($tableName, function ($table) use ($columnName, $type) {
                    match (strtolower($type)) {
                        'number' => $table->decimal($columnName, 15, 2)->default(0)->nullable(),
                        'date' => $table->date($columnName)->nullable(),
                        default => $table->string($columnName)->nullable(),
                    };
                });
                $added = true;
            } catch (\Exception $e) {
                // Handle duplicate column or index errors gracefully
                if (str_contains($e->getMessage(), 'Duplicate column') || 
                    str_contains($e->getMessage(), 'already exists') ||
                    str_contains($e->getMessage(), 'Duplicate key')) {
                    // Column or index already exists, that's fine - continue
                } else {
                    throw $e;
                }
            }
        }
        
        if (strtolower($type) === 'number') {
            $typeColumn = $columnName . '_type';
            $maxColumn = $columnName . '_max';

            if (!Schema::hasColumn($tableName, $typeColumn)) {
                try {
                    Schema::table($tableName, function ($table) use ($typeColumn) {
                        $table->string($typeColumn, 20)->default('fixed')->nullable();
                    });
                    $added = true;
                } catch (\Exception $e) {
                    if (!str_contains($e->getMessage(), 'Duplicate column') && 
                        !str_contains($e->getMessage(), 'already exists') &&
                        !str_contains($e->getMessage(), 'Duplicate key')) {
                        throw $e;
                    }
                }
            }

            if (!Schema::hasColumn($tableName, $maxColumn)) {
                try {
                    Schema::table($tableName, function ($table) use ($maxColumn) {
                        $table->decimal($maxColumn, 15, 2)->default(0)->nullable();
                    });
                    $added = true;
                } catch (\Exception $e) {
                    if (!str_contains($e->getMessage(), 'Duplicate column') && 
                        !str_contains($e->getMessage(), 'already exists') &&
                        !str_contains($e->getMessage(), 'Duplicate key')) {
                        throw $e;
                    }
                }
            }
        }

        return $added;
    }

    /**
     * Remove a custom column from the tenant-specific table
     *
     * @param int $boxId
     * @param string $columnName
     * @return bool
     */
    public static function removeCustomColumn($boxId, $columnName)
    {
        $tableName = self::getTableName($boxId);

        if (!Schema::hasTable($tableName)) {
            return false;
        }

        if (Schema::hasColumn($tableName, $columnName)) {
            // Protect only linking/system fields (tenant table only has custom fields + linking fields)
            $protectedFields = [
                'id',
                'salary_setting_id',
                'box_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ];

            if (in_array($columnName, $protectedFields)) {
                return false; // Don't remove system/linking fields
            }

            try {
                // All other fields in tenant table are custom fields, safe to remove
                Schema::table($tableName, function ($table) use ($columnName) {
                    $table->dropColumn($columnName);
                });

                return true;
            } catch (\Exception $e) {
                // Handle errors gracefully (e.g., column doesn't exist, foreign key constraints)
                if (str_contains($e->getMessage(), 'Unknown column') ||
                    str_contains($e->getMessage(), "doesn't exist")) {
                    return false;
                }
                throw $e;
            }
        }

        return false;
    }

    /**
     * Get all standard field names (base names without _type or _max)
     *
     * @return array
     */
    public static function getStandardFieldNames()
    {
        return [
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
            // Earnings
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
            // Reimbursements
            'travel_reimbursement',
            'fuel_reimbursement',
            'telephone_reimbursement',
            'driver_reimbursement',
            'medical_reimbursement',
            'uniform_reimbursement',
            // Contributions
            'employer_pf',
            'employer_esi',
            'gratuity',
            'superannuation',
            'employer_lwf',
            'employee_pf',
            'employee_esi',
            'professional_tax',
            'tds',
            // Deductions
            'salary_advance_recovery',
            'loan_emi_recovery',
            'canteen_deduction',
            'attendance_loss',
            'notice_period_recovery',
            'arrears_recovery',
            'insurance_premium_employee',
            // One-Time
            'joining_bonus',
            'retention_bonus',
            'performance_bonus',
            'ex_gratia',
            'full_final_settlement',
            'leave_encashment',
            'gratuity_payout',
            'severance_compensation',
            // Variable
            'variable_amount',
            'variable_amount_type',
            'variable_amount_max',
            'variable_label',
            // Metadata
            'component_types',
            'custom_fields_max_amounts',
            // System fields
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
    }
}
