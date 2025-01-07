<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $warehouses = Warehouse::orderBy('name', 'asc')->paginate(20);
        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $warehouse = new Warehouse;
        return view('warehouses.form', compact('warehouse'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $warehouse = $request->id ? Warehouse::findOrFail($request->id) : new Warehouse();
        $warehouse->name = $request->name;
        $warehouse->address = $request->address;
        $warehouse->phone = $request->phone;
        $warehouse->in_charge_name = $request->in_charge_name;
        $warehouse->save();

        $message = trans('core.changes_saved');
        return redirect()->route('warehouse.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.form', compact('warehouse'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $exists = User::where('warehouse_id', $warehouse->id)->count();
        if ($exists > 0) {
            $warning = "You can't delete this branch. This branch has ".$exists." active user(s)";
            return redirect()->back()->with('warning', $warning);
        } else {
            if ($warehouse->transactions()->count() == 0) {
                $warehouse->delete();
                $message = trans('core.deleted');
                return redirect()->back()->with('message', $message);
            } else {
                $warning = trans('core.warehouse_has_transactions');
                return redirect()->back()->with('warning', $warning);
            }
        }
    }
}
