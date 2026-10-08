<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Department extends Model implements AuditableContract
{
    use SoftDeletes, Auditable, NodeTrait, \Spatie\Permission\Traits\HasPermissions;

    public $guard_name = 'web';

    protected $fillable = [
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

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class)->whereIsRoot();
    }

    public function allTeams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
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
        // Departments do not have roles, they act as roles themselves in this context.
        return false;
    }
}
