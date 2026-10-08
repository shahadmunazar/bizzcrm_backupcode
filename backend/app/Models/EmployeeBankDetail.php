<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class EmployeeBankDetail extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'employee_bank_details';

    protected $fillable = [
        'box_user_id',
        'box_id',
        'created_by',
        'updated_by',
        'bank_name',
        'bank_type',
        'account_number',
        'branch',
        'ifsc_code',
    ];

    /**
     * Get the box user (employee) this bank detail belongs to.
     */
    public function boxUser(): BelongsTo
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    /**
     * Get the box (department or group).
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
