<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppQuickReply extends Model implements AuditableContract
{
    use Auditable;

    use HasFactory, SoftDeletes;


    protected $fillable = [
        'box_id',
        'title',
        'message',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }
}
