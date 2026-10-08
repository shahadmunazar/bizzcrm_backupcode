<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class NintyNineAcersLead extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    
    protected $table = 'ninty_nine_acers_leads';
    protected $guarded = ['id'];
}
