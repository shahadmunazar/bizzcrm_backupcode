<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SystemField extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'system_fields';

    protected $fillable = [
        'box_id',
        'name',
        'object',
        'field',
        'field_name',
        'field_type',
        'field_refernce',
        'field_refernce_key',
        'options',
        'validation_rules',
        'sequence',
        'is_visible',
        'is_required',
        'is_editable',
        'is_sortable',
        'is_searchable',
        'placeholder',
        'updated_by',
    ];

    protected $casts = [
        'options'       => 'array',
        'is_visible'    => 'boolean',
        'is_required'   => 'boolean',
        'is_editable'   => 'boolean',
        'is_sortable'   => 'boolean',
        'is_searchable' => 'boolean',
    ];

    /**
     * Get the user who last updated this field.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }
}
