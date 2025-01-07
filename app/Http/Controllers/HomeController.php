<?php
namespace App\Http\Controllers;

use App\Models\Sell;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    private $searchParams = ['invoice_no', 'customer', 'bill_no'];

    public function getIndex()
    {
        $dayNames = [];
        $lastSevenDaySells = [];
        $lastSevenDayPurchases = [];
        $todays_stats = [
            'total_selling_quantity' => 0,
            'total_purchasing_quantity' => 0
        ];

        for ($i = 0; $i <= 5; $i++) {
            $dayNames[] = now()->subDays($i)->format('D');
            $getNow = now()->subDays($i)->format('Y-m-d');
            $getStarts = Carbon::createFromFormat('Y-m-d', $getNow)->startOfDay();
            $getEnds = Carbon::createFromFormat('Y-m-d', $getNow)->endOfDay();

            if ($i == 0 && settings('dashboard') == 'tile-box') {
                $this->processTransactions($getStarts, $getEnds, $todays_stats);
            }

            $this->processTransactions($getStarts, $getEnds, $lastSevenDaySells, $lastSevenDayPurchases);

            $lastSevenDayTransactions[] = Payment::whereBetween('date', [$getStarts, $getEnds])->sum('amount');
        }

        $todays_stats['total_transactions_today'] = $lastSevenDayTransactions[0] ?? 0;
        $todays_stats['total_selling_price'] = $lastSevenDaySells[0] ?? 0;
        $todays_stats['total_purchasing_price'] = $lastSevenDayPurchases[0] ?? 0;

        $daynames = array_reverse($dayNames);
        $lastSevenDaySells = implode(',', array_reverse($lastSevenDaySells));
        $lastSevenDayPurchases = implode(',', array_reverse($lastSevenDayPurchases));
        $lastSevenDayTransactions = implode(',', array_reverse($lastSevenDayTransactions));

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();
        $stock_value_by_cost = 0;
        $stock_value_by_price = 0;
        $top_products_all = [];
        $top_products_month = [];

        $products = Product::select('quantity', 'mrp', 'cost_price')->get();
        foreach ($products as $product) {
            $stock_value_by_cost += $product->quantity * $product->cost_price;
            $stock_value_by_price += $product->quantity * $product->mrp;
        }

        Sell::chunk(50000, function ($sells) use (&$top_products_all, &$top_products_month, &$start, &$end) {
            foreach ($sells->groupBy('product_id') as $sell) {
                $product_selling_name = $sell->map(fn($item) => $item->product->name);
                $top_products_all[$product_selling_name[0]] = $sell->sum('quantity');
                $top_products_month[$product_selling_name[0]] = $sell->whereBetween('date', [$start, $end])->sum('quantity');
            }
        });

        if ($products->count() != 0) {
            arsort($top_products_month);
            $top_products = array_slice($top_products_month, 0, 5);
        } else {
            $top_products = [];
        }

        $top_product_name = array_keys($top_products);
        $selling_quantity = array_values($top_products);

        $profit_estimate = $stock_value_by_price - $stock_value_by_cost;
        $stock = [$stock_value_by_cost, $stock_value_by_price, $profit_estimate];

        for ($i = 0; $i <= 5; $i++) {
            $now = Carbon::now()->subMonths($i)->format('Y-m-d');
            $from = Carbon::createFromFormat('Y-m-d', $now)->startOfMonth();
            $to = Carbon::createFromFormat('Y-m-d', $now)->endOfMonth();
            $month = Carbon::createFromFormat('Y-m-d', $now)->format("M");
            $months[] = $month;

            $transactionThisMonth = Transaction::whereBetween('date', [$from, $to]);
            $sellDiscount = $transactionThisMonth->where('transaction_type', 'sell')->sum('discount');
            $total_selling_tax = $transactionThisMonth->where('transaction_type', 'sell')->sum('total_tax');
            $purchaseDiscount = $transactionThisMonth->where('transaction_type', 'purchase')->sum('discount');
            $total_purchasing_tax = $transactionThisMonth->where('transaction_type', 'purchase')->sum('total_tax');

            $sells[] = Sell::whereBetween('date', [$from, $to])->sum('sub_total') + $total_selling_tax - $sellDiscount;
            $purchases[] = Purchase::whereBetween('date', [$from, $to])->sum('sub_total') + $total_purchasing_tax - $purchaseDiscount;

            $lastSixMonthsSellTransactions = Transaction::whereBetween('date', [$from, $to])->where('transaction_type', 'sell');
            $last_six_months_profit[] = $lastSixMonthsSellTransactions->sum('total') - $lastSixMonthsSellTransactions->sum('total_cost_price');
        }

        return view('home', compact(
            'todays_stats', 'top_product_name', 'selling_quantity', 'stock', 'months', 'sells', 'purchases', 'last_six_months_profit', 'lastSevenDaySells', 'lastSevenDayPurchases', 'lastSevenDayTransactions', 'daynames'
        ));
    }

    private function processTransactions($start, $end, &$sells = [], &$purchases = [])
    {
        Transaction::whereBetween('date', [$start, $end])
            ->chunk(1000, function ($transactions) use (&$sells, &$purchases) {
                $sells[] = $transactions->where('transaction_type', 'sell')->sum('net_total');
                $purchases[] = $transactions->where('transaction_type', 'purchase')->sum('net_total');
            });
    }

    public function todayCashIn()
    {
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $received_cash = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', 'credit')->get();
        $total_received_amount = $received_cash->sum('amount');

        $totalCashPayment = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', 'credit')->where('method', 'cash')->sum('amount');
        $totalCardPayment = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', 'credit')->where('method', 'card')->sum('amount');
        $totalCashAndCardPayment = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', 'credit')->where('method', 'cash + card')->sum('amount');
        $totalMobilePayment = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', 'credit')->where('method', 'mobile_money')->sum('amount');

        return view('dashboard.cashin', compact('received_cash', 'total_received_amount', 'totalCashPayment', 'totalCardPayment', 'totalCashAndCardPayment', 'totalMobilePayment'));
    }

    public function todayCashOut()
    {
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $paid_rows = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', '!=', 'credit')->get();
        $total_paid_sum = $paid_rows->sum('amount');

        return view('dashboard.cashout', compact('paid_rows', 'total_paid_sum'));
    }

    public function todayInvoice(Request $request)
    {
        $customers = Client::orderBy('first_name', 'asc')->where('client_type', '!=', 'purchaser')->pluck('first_name', 'id');
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $invoices = Transaction::whereBetween('date', [$today_starts, $today_ends])->where('transaction_type', 'sell');

        if ($request->get('invoice_no')) {
            $invoices->where('reference_no', 'LIKE', '%' . $request->get('invoice_no') . '%');
        }

        if ($request->get('customer')) {
            $invoices->whereClientId($request->get('customer'));
        }

        $invoices = $invoices->paginate(25);

        $total = $invoices->sum('total');
        $total_vat = $invoices->sum('total_tax');
        $net_total = $invoices->sum('net_total');
        $total_cost_price = $invoices->sum('total_cost_price');
        $profit = $total - $total_cost_price;

        $payments = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', 'credit');
        $total_cash_payment = $payments->where('method', 'cash')->sum('amount');
        $total_card_payment = $payments->where('method', 'card')->sum('amount');
        $total_cheque_payment = $payments->where('method', 'cheque')->sum('amount');

        return view('dashboard.invoice-list-today', compact(
            'customers', 'invoices', 'total', 'total_vat', 'net_total', 'total_cost_price', 'profit', 'total_cash_payment', 'total_card_payment', 'payments', 'total_cheque_payment'
        ));
    }

    public function postTodayInvoice(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([HomeController::class, 'todayInvoice'], $params);
    }

    public function todaysBill(Request $request)
    {
        $suppliers = Client::orderBy('first_name', 'asc')->where('client_type', 'purchaser')->pluck('first_name', 'id');
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $bills = Transaction::whereBetween('date', [$today_starts, $today_ends])->where('transaction_type', 'purchase')->orderBy('id', 'desc');

        if ($request->get('bill_no')) {
            $bills->where('reference_no', 'LIKE', '%' . $request->get('bill_no') . '%');
        }

        if ($request->get('supplier')) {
            $bills->whereClientId($request->get('supplier'));
        }

        return view('dashboard.bill-list-today', compact('bills', 'suppliers'));
    }

    public function postTodayBill(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([HomeController::class, 'todaysBill'], $params);
    }

    public function todayExpense()
    {
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $expense_rows = Expense::whereBetween('created_at', [$today_starts, $today_ends])->get();
        $total_expense_sum = $expense_rows->sum('amount');

        return view('dashboard.expense', compact('expense_rows', 'total_expense_sum'));
    }

    public function todayTransaction()
    {
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $transaction_lists = Payment::whereBetween('date', [$today_starts, $today_ends])->orderBy('id', 'desc')->get();
        $transaction_amount = $transaction_lists->sum('amount');

        return view('dashboard.transaction-list-today', compact('transaction_lists', 'transaction_amount'));
    }

    public function destroy($id)
    {
        // Implement the destroy method if needed
    }

    public function cashDetails()
    {
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $cash_in_hands = CashRegister::where('date', $today)->sum('cash_in_hands');
        $total_received = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', 'credit')->where('method', 'cash')->sum('amount');
        $total_paid = Payment::whereBetween('date', [$today_starts, $today_ends])->where('type', '!=', 'credit')->where('method', 'cash')->sum('amount');
        $total_expense = Expense::whereBetween('created_at', [$today_starts, $today_ends])->sum('amount');

        $remaining_cash = $cash_in_hands + $total_received - $total_paid - $total_expense;

        return view('dashboard.today-cash-details', compact('cash_in_hands', 'total_received', 'total_paid', 'total_expense', 'remaining_cash'));
    }

    public function profitDetails()
    {
        $today = Carbon::now()->format('Y-m-d');
        $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
        $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

        $sellTransactionToday = Transaction::whereBetween('date', [$today_starts, $today_ends])->where('transaction_type', 'sell');
        $SellItemMrp = $sellTransactionToday->sum('total');
        $SellItemCost = $sellTransactionToday->sum('total_cost_price');
        $SellItemTax = $sellTransactionToday->sum('total_tax');
        $todaysExpense = Expense::whereBetween('created_at', [$today_starts, $today_ends])->sum('amount');

        $todaysProfit = $SellItemMrp - $SellItemCost;

        return view('dashboard.profit-details', compact('todaysProfit', 'SellItemCost', 'SellItemMrp', 'todaysExpense', 'SellItemTax'));
    }
}
