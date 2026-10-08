<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Division extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'divisions';

    protected $fillable = [
        'box_id',
        'company_id',
        'name',
        'auto_map_loaction',
        'limit_member',
        'max_members',
        'status',
        'added_by', // Legacy name for created_by in initial request, keeping for compat
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function areas()
    {
        return $this->hasMany(DivisionArea::class);
    }
}
