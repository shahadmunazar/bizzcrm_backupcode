<?php
namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeBankStatutory extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'employee_bank_statutories_';

    protected $fillable = [
        'box_user_id',
        'box_id',
        'created_by',
        'updated_by',
        'salary_basis',
        'salary_amount',
        'payment_type',
        'pf_contribution',
        'pf_no',
        'pf_rate',
        'pf_additional',
        'pf_total',
        'esi_contribution',
        'esi_no',
        'esi_rate',
        'esi_additional',
        'esi_total',
    ];

    /**
     * The employee (box user) this statutory data belongs to.
     */
    public function boxUser(): BelongsTo
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    /**
     * The box (department or division) associated with the employee.
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    /**
     * The user who created this record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The user who last updated this record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
