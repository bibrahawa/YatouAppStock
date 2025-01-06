<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public function subcategories(){
    	return $this->hasMany('App\Models\Subcategory');
    }

    public function products () {
    	return $this->hasManyThrough('App\Models\Product', 'App\Models\Subcategory');
    }

    public function product () {
    	return $this->hasMany('App\Models\Product');
    }
}
