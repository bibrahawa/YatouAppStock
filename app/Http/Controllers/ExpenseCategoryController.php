<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {
        $categories;
        if($request->has('name')) {
            $categories = ExpenseCategory::where('name', 'like', '%' . $request->get('name') . '%')->paginate(30);
        } else {
        $categories = ExpenseCategory::paginate(30);
        }

        return view('expense-category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postIndex(Request $request)
    {
        $category = new ExpenseCategory;
            $category->name = $request->get('name');
        $category->save();

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editExpenseCategory(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $category = ExpenseCategory::find($request->get('id'));
            $category->name = $request->get('name');
        $category->save();

        $message = trans('core.changes_saved');
        return redirect()->back()->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteExpenseCategory(Request $request)
    {

        $category = ExpenseCategory::find($request->get('id'));
        $category->delete();
        return redirect()->route('expense.category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
