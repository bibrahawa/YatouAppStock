<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at','updated_at'];

	public function transaction () {
		return $this->hasOne('App\Models\Transaction', 'reference_no', 'reference_no');
	}

	public function client() {
		return $this->belongsTo('App\Models\Client');
	}

}
