<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class TableColumnPreference extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'table_column_preferences';

    protected $fillable = [
        'box_id',
        'user_id',
        'table_name',
        'column_name',
        'display_name',
        'order',
        'visible',
        'updated_by',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
