<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Salary extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'box_user_id',
        'box_id',
        'salary_payroll_deduction_id',
        'salary_payroll_overtime_id',
        'salary_payroll_addition_id',
        'effective_from',
        'effective_to',
        'gross_salary',
        'ctc_amount',
        'total_deductions',
        'net_salary',
        'salary_settings_id',
        // NOTE: salary_settings_snapshot is NOT saved to salaries table - only to salary_history for audit trail
        'increment_reason',
        'approved_by',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        // salary_settings_snapshot is only stored in salary_history, not in salaries table
        // 'salary_settings_snapshot' => 'array',
    ];

    public function boxuser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function earnings()
    {
        return $this->hasOne(SalaryEarning::class);
    }

    public function salarySetting()
    {
        return $this->belongsTo(SalarySetting::class, 'salary_settings_id');
    }

    // Alias for backward compatibility
    public function salary_settings()
    {
        return $this->salarySetting();
    }


    public function deductions()
    {
        return $this->hasOne(SalaryDeduction::class);
    }

    public function history()
    {
        return $this->hasMany(SalaryHistory::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payrollDeduction()
    {
        return $this->belongsTo(PayrollDeduction::class, 'salary_payroll_deduction_id');
    }

    public function payrollOvertime()
    {
        return $this->belongsTo(PayrollOvertime::class, 'salary_payroll_overtime_id');
    }

    public function payrollAddition()
    {
        return $this->belongsTo(PayrollAddition::class, 'salary_payroll_addition_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Capture complete snapshot of salary settings (including custom fields)
     * This creates a JSON record of all salary settings at the time of assignment
     *
     * @return array|null
     */
    public function captureSalarySettingsSnapshot(): ?array
    {
        if (!$this->salarySetting) {
            return null;
        }

        $salarySetting = $this->salarySetting;
        $boxId = $this->box_id;

        // Get base salary setting data
        $snapshot = $salarySetting->toArray();

        // Get custom fields data from tenant table
        $customData = \App\Models\SalarySetting::getCustomFieldsData($salarySetting->id, $boxId);

        // Merge custom fields into snapshot
        foreach ($customData as $key => $value) {
            // Skip system fields
            if (!in_array($key, ['id', 'salary_setting_id', 'box_id', 'created_at', 'updated_at', 'deleted_at'])) {
                $snapshot[$key] = $value;
            }
        }

        // Add metadata
        $snapshot['_snapshot_metadata'] = [
            'captured_at' => now()->toDateTimeString(),
            'salary_setting_id' => $salarySetting->id,
            'salary_setting_name' => $salarySetting->name ?? null,
            'box_id' => $boxId,
        ];

        return $snapshot;
    }

    /**
     * Save salary settings snapshot to this salary record
     */
    public function saveSalarySettingsSnapshot(): bool
    {
        $snapshot = $this->captureSalarySettingsSnapshot();

        if ($snapshot) {
            $this->update(['salary_settings_snapshot' => $snapshot]);
            return true;
        }

        return false;
    }

    /**
     * Get salary settings snapshot
     */
    public function getSalarySettingsSnapshot(): ?array
    {
        return $this->salary_settings_snapshot;
    }
}
