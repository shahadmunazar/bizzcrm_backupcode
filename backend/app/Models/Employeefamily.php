<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Employeefamily extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;


    protected $table = 'employee_families';

    protected $fillable = [
        'box_user_id',
        'box_id',
        'created_by',
        'updated_by',
        'name',
        'relationship',
        'dob',
        'contact',
    ];

    /**
     * The employee (box user) this family member belongs to.
     */
    public function boxUser(): BelongsTo
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    /**
     * The box (department or team context).
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    /**
     * User who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated the record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
