<?php
namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeePermission extends Pivot
{
    use Auditable;
    protected $table = 'box_user_permissions';

    protected $fillable = [
        'box_user_id',
        'permission_id',
    ];
}
