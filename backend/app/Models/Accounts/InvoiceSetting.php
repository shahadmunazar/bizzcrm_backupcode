<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'company_id',
        'invoice_logo',
        'logo_width',
        'logo_height',
        'invoice_prefix',
        'from_date',
        'to_date',
        'invoice_due_days',
        'enable_round_off',
        'round_off_type',
        'show_company_details',
        'header_terms',
        'footer_terms',
    ];
}
