<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SalaryReport extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'salary_id',
        'box_user_id',
        'box_id',
        'year',
        'month',
        'total_working_days',
        'total_weekly_offs',
        'total_holidays',
        'paid_days',
        'leave_days',
        'gross_salary',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'earnings_breakdown',
        'deductions_breakdown',
        'is_locked',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'earnings_breakdown'   => 'array',
        'deductions_breakdown' => 'array',
        'is_locked'            => 'boolean',
    ];

    // Relationships
    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function transactions()
    {
        return $this->hasMany(SalaryTransaction::class);
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

    // Helper: calculate net salary
    public function calculateNetSalary()
    {
        $this->net_salary = $this->total_earnings - $this->total_deductions;
        return $this->net_salary;
    }
}
