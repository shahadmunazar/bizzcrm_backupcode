<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use Auditable;
    // Extending DatabaseNotification automatically handles the UUID 'id' and 'data' casting.
}
