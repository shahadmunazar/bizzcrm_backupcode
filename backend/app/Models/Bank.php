<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Bank extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'banks';

    protected $fillable = [
        'box_id',
        'company_id',
        'name',
        'account_no',
        'ifsc',
        'address',
        'show_pdf',
        'status',
        'added_by',
        'last_updated_by'
    ];

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
