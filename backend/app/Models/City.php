<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
class City extends Model implements AuditableContract
{
    use HasFactory,Auditable;

    protected $fillable = [
        'name',
        'state_id',
        'country_id',
        'status',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }
}
