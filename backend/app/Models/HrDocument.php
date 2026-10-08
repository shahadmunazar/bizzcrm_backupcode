<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class HrDocument extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'box_id',
        'box_user_id',
        'name',
        'category',
        'file_path',
        'uploaded_by',
    ];
}
