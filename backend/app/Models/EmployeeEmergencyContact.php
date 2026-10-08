<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class EmployeeEmergencyContact extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;


    protected $table = 'employee_emergency_contacts';

    protected $fillable = [
        'box_user_id',
        'box_id',
        'created_by',
        'updated_by',
        'name',
        'relation',
        'phone',
        'phone_alt',
        'type',
    ];

    /**
     * Get the box user (employee) this emergency contact belongs to.
     */
    public function boxUser(): BelongsTo
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    /**
     * Get the associated box (department or team).
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
