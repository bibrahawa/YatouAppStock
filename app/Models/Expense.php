<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    public function expenseCategory() {
    	return $this->belongsTo('App\Models\ExpenseCategory', 'expense_category_id');
    }
}
