<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLetter extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = ['box_id', 'user_id', 'template_id', 'content', 'letter_type', 'status', 'applied_date', 'release_date', 'requested_by', 'created_by', 'updated_by', 'deleted_by'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class);
    }
}
