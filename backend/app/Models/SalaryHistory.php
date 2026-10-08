<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SalaryHistory extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $table    = 'salary_history';
    protected $fillable = [
        'salary_id',
        'box_user_id',
        'box_id',
        'changed_by',
        'change_type',
        'remarks',
        'old_value',
        'new_value',
        'diff',
        'salary_settings_snapshot', // JSON snapshot of all salary settings at the time of change
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'diff'      => 'array',
        'salary_settings_snapshot' => 'array',
    ];

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
