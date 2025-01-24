<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name', 'slogan', 'address', 'email', 'phone', 'owner_name', 'currency_code', 'theme', 'enable_purchaser', 'enable_customer', 'vat_no', 'pos_invoice_footer_text', 'dashboard', 'product_tax', 'invoice_tax', 'invoice_tax_rate', 'invoice_tax_type'
    
    ];
}
