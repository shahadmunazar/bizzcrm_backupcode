<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ProjectCategory extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'box_id', 'name', 'status', 'created_by', 'updated_by', 'deleted_by'
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }


    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
