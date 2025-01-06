<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use SoftDeletes;

    public function category() {
    	return $this->belongsTo('App\Models\Category');
    }

    public function products(){
    	return $this->hasMany('App\Models\Product');
    }

    public function sells () {
    	return $this->hasManyThrough('App\Models\Sell', 'App\Models\Product');
    }
}
