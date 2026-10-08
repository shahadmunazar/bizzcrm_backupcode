<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PaymentMode extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'payment_modes';

    protected $fillable = [
        'box_id',
        'name',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
