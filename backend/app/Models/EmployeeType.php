<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EmployeeType extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'box_id',
        'name',
        'status',
        'added_by',
        'updated_by',
        'deleted_by',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
