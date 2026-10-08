<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'box_id',
        'name',
        'rate',
        'status',
        'added_by',
        'updated_by',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }
}
