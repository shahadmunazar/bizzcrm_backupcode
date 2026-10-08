<?php
namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'name',
        'guard_name',
        'module',
        'action',
    ];

    // Many-to-Many relationship with BoxUser
    public function boxUsers()
    {
        return $this->belongsToMany(BoxUser::class, 'box_user_permissions')->withTimestamps();
    }

    // Legacy relationship (if still needed)
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_permissions')->withTimestamps();
    }
}
