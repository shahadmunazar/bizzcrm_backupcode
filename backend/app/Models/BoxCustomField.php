<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BoxCustomField extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'box_custom_fields';

    protected $fillable = [
        'box_id',
        'type',
        'col_name',
        'name',
        'error_message',
        'required',
        'object',
        'readonly',
        'hidden',
        'params',
        'ordering',
        'status',
        'quick_edit',
        'added_by',
        'last_updated_by',
    ];

    protected $casts = [
        'params'   => 'array',
        'required' => 'boolean',
        'readonly' => 'boolean',
        'hidden'   => 'boolean',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
