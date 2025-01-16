<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\CashRegister;
use Carbon\Carbon;
use App\Models\ExpenseCategory;

class ExpenseController extends Controller
{
    private $searchParams = ['expense_category', 'from', 'to'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {
        $expenses = Expense::orderBy('created_at', 'desc');
        if ($request->get('expense_category')) {
            $expenses->where('expense_category_id', $request->get('expense_category'));
        }

        $from = $request->get('from');
        $to = $request->get('to') ?: date('Y-m-d');
        $to = Carbon::createFromFormat('Y-m-d', $to);
        $to = $this->filterTo($to);

        if ($request->get('from') || $request->get('to')) {
            if (!is_null($from)) {
                $from = Carbon::createFromFormat('Y-m-d', $from);
                $from = $this->filterFrom($from);
                $expenses->whereBetween('created_at', [$from, $to]);
            } else {
                $expenses->where('created_at', '<=', $to);
            }
        }

        $totalexpenses = $expenses->sum('amount');

        if ($request->get('from') || $request->get('to') || $request->get('expense_category')) {
            $expenses = $expenses->paginate(100);
        } else {
            $expenses = $expenses->paginate(25);
        }

        $categories = ExpenseCategory::all();
        return view('expenses.index')->withExpenses($expenses)->withCategories($categories)->withTotalexpenses($totalexpenses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postSearch(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([ExpenseController::class, 'getIndex'], $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(Request $request)
    {

        $request->validate([
            'purpose' => 'required',
            'amount' => 'required|numeric',
            'expense_category_id' => 'required'
        ]);

        $expense = new Expense;
        $expense->purpose = $request->get('purpose');
        $expense->expense_category_id = $request->get('expense_category_id');
        $expense->amount = $request->get('amount');
        $expense->created_at = Carbon::parse($request->get('date'));
        $expense->save();

        $message = trans('core.saved');
        return redirect()->back()->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cashRegister(Request $request)
    {
        $cash_register = new CashRegister;
        $cash_register->cash_in_hands = $request->get('cash_in_hands');
        $cash_register->date = Carbon::now()->format('Y-m-d');
        $cash_register->save();

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editExpense(Request $request)
    {
        $request->validate([
            'purpose' => 'required',
            'amount' => 'required|numeric',
        ]);

        $expense = Expense::find($request->get('id'));
        $expense->purpose = $request->get('purpose');
        $expense->amount = $request->get('amount');
        $expense->expense_category_id = $request->get('expense_category_id');
        if ($request->get('date')) {
            $expense->created_at = Carbon::parse($request->get('date'));
        }
        $expense->save();

        $message = trans('core.changes_saved');
        return redirect()->back()->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteExpense(Request $request)
    {
        $expense = Expense::find($request->get('id'));
        $expense->delete();
        return redirect()->route('expense.index');
    }

    private function filterTo($to)
    {
        // Implement your filterTo logic here
        return $to;
    }

    private function filterFrom($from)
    {
        // Implement your filterFrom logic here
        return $from;
    }
}
