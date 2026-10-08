<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SalaryDeduction extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'salary_id',
        'box_user_id',
        'box_id',
        'tds',
        'esi',
        'pf',
        'tds_sum_amount', 
        'esi_sum_amoutn', // Note: Database has typo, keeping for compatibility
        'pf_sum_amount',
        'leave',
        'prof_tax',
        'labour_welfare',
        'othersdeductions',
        // New deduction fields
        'salary_advance_recovery',
        'loan_emi_recovery',
        'canteen_deduction',
        'attendance_loss',
        'notice_period_recovery',
        'arrears_recovery',
        'insurance_premium_employee',
        'employee_ids',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'tds'              => 'decimal:2',
        'esi'              => 'decimal:2',
        'pf'               => 'decimal:2',
        'leave'            => 'decimal:2',
        'prof_tax'         => 'decimal:2',
        'labour_welfare'   => 'decimal:2',
        'othersdeductions' => 'decimal:2',
    ];

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'box_user_id');
    }
}
