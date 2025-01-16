<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'reference_no', 'type', 'amount', 'payment_method',
        'note', 'warehouse_id', 'total', 'invoice_tax', 'total_tax',
        'labor_cost', 'net_total', 'paid', 'due', 'date', 'transaction_type'
    ];

    public function client(){
    	return $this->belongsTo('App\Models\Client');
    }

    public function purchases() {
    	return $this->hasMany('App\Models\Purchase', 'reference_no', 'reference_no');
    }

    public function sells() {
    	return $this->hasMany('App\Models\Sell', 'reference_no', 'reference_no');
    }

    public function payments() {
        return $this->hasMany('App\Models\Payment', 'reference_no', 'reference_no');
    }

    public function returnSales() {
        return $this->hasMany('App\Models\ReturnTransaction', 'sells_reference_no', 'reference_no');
    }

    public function warehouse () {
        return $this->belongsTo('App\Models\Warehouse');
    }

    protected static function boot () {
        parent::boot();
        self::saving(function ($model) {
            $model->warehouse_id = auth()->user()->warehouse_id;
        });
    }
}
