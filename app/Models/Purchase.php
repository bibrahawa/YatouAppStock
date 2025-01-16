<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

	protected $dates = ['created_at','updated_at'];

    public function client()
    {
    	return $this->belongsTo('App\Models\Client');
    }

    public function product()
    {
    	return $this->belongsTo('App\Models\Product');
    }

    protected static function boot () {
        parent::boot();
        self::saving(function ($model) {
            $model->warehouse_id = auth()->user()->warehouse_id;
        });
    }
}
