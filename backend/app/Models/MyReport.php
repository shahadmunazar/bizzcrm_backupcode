<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class MyReport extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'user_id',
        'name',
        'is_public',
        'sort_by',
        'sort_dir',
        'is_default',
        'page_length',
        'display_start',
        'show_in_report',
        'is_system',
        'filters',
        'columns',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_default' => 'boolean',
        'show_in_report' => 'boolean',
        'is_system' => 'boolean',
        'filters' => 'array',
        'columns' => 'array',
    ];
}
