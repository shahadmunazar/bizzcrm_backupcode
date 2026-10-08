<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Company extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'companies';

    protected $fillable = [
        'box_id',
        'bill_name',
        'bill_phone',
        'pan',
        'gstin',
        'bill_address',
        'bill_pin_code',
        'bill_state_id',
        'bill_city_id',
        'status',
        'added_by',
        'last_updated_by',
        'params',
    ];

    protected $casts = [
        'params' => 'array',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'bill_state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'bill_city_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
