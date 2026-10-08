<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SalaryEarning extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'salary_id',
        'box_user_id',
        'box_id',
        'basic',
        'da',
        'hra',
        'da_sum_amount', 
        'hra_sum_amount',
        'conveyance',
        'allowance',
        'medical_allowance',
        'others',
        // New earnings fields
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
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'basic'             => 'decimal:2',
        'da'                => 'decimal:2',
        'hra'               => 'decimal:2',
        'conveyance'        => 'decimal:2',
        'allowance'         => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'others'            => 'decimal:2',
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
