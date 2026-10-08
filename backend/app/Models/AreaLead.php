<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AreaLead extends Model implements AuditableContract
{
    use HasFactory, Auditable, SoftDeletes;

    protected $table = 'area_leads';

    protected $fillable = [
        'division_id',
        'division_area_id',
        'area_lead_id',
        'box_id',
        'box_user_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'lat',
        'lng',
        'radius',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function divisionArea()
    {
        // Assuming DivisionArea model exists, if not, this might need adjustment later
        return $this->belongsTo(DivisionArea::class, 'division_area_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'area_lead_id');
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'box_user_id');
    }
}
