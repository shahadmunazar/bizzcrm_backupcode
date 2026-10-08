<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SalaryTransaction extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'salary_report_id',
        'user_bank_account_id',
        'payment_date',
        'amount',
        'payment_method',
        'transaction_ref',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    // Relationships
    public function salaryReport()
    {
        return $this->belongsTo(SalaryReport::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(EmployeeBankDetail::class, 'user_bank_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
