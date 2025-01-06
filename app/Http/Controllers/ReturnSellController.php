<?php

namespace App\Http\Controllers;

use DB;
use App\Tax;
use App\Models\Sell;
use App\Client;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Category;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Http\Requests;
use App\ReturnTransaction;
use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
use App\Http\Requests\SellRequest;
use App\Http\Controllers\Controller;
use App\Exceptions\ValidationException;

class ReturnSellController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getReturnSell()
    {
        $sell = new Sell;
        $invoices = Transaction::where('return', 1)->select('reference_no')->get();
        $customers = Client::where('client_type','!=', 'purchaser')->where('id', '!=', 1)->get();
        $products = Product::orderBy('name', 'asc')->where('status', 1)->select('id','name','cost_price', 'mrp','minimum_retail_price','quantity', 'tax_id', 'code')->get();
        return view('return.sale')
                        ->withSell($sell)
                        ->withCustomers($customers)
                        ->withProducts($products)
                        ->withInvoices($invoices);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchInvoice($invoice_no)
    {
        $invoice = Transaction::where('reference_no', $invoice_no)->first();
        $previous_balance = $invoice->paid - $invoice->net_total;
        if($previous_balance > 0){
            $previous_balance =  $previous_balance;
        }else{
            $previous_balance =  0;
        }

        return response()->json($previous_balance);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReturnSell(Request $request)
    {
        $invoice_no = $request->get('invoice_no');
        $old_transactions = Transaction::where('reference_no', $invoice_no)->first();

        $ym = Carbon::now()->format('Y/m');

        $row = Transaction::where('transaction_type', 'sell')->withTrashed()->get()->count() > 0 ? Transaction::where('transaction_type', 'sell')->withTrashed()->get()->count() + 1 : 1;
        $ref_no = "RET-".ref($row);
        $total = 0;
        $productTax = 0;
        $total_cost_price = 0;
        $sells = $request->get('sells');
        $paid = floatval($request->get('paid')) ?: 0;

        foreach ($sells as $sell_item) {

            if (intval($sell_item['quantity']) === 0) {
                throw new ValidationException('Product quantity is required');
            }

            if (!$sell_item['product_id'] || $sell_item['product_id'] === '') {
                throw new ValidationException('Product ID is required');
            }

            $total = $total + $sell_item['subtotal'];
            $total_cost_price = $total_cost_price + ($sell_item['cost_price'] * $sell_item['quantity']);

            $sell = new Sell;
                $sell->reference_no = $ref_no;
                $sell->product_id = $sell_item['product_id'];
                $sell->quantity = $sell_item['quantity'];

                $sell->unit_cost_price = $sell_item['cost_price'];
                $sell->sub_total = $sell_item['subtotal']- $productTax;
                $sell->client_id = $old_transactions->client_id;
                $sell->date = Carbon::parse($request->get('date'))->format('Y-m-d H:i:s');
            $sell->save();

            $product = $sell->product;
                $product->quantity = $product->quantity - intval($sell_item['quantity']);
            $product->save();


            //discount
            $discount = $request->get('discount');
            $discountType = $request->get('discountType');
            $discountAmount = $discount;
            if($discountType == 'percentage'){
                $discountAmount = $total * (1 * $discount / 100);
            }

            $total_payable = $total - $discountAmount;
            //discount ends


            //invoice tax
            if(settings('invoice_tax') == 1){
                if(settings('invoice_tax_type') == 1){
                    $invoice_tax = (settings('invoice_tax_rate') * $total_payable) / 100;
                }else{
                    $invoice_tax = settings('invoice_tax_rate');
                }
            }else{
                $invoice_tax = 0;
            }
            //ends

            $transaction = new Transaction;
                $transaction->reference_no = $ref_no;
                $transaction->client_id = $old_transactions->client_id;
                $transaction->transaction_type = 'sell';
                $transaction->total_cost_price = $total_cost_price;
                $transaction->discount = $discountAmount;
                $transaction->total = $total_payable;
                $transaction->invoice_tax = round($invoice_tax, 2);
                $transaction->total_tax = round($invoice_tax, 2);
                $transaction->net_total =  round(($total_payable + $invoice_tax), 2);
                $transaction->date = Carbon::parse($request->get('date'))->format('Y-m-d H:i:s');
                $transaction->paid = $paid + $request->get('previous_balance');
                $transaction->return_invoice = $invoice_no;
                $transaction->return_balance = $request->get('previous_balance');
            $transaction->save();


            if($paid > 0){
                $payment = new Payment;
                    $payment->client_id = $old_transactions->client_id;
                    $payment->amount = $paid;
                    $payment->method = $request->get('method');
                    $payment->type = 'credit';
                    $payment->reference_no = $ref_no;
                    $payment->note = "Paid for Invoice ".$ref_no;
                    $payment->date = Carbon::parse($request->get('date'))->format('Y-m-d H:i:s');
                $payment->save();
            }

            return response(['message' => 'Successfully saved transaction.']);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
