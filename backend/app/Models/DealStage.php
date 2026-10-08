<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class DealStage extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    protected $fillable = ['box_id', 'name', 'status', 'created_by', 'updated_by', 'deleted_by'];
    
}
