<?php

namespace  App\Models;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\DNS1D;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'details',
        'cost_price',
        'mrp',
        'minimum_retail_price',
        'unit',
        'opening_stock',
    ];

    public function category(){
    	return $this->belongsTo('App\Models\Category');
    }

    public function subcategory(){
    	return $this->belongsTo('App\Models\Subcategory');
    }

    public function tax(){
        return $this->belongsTo('App\Models\Tax');
    }

    public function purchases() {
    	return $this->hasMany('App\Models\Purchase');
    }

    public function getBarCodeAttribute()
    {
        return 'data:image/png;base64,' . DNS1D::getBarcodePNG($this->code, "c128A",1,33,array(1,1,1), true);
    }

    public function sells() {
    	return $this->hasMany('App\Models\Sell');
    }

    public function damages() {
        return $this->hasMany('App\Models\Damage');
    }
}
