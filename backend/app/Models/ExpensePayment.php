<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'payer_id',
        'payment_reference',
        'payment_date',
        'amount',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }
}
