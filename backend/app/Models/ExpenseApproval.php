<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'approver_id',
        'role_approved_as',
        'status',
        'comment',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
