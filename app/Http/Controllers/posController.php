<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Tax;
use App\Models\Sell;
use App\Models\Client;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Category;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    /**
     * Redirect to pos screen
     *
     * @return \Illuminate\Http\Response
    */
    public function getPOS(Request $request){
        $categories = Category::all();
        $customers = Client::where('client_type','!=', 'purchaser')->where('id', '!=', 1)->get();
        return view('pos.posdesign', compact('categories', 'customers'));
    }

    /**
     * Saving sell in pos
     *
     * @return \Illuminate\Http\Response
     */
    public function posPost(Request $request) {
        $customer = $request->get('customer');
        $enableProductTax = settings('product_tax');

        $ym = Carbon::now()->format('Y/m');

        $row = Transaction::where('transaction_type', 'sell')->withTrashed()->count() > 0 ? Transaction::where('transaction_type', 'sell')->withTrashed()->count() + 1 : 1;

        $ref_no = $ym.'/S-'.ref($row);
        $total = 0;
        $totalProductTax = 0;
        $productTax = 0;
        $total_cost_price = 0;
        $sells = $request->get('sells');
        $paid = floatval($request->get('paid')) ?: 0;

        $transactionId = null;

        DB::transaction(function() use ($request, $sells, $ref_no, &$total, &$total_cost_price, &$totalProductTax, $customer, $paid, $enableProductTax, $productTax, &$transactionId) {
            foreach ($sells as $sell_item) {
                $total += $sell_item['sell_quantity'] * $sell_item['mrp'];
                $total_cost_price += $sell_item['cost_price'] * $sell_item['sell_quantity'];

                $sell = new Sell;
                $sell->reference_no = $ref_no;
                $sell->product_id = $sell_item['id'];
                $sell->quantity = $sell_item['sell_quantity'];

                if ($enableProductTax == 1) {
                    // Product tax calculation
                    $product_row = Product::findOrFail($sell_item['id']);
                    $taxRate = $product_row->tax->rate;
                    $taxType = $product_row->tax->type;

                    $productTax = ($taxType == 1) ? (($sell_item['sell_quantity'] * $taxRate * $product_row->mrp) / 100) : ($sell_item['sell_quantity'] * $taxRate);

                    $sell->product_tax = $productTax;
                    $totalProductTax += $productTax;
                }

                $sell->unit_cost_price = $sell_item['cost_price'];
                $sell->sub_total = ($sell_item['sell_quantity'] * $sell_item['mrp']) - $productTax;
                $sell->date = Carbon::now()->format('Y-m-d H:i:s');
                $sell->client_id = $customer;
                $sell->save();

                // Update quantity of product after every sell
                $product = $sell->product;
                $product->quantity -= intval($sell_item['sell_quantity']);
                $product->save();
            }

            // Discount
            $discount = $request->get('discount_amount');
            $total_payable = $total - $discount;

            // Invoice tax
            $invoice_tax = settings('invoice_tax') == 1 ? $request->get('invoice_tax') : 0;

            $transaction = new Transaction;
            $transaction->reference_no = $ref_no;
            $transaction->client_id = $customer;
            $transaction->transaction_type = 'sell';
            $transaction->total_cost_price = $total_cost_price;
            $transaction->discount = $discount;
            $transaction->total = $total_payable - $totalProductTax;
            $transaction->invoice_tax = $invoice_tax;
            $transaction->total_tax = $totalProductTax + $invoice_tax;
            $transaction->labor_cost = 0;
            $transaction->net_total = $total_payable + $invoice_tax;
            $transaction->paid = ($paid > ($total_payable + $invoice_tax) ? ($total_payable + $invoice_tax) : $paid);
            $transaction->change_amount = ($paid > ($total_payable + $invoice_tax) ? ($paid - $total_payable + $invoice_tax) : 0);
            $transaction->pos = 1;
            $transaction->date = Carbon::now()->format('Y-m-d H:i:s');
            $transaction->save();

            $transactionId = $transaction->id;

            if ($paid > 0) {
                $payment = new Payment;
                $payment->client_id = $customer;
                $payment->amount = ($paid > ($total_payable + $invoice_tax) ? ($total_payable + $invoice_tax) : $paid);
                $payment->method = $request->get('method');
                $payment->type = 'credit';
                $payment->reference_no = $ref_no;
                $payment->note = "Paid for Invoice ".$ref_no;
                $payment->date = Carbon::now()->format('Y-m-d H:i:s');
                $payment->save();
            }
        });

        return response()->json(['id' => $transactionId, 'message' => 'Successfully saved transaction.']);
    }

    /**
     * Return to the pos invoice
     *
     * @return \Illuminate\Http\Response
    */
    public function posInvoice($id) {
        $transaction = Transaction::findOrFail($id);
        return view('pos.invoice', compact('transaction'));
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
