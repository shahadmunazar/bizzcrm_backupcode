<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ProductHsn extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'company_id',
        'code',
        'short_name',
        'sgst',
        'cgst',
        'igst',
        'type',
        'uqc',
        'cess',
        'from_date',
        'to_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }
}
