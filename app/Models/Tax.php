<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    Protected $fillable = ['name', 'type', 'rate'];
}
