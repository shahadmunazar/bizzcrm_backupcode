<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BizzAuditable;

class Expense extends Model
{
    use HasFactory, SoftDeletes, BizzAuditable;

    protected $fillable = [
        'box_id',
        'boxuser_id',
        'customer_check_in_id',
        'expense_category_id',
        'expense_date',
        'amount',
        'payment_method',
        'description',
        'location',
        'status',
    ];

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class, 'boxuser_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function checkIn()
    {
        return $this->belongsTo(CustomerCheckIn::class, 'customer_check_in_id');
    }

    public function receipts()
    {
        return $this->hasMany(ExpenseReceipt::class);
    }

    public function approvals()
    {
        return $this->hasMany(ExpenseApproval::class);
    }

    public function payment()
    {
        return $this->hasOne(ExpensePayment::class);
    }
}
