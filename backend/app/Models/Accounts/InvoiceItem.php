<?php
namespace App\Models\Accounts;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Model};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use App\Models\{Box, Lead};

class InvoiceItem extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'box_id',
        'invoice_id',
        'lead_id',
        'package_id',
        'amount',
        'msp',
        'qty',
        'sgst',
        'cgst',
        'igst',
        'remarks'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'msp' => 'decimal:2',
        'sgst' => 'decimal:2',
        'cgst' => 'decimal:2',
        'igst' => 'decimal:2',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'package_id');
    }
}
