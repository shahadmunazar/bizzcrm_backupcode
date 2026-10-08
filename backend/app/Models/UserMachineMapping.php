<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
class UserMachineMapping extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes,Auditable;

    protected $fillable = [
        'box_user_id',
        'box_id',
        'biometric_machine_id',
        'machine_user_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function user()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function machine()
    {
        return $this->belongsTo(BiometricMachine::class, 'biometric_machine_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(BoxUser::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(BoxUser::class, 'updated_by');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(BoxUser::class, 'deleted_by');
    }
}
