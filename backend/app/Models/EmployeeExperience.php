<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class EmployeeExperience extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;


    protected $table = 'employee_experiences';

    protected $fillable = [
        'box_user_id',
        'box_id',
        'created_by',
        'updated_by',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'current_working',
    ];

    /**
     * The employee (box user) this experience belongs to.
     */
    public function boxUser(): BelongsTo
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    /**
     * The box/department associated with this experience.
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
