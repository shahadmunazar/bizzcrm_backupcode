<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class BiometricMachine extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'biometric_machines';

    protected $fillable = [
        'box_id',
        'machine_no',
        'machine_type',
        'serial_no',
        'server_port_no',
        'machine_name',
        'branch_name',
        'timezone',
        'status',
        'access_control',
        'country_id',
        'state_id',
        'last_sync',
        'last_sync_attendance',
        'sync_status',
        'city_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class, 'branch_name');
    }


    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
