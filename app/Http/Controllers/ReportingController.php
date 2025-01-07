<?php
namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\Client;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportingController extends Controller
{
    public function getIndex()
    {
        $products = Product::all();
        $clients = Client::all();
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $warehouses = Warehouse::all();
        return view('reporting.index', compact('products', 'clients', 'categories', 'subcategories', 'warehouses'));
    }

    public function postPurchaseReport(Request $request)
    {
        $warehouse_id = $request->get('warehouse_id');
        $query = Transaction::where('transaction_type', 'purchase');
        $transactions = ($warehouse_id == 'all') ? $query : $query->where('warehouse_id', $warehouse_id);
        $from = $request->get('from');
        $to = $request->get('to') ?: date('Y-m-d');
        $to = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

        if ($from || $to) {
            if (!is_null($from)) {
                $from = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $transactions->whereBetween('date', [$from, $to]);
            } else {
                $transactions->where('date', '<=', $to);
            }
        }

        return view('reporting.purchaseReport')
            ->withTransactions($transactions->get())
            ->withFrom($request->get('from'))
            ->withTo($request->get('to'));
    }

    public function postSellsReport(Request $request)
    {
        $warehouse_id = $request->get('warehouse_id');
        $warehouse_name = ($warehouse_id == 'all') ? 'All Branch' : Warehouse::find($warehouse_id)->name;

        $query = Transaction::where('transaction_type', 'sell');
        $transactions = ($warehouse_id == 'all') ? $query : $query->where('warehouse_id', $warehouse_id);

        $from = $request->get('from');
        $to = $request->get('to') ?: date('Y-m-d');
        $to = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

        if ($from || $to) {
            if (!is_null($from)) {
                $from = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $transactions->whereBetween('date', [$from, $to]);
            } else {
                $transactions->where('date', '<=', $to);
            }
        }

        return view('reporting.sellsReport')
            ->withTransactions($transactions->get())
            ->withFrom($request->get('from'))
            ->withTo($request->get('to'));
    }

    public function postProductReport(Request $request)
    {
        $warehouse_id = $request->get('warehouse_id');
        $warehouse_name = ($warehouse_id == 'all') ? 'All Branch' : Warehouse::find($warehouse_id)->name;

        $from = $request->get('from');
        $to = $request->get('to') ?: date('Y-m-d');
        $to = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

        $sells = ($warehouse_id == "all") ? Sell::query() : Sell::where('warehouse_id', $warehouse_id);
        $purchases = ($warehouse_id == "all") ? Purchase::query() : Purchase::where('warehouse_id', $warehouse_id);

        if ($from || $to) {
            if (!is_null($from)) {
                $from = Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $sells->whereBetween('date', [$from, $to]);
                $purchases->whereBetween('date', [$from, $to]);
            } else {
                $sells->where('date', '<=', $to);
                $purchases->where('date', '<=', $to);
            }
        }

        $product_id = $request->get('product_id');
        $products = ($product_id != 'all') ? Product::whereId($product_id)->get() : Product::all();

        $total = [];
        $total_profit = 0;
        foreach ($products as $product) {
            $cloneForSells = clone $sells;
            $cloneForPurchases = clone $purchases;

            $sellRow = $cloneForSells->whereProductId($product->id);
            if ($sellRow->count() > 0 && $sellRow->first()->quantity > 0) {
                $sellItemMrp = $sellRow->first()->sub_total / $sellRow->first()->quantity;
                $sellItemCost = $sellRow->first()->unit_cost_price;
            } else {
                $sellItemMrp = 0;
                $sellItemCost = 0;
            }

            $total[$product->id]['name'] = $product->name;
            $total[$product->id]['purchase'] = $cloneForPurchases->whereProductId($product->id)->sum('quantity') . " " . $product->unit;
            $total[$product->id]['sells'] = $sellRow->sum('quantity') . " " . $product->unit;
            $total[$product->id]['stock'] = $product->quantity . " " . $product->unit;

            $total[$product->id]['profit'] = ($sellItemMrp - $sellItemCost) * $sellRow->sum('quantity') . " " . settings('currency_code');

            $total_profit += floatval($total[$product->id]['profit']);
        }

        return view('reporting.productReport')
            ->withTotal($total)
            ->withFrom($request->get('from'))
            ->withTo($request->get('to'))
            ->with('total_profit', $total_profit)
            ->with('warehouse_name', $warehouse_name);
    }

    public function postStockReport(Request $request)
    {
        $product_id = $request->get('product_id');
        $products = ($product_id != 'all') ? Product::whereId($product_id)->get() : Product::all();
        $product_name = ($product_id != 'all') ? $products->first()->name : 'All Products';

        $stock_value_by_cost = 0;
        $stock_value_by_price = 0;
        foreach ($products as $product) {
            $stock_value_by_cost += ($product->quantity * $product->cost_price);
            $stock_value_by_price += ($product->quantity * $product->mrp);
        }

        $profit_estimate = $stock_value_by_price - $stock_value_by_cost;
        $stock = [$stock_value_by_cost, $stock_value_by_price, $profit_estimate];

        return view('reporting.stockReport', compact('stock', 'product_name'));
    }

    public function postCategoryReport(Request $request)
    {
        $to = $request->get('to') ?: date('Y-m-d');
        $to = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
        $from = $request->get('from');
        $from = $from ? Carbon::createFromFormat('Y-m-d', $from)->startOfDay() : Carbon::createFromDate(1970, 1, 1, env('TIMEZONE', 'UTC'));

        $category_id = $request->get('category_id');
        $categories = ($category_id != 'all') ? Category::where('id', $category_id)->get() : Category::all();

        $data = [];
        $total_profit = 0;

        foreach ($categories as $category) {
            if ($category->products->count() == 0) {
                $data[$category->id] = [
                    'name' => $category->category_name,
                    'quantity' => '-',
                    'profit' => "-"
                ];
                continue;
            }

            $profit = 0;
            $quantity = 0;
            foreach ($category->products as $product) {
                $productSell = $product->sells()->whereBetween('date', [$from, $to])->get();
                foreach ($productSell as $sell) {
                    $mrp = $sell->sub_total;
                    $cost_price = $sell->quantity * $sell->unit_cost_price;
                    $profit += $mrp - $cost_price;
                    $quantity += $sell->quantity;
                }
            }

            $data[$category->id] = [
                'name' => $category->category_name,
                'quantity' => $quantity . " " . $product->unit,
                'profit' => settings('currency_code') . " " . $profit
            ];

            $total_profit += $profit;
        }

        return view('reporting.categoryReport', compact('data', 'from', 'to', 'total_profit'));
    }

    public function postSubCategoryReport(Request $request)
    {
        $to = $request->get('to') ?: date('Y-m-d');
        $to = Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
        $from = $request->get('from');
        $from = $from ? Carbon::createFromFormat('Y-m-d', $from)->startOfDay() : Carbon::createFromDate(1970, 1, 1, env('TIMEZONE', 'UTC'));

        $subcategory_id = $request->get('subcategory_id');
        $subcategories = ($subcategory_id != 'all') ? Subcategory::where('id', $subcategory_id)->get() : Subcategory::all();

        $data = [];
        $total_profit = 0;

        foreach ($subcategories as $subcategory) {
            if ($subcategory->products->count() == 0) {
                $data[$subcategory->id] = [
                    'name' => $subcategory->name,
                    'quantity' => '-',
                    'profit' => "-"
                ];
                continue;
            }

            $quantity = 0;
            $profit = 0;

            foreach ($subcategory->products as $product) {
                $productSell = $product->sells()->whereBetween('date', [$from, $to])->get();

                foreach ($productSell as $sell) {
                    $quantity += $sell->quantity;
                    $mrp = $sell->sub_total;
                    $cost_price = $sell->quantity * $sell->unit_cost_price;
                    $profit += $mrp - $cost_price;
                }

                $data[$subcategory->id] = [
                    'name' => $subcategory->name,
                    'quantity' => $quantity . " " . $product->unit,
                    'profit' => settings('currency_code') . " " . $profit
                ];
            }

            $total_profit += $profit;
        }
        return view('reporting.subCategoryReport', compact('data', 'from', 'to', 'total_profit'));
    }

    public function postBranchReport(Request $request)
    {
        $branch = $request->get('warehouse_id');
        $product_id = $request->get('product_id');

        $branch_name = ($branch == 'all') ? 'All Branch' : Warehouse::find($branch)->name;
        $product_name = ($product_id == 'all') ? 'All Product' : Product::find($product_id)->name;

        $products = ($product_id == "all") ? Product::select('name', 'id', 'unit')->get() : Product::whereId($product_id)->get();

        $stock = [];

        foreach ($products as $product) {
            $purchase_query = ($branch == 'all') ? $product->purchases() : $product->purchases()->where('warehouse_id', $branch);
            $sell_query = ($branch == 'all') ? $product->sells() : $product->sells()->where('warehouse_id', $branch);

            $purchasing_quantity = $purchase_query->where('product_id', $product->id)->sum('quantity');
            $selling_quantity = $sell_query->where('product_id', $product->id)->sum('quantity');

            $stock[$product->id] = [
                'name' => $product->name,
                'quantity' => $product->opening_stock + $purchasing_quantity - $selling_quantity . " " . $product->unit,
            ];
        }

        return view('reporting.warehouseReport', compact('branch_name', 'product_name', 'stock'));
    }

    public function postProfitReport(Request $request)
    {
        $branch_id = $request->get('warehouse_id');
        $branch_name = ($branch_id == 'all') ? 'All Branch' : Warehouse::find($branch_id)->name;

        $query = Transaction::where('transaction_type', 'sell');
        $transactions = ($branch_id == 'all') ? $query : $query->where('warehouse_id', $branch_id);

        $from = Carbon::parse($request->get('from') ?: date('Y-m-d'))->startOfDay();
        $to = Carbon::parse($request->get('to') ?: date('Y-m-d'))->endOfDay();

        $transactions = $transactions->whereBetween('date', [$from, $to]);

        $total_selling_price = $transactions->sum('total');
        $total_cost_price = $transactions->sum('total_cost_price');
        $gross_profit = $total_selling_price - $total_cost_price;

        $expenses = Expense::whereBetween('created_at', [$from, $to])->get();
        $total_expense = $expenses->sum('amount');

        $net_profit = $gross_profit - $total_expense;

        $total_tax = $transactions->sum('total_tax');

        $net_profit_after_tax = $net_profit - $total_tax;

        return view('reporting.profitReport', compact('from', 'to', 'branch_name', 'total_selling_price', 'total_cost_price', 'gross_profit', 'total_expense', 'expenses', 'net_profit', 'total_tax', 'net_profit_after_tax'));
    }

    public function postDueReport(Request $request)
    {
        $query = Client::where('client_type', 'customer')->orderBy('first_name', 'asc');
        $clients = ($request->get('client_id') == 'all') ? $query->get() : $query->where('id', $request->get('client_id'))->get();

        $from = Carbon::parse($request->get('from'))->startOfDay();
        $to = Carbon::parse($request->get('to'))->endOfDay();

        $due_details = [];

        foreach ($clients as $client) {
            $due_details[$client->id]['name'] = $client->first_name;
            $due_details[$client->id]['net_total'] = $client->transactions()->whereBetween('date', [$from, $to])->sum('net_total');
            $due_details[$client->id]['paid'] = $client->payments()->whereBetween('date', [$from, $to])->where('type', 'credit')->sum('amount');
            $due_details[$client->id]['due'] = $due_details[$client->id]['net_total'] - $due_details[$client->id]['paid'];
        }

        return view('reporting.clientDueReport', compact('due_details', 'from', 'to'));
    }
}
