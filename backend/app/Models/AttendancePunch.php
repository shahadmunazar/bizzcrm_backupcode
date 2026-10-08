<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AttendancePunch extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'attendance_punches';

    protected $fillable = [
        'boxes_id',
        'user_id',
        'box_user_id',
        'device_id',
        'action',
        'punched_at',
        'latitude',
        'longitude',
        'signed_payload',
        'signature'
    ];
}
