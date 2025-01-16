<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    private $searchParams = ['receipt_no', 'invoice_no', 'client', 'type', 'from', 'to', 'method'];

    public function getIndex(Request $request)
    {
        $clients = Client::pluck('first_name', 'id');
        $type = ['debit' => 'Debit', 'credit' => 'Credit', 'return' => 'Return'];
        $methods = ['cash' => 'Cash', 'card' => 'Card', 'cheque' => 'Cheque', 'others' => 'Others'];

        $payments = Payment::orderBy('id', 'desc');

        if ($request->filled('receipt_no')) {
            $str = ltrim($request->get('receipt_no'), '0');
            $payments->whereId($str);
        }

        if ($request->filled('invoice_no')) {
            $payments->where('reference_no', 'LIKE', '%' . $request->get('invoice_no') . '%');
        }

        if ($request->filled('client')) {
            $payments->whereClientId($request->get('client'));
        }

        if ($request->filled('type')) {
            $payments->whereType($request->get('type'));
        }

        if ($request->filled('method')) {
            $payments->whereMethod($request->get('method'));
        }

        $from = $request->get('from');
        $to = $request->get('to') ?: date('Y-m-d');
        $to = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

        if ($request->filled('from') || $request->filled('to')) {
            if (!is_null($from)) {
                $from = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $payments->whereBetween('created_at', [$from, $to]);
            } else {
                $payments->where('created_at', '<=', $to);
            }
        }

        $total_debit = (clone $payments)->whereType('debit')->sum('amount');
        $total_credit = (clone $payments)->whereType('credit')->sum('amount');
        $total_return = (clone $payments)->whereType('return')->sum('amount');

        if ($request->filled('print')) {
            $printable_payments = $payments->get();
            return view('payments.print-payment-list', compact('printable_payments', 'total_debit', 'total_credit', 'total_return', 'from', 'to'));
        }

        return view('payments.list', [
            'clients' => $clients,
            'type' => $type,
            'total_debit' => $total_debit,
            'total_credit' => $total_credit,
            'total_return' => $total_return,
            'methods' => $methods,
            'payments' => $payments->paginate(20)
        ]);
    }

    public function postIndex(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([PaymentController::class, 'getIndex'], $params);
    }

    public function postPayment(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'type' => 'required|string',
            'date' => 'required|date',
            'reference_no' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        if ($request->get('invoice_payment') == 1) {
            $ref_no = $request->get('reference_no');
            $transaction = Transaction::where('reference_no', $ref_no)->firstOrFail();
            $transaction->paid = round($transaction->paid + $request->get('amount'), 2);
            $transaction->save();

            Payment::create([
                'client_id' => $request->get('client_id'),
                'amount' => round($request->get('amount'), 2),
                'method' => $request->get('method'),
                'type' => $request->get('type'),
                'reference_no' => $request->get('reference_no'),
                'note' => $request->get('note'),
                'date' => Carbon::parse($request->get('date'))->format('Y-m-d H:i:s'),
            ]);
        } else {
            $amount = round($request->get('amount'), 2);
            $client = Client::findOrFail($request->get('client_id'));

            foreach ($client->transactions as $transaction) {
                $due = round($transaction->net_total - $transaction->paid, 2);
                if ($due > 0 && $amount > 0) {
                    $paymentAmount = min($amount, $due);
                    $transaction->paid += $paymentAmount;
                    $transaction->save();

                    Payment::create([
                        'client_id' => $client->id,
                        'amount' => $paymentAmount,
                        'method' => $request->get('method'),
                        'type' => $request->get('type'),
                        'reference_no' => $transaction->reference_no,
                        'note' => $request->get('note'),
                        'date' => Carbon::parse($request->get('date'))->format('Y-m-d H:i:s'),
                    ]);

                    $amount -= $paymentAmount;
                }
                if ($amount <= 0) {
                    break;
                }
            }
        }

        return redirect()->back()->withSuccess(trans('core.payment_received'));
    }

    public function printReceipt(Payment $payment)
    {
        return view('payments.receipt-print', compact('payment'));
    }
}
