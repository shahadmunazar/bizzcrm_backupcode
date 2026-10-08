<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CallLog extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    
    protected $fillable = [
        'box_id',
        'created_by',
        'name',
        'from',
        'to',
        'type',
        'status',
        'duration',
        'start_time',
        'end_time',
        'rawTimestamp',
        'start_lat',
        'start_long',
        'end_lat',
        'end_long',
        'user_id',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
