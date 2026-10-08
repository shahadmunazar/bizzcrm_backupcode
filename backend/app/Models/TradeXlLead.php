<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class TradeXlLead extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    
    protected $table = 'trade_xl_leads';
    protected $guarded = ['id'];
}
