<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AssignEmployeesOffice extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes,Auditable;

    protected $table = 'assign_employees_offices';

    protected $fillable = [
        'box_user_id',
        'branch_office_id',
        'box_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }


    public function employee()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }

    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class, 'branch_office_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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
