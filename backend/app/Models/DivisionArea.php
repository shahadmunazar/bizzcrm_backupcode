<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class DivisionArea extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'division_areas';

    protected $fillable = [
        'box_id',
        'division_id',
        'state_id',
        'city_id',
        'lead_id',
        'lat',
        'lng',
        'radius',
        'polygon_paths',
        'location_type',
        'name', // present in migration
        'limit_member',
        'max_members',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class)->withTrashed();
    }

    public function creator()

    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
