<?php
namespace App\Models;

use App\Helpers\SalarySettingHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SalarySetting extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'salary_settings'; // Default table (for backward compatibility)

    protected $guarded = [];

    protected $casts = [
        'component_types' => 'array',
        'custom_fields_max_amounts' => 'array',
    ];

    /**
     * Get custom fields data from tenant-specific table
     * 
     * @param int $salarySettingId
     * @param int $boxId
     * @return array
     */
    public static function getCustomFieldsData($salarySettingId, $boxId)
    {
        $tableName = SalarySettingHelper::getTableName($boxId);
        
        if (!Schema::hasTable($tableName)) {
            return [];
        }
        
        $customData = DB::table($tableName)
            ->where('salary_setting_id', $salarySettingId)
            ->first();
            
        return $customData ? (array) $customData : [];
    }

    /**
     * Save custom fields data to tenant-specific table
     * 
     * @param int $salarySettingId
     * @param int $boxId
     * @param array $customData
     * @return bool
     */
    public static function saveCustomFieldsData($salarySettingId, $boxId, $customData)
    {
        SalarySettingHelper::ensureTableExists($boxId);
        $tableName = SalarySettingHelper::getTableName($boxId);
        
        // Get custom field definitions for this box
        $customFieldDefs = \App\Models\BoxCustomField::where('box_id', $boxId)
            ->where('object', 'like', 'salary_%')
            ->get();
        
        // Filter to only custom fields and ensure columns exist
        $filteredData = [];
        foreach ($customData as $key => $value) {
            // Skip system fields
            if (in_array($key, ['id', 'salary_setting_id', 'box_id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }
            
            // Check if it's a custom field (base name without _type or _max)
            $baseKey = str_replace('_type', '', str_replace('_max', '', $key));
            
            // Find matching custom field definition
            $fieldDef = $customFieldDefs->firstWhere('col_name', $baseKey);
            
            // If field definition found, use its type; otherwise assume 'number' for fields with _type or _max
            $fieldType = $fieldDef ? $fieldDef->type : 'number';
            
            // This is a custom field - ensure column exists
            if ($key === $baseKey) {
                // Main field value - ensure column exists
                SalarySettingHelper::addCustomColumn($boxId, $key, $fieldType);
            } elseif (str_ends_with($key, '_type')) {
                // Type field (percentage/fixed) - store as string
                // Ensure column exists for _type fields
                if (!Schema::hasColumn($tableName, $key)) {
                    try {
                        Schema::table($tableName, function ($table) use ($key) {
                            $table->string($key, 20)->default('fixed')->nullable();
                        });
                    } catch (\Exception $e) {
                        // Column might already exist, continue
                        if (!str_contains($e->getMessage(), 'Duplicate column') && 
                            !str_contains($e->getMessage(), 'already exists')) {
                            throw $e;
                        }
                    }
                }
            } elseif (str_ends_with($key, '_max')) {
                // Max amount field - store as decimal
                if (!Schema::hasColumn($tableName, $key)) {
                    try {
                        Schema::table($tableName, function ($table) use ($key) {
                            $table->decimal($key, 15, 2)->default(0)->nullable();
                        });
                    } catch (\Exception $e) {
                        // Column might already exist, continue
                        if (!str_contains($e->getMessage(), 'Duplicate column') && 
                            !str_contains($e->getMessage(), 'already exists')) {
                            throw $e;
                        }
                    }
                }
            }
            
            // Add to filtered data
            if (str_ends_with($key, '_type')) {
                // Type field - store as string (percentage or fixed)
                $filteredData[$key] = $value;
            } elseif (str_ends_with($key, '_max')) {
                // Max amount field - convert to float
                $filteredData[$key] = ($value === '' || $value === null) ? 0 : floatval($value);
            } elseif ($key === $baseKey && $fieldType === 'number') {
                // Main number field - convert to float
                $filteredData[$key] = ($value === '' || $value === null) ? 0 : floatval($value);
            } else {
                // Other fields (text, etc.) - store as is
                $filteredData[$key] = $value;
            }
        }
        
        if (empty($filteredData)) {
            return true; // No custom fields to save
        }
        
        $filteredData['salary_setting_id'] = $salarySettingId;
        $filteredData['box_id'] = $boxId;
        $filteredData['updated_at'] = now();
        
        // Check if record exists
        $exists = DB::table($tableName)
            ->where('salary_setting_id', $salarySettingId)
            ->exists();
            
        if ($exists) {
            DB::table($tableName)
                ->where('salary_setting_id', $salarySettingId)
                ->update($filteredData);
        } else {
            $filteredData['created_at'] = now();
            DB::table($tableName)->insert($filteredData);
        }
        
        return true;
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get all salaries that use this salary setting
     */
    public function salaries()
    {
        return $this->hasMany(Salary::class, 'salary_settings_id');
    }
}
