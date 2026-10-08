<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceFingerprint extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'attendance_fingerprints';

    protected $fillable = [
        'boxes_id',
        'user_id',
        'box_user_id',
        'device_id',
        'public_key',
        'signature',
        'key_algorithm',
        'status',
        'enrolled_at'
    ];
}
