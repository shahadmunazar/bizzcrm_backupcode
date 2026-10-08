<?php
namespace App\Models\Accounts;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use App\Models\{Box, BoxUser, Lead, Company, Bank, Division, User};

class Receipt extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'box_user_id',
        'lead_id',
        'company_id',
        'receipt_date',
        'invoice_number',
        'amount',
        'payment_mode',
        'bank_id',
        'division_id',
        'reference',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'tds_deducted',
        'tds_amount',
        'remarks',
        'approved_by',
        'type'
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function boxUser()
    {
        return $this->belongsTo(BoxUser::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
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
