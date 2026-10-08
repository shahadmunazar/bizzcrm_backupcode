<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class LeadSourceTemplate extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'name',
        'indetifier',
        'system_fields',
        'source_fields',
        'status',
        'from',
        'has_word',
    ];

    protected $casts = [
        'system_fields' => 'array',
        'source_fields' => 'array',
    ];
}
