<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnTransaction extends Model
{
	use SoftDeletes;

    public function sells(){
    	return $this->belongsTo('App\Models\Sell', 'sells_id');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'returned_by');
    }
}
