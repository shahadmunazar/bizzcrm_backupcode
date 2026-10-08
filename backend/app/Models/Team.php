<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Team extends Model implements AuditableContract
{

    use SoftDeletes, Auditable, NodeTrait, \Spatie\Permission\Traits\HasPermissions;

    public $guard_name = 'web';

    protected $fillable = [
        'department_id',
        'box_id',
        'name',
        'user_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'show_into_leads',
        'show_into_accounts',
    ];
    protected $guarded = [];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function hasRole($roles): bool
    {
        return false;
    }
}
