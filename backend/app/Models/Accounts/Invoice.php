<?php
namespace App\Models\Accounts;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use App\Models\{Box, Lead, Company, User};

class Invoice extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'box_id',
        'type',
        'company_id',
        'lead_id',
        'amount',
        'amount_with_tax',
        'paid_amount',
        'balance_amount',
        'invoice_date',
        'due_date',
        'tax_type',
        'code',
        'remarks',
        'added_by',
        'approved_by',
        'last_updated_by',
        'invoice_no',
        'system_number',
        'payment_option',
        'status',
        'status_date'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'status_date' => 'datetime',
        'amount' => 'decimal:2',
        'amount_with_tax' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
