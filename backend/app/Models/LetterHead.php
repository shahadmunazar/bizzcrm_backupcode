<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

use Illuminate\Database\Eloquent\SoftDeletes;

class LetterHead extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'file_path',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
