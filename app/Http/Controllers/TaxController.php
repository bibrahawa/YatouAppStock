<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $taxes = Tax::paginate(10);
        return view('taxes.index', compact('taxes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postTax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'type' => 'required',
            'rate' => 'required|numeric',
        ]);

        $tax = new Tax($validated);
        $tax->save();

        $message = trans('core.saved');
        return redirect()->back()->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editTax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'type' => 'required',
            'rate' => 'required|numeric',
        ]);

        $tax = Tax::findOrFail($request->get('id'));
        $tax->update($validated);

        $message = trans('core.changes_saved');
        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteTax(Request $request)
    {
        $tax = Tax::findOrFail($request->get('id'));
        $tax->delete();

        $message = trans('core.deleted');
        return redirect()->route('tax.index')->with('message', $message);
    }
}
