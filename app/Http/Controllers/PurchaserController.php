<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Transaction;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaserController extends Controller
{
    private $searchParams = ['name', 'phone', 'company_name', 'address'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {
        $purchasers = Client::orderBy('first_name', 'asc')->where('client_type', 'purchaser');

        foreach ($this->searchParams as $param) {
            if ($request->get($param)) {
                $purchasers->where($param, 'LIKE', '%' . $request->get($param) . '%');
            }
        }

        return view('purchaser.index')->withPurchasers($purchasers->paginate(20));
    }

    /**
     * Handle the post request for filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([PurchaserController::class, 'getIndex'], $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewPurchaser()
    {
        $purchaser = new Client;
        return view('purchaser.form', compact('purchaser'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ClientRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function getEditPurchaser(Client $purchaser)
    {
        return view('purchaser.form')->withPurchaser($purchaser);
    }

    public function postPurchaser(ClientRequest $request)
    {

        $client = $request->id ? Client::findOrFail($request->id) : new Client();
        $client->first_name = $request->get('first_name');
        $client->last_name = $request->get('last_name');
        $client->phone = $request->get('phone');
        $client->company_name = $request->get('company_name');
        $client->address = $request->get('address');
        $client->email = $request->get('email');
        $client->account_no = $request->get('account_no');
        $client->client_type = $client->client_type;

        if ($request->get('previous_due') != null) {
            $client->provious_due = $request->get('previous_due');
        }

        $client->save();

        if ($request->get('previous_due') != null) {
            $transaction = new Transaction;
            $row = Transaction::where('transaction_type', 'opening')->withTrashed()->count() + 1;
            $ref_no = "OPENING-" . str_pad($row, 5, '0', STR_PAD_LEFT);
            $transaction->fill([
                'reference_no' => $ref_no,
                'client_id' => $client->id,
                'transaction_type' => 'opening',
                'warehouse_id' => 1,
                'total' => $request->get('previous_due'),
                'invoice_tax' => 0,
                'total_tax' => 0,
                'labor_cost' => 0,
                'net_total' => $request->get('previous_due'),
                'paid' => 0,
            ]);
            $transaction->save();
        }

        $message = trans('core.changes_saved');
        return redirect()->route('purchaser.index')->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function deleteClient(Client $client)
    {
        if ($client->sells->count() == 0 && $client->purchases->count() == 0) {
            $client->delete();
            $message = trans('core.deleted');
            return redirect()->back()->withMessage($message);
        }

        $message = trans('core.client_has_sells');
        return redirect()->back()->withMessage($message);
    }
}
