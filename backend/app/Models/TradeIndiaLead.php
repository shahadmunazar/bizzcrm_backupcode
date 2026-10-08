<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class TradeIndiaLead extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    
    protected $table = 'trade_india_leads';
    protected $guarded = ['id'];
}
