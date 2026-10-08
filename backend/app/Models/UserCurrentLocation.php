<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use App\Traits\BizzAuditable;

class UserCurrentLocation extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable, BizzAuditable;

    protected $table = 'user_current_locations';

    protected $fillable = [
        'box_id',
        'user_id',
        'boxuser_id',
        'latitude',
        'longitude',
        'accuracy',
        'battery_level',
        'is_charging',
        'network_status',
        'network_type',
        'last_online_at',
        'last_offline_at',
        'status',
        'recorded_at',
        'added_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
