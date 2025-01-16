<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\CashRegister;
use App\Models\Payment;
use App\Models\Expense;

function user() {
    return Auth::user();
}

function licensed() {
    // return Licensor::hasLicense();
    return true;
}

function currentRoute() {
    return Route::currentRouteName();
}

function filterFrom($date) {
    return $date->startOfDay();
}

function filterTo($date) {
    return $date->endOfDay();
}

function settings($key, $fallback = null) {
    $settings = Setting::orderBy('id', 'asc')->first();
    return $settings ? $settings->{$key} : $fallback;
}

function carbonDate($date, $format) {
    $dtobj = Carbon::parse($date);
    switch ($format) {
        case 'y-m-d':
            return $dtobj->format('F jS, Y');
        case 'h-i-s':
        case 'time':
            return $dtobj->format('g:i A');
        default:
            return $dtobj->format('F jS Y, g:i A');
    }
}

function bangla_digit($value) {
    $bn_digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    $locale = app()->getLocale();
    $en_digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $hindi_digits = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];

    if ($locale === 'bn') {
        return str_replace(range(0, 9), $bn_digits, $value);
    } elseif ($locale === 'hindy') {
        return str_replace(range(0, 9), $hindi_digits, $value);
    }

    return str_replace(range(0, 9), $en_digits, $value);
}

function ref($num) {
    if ($num < 10) {
        return "000" . $num;
    } elseif ($num < 100) {
        return "00" . $num;
    } elseif ($num < 1000) {
        return "0" . $num;
    } else {
        return $num;
    }
}

function todayProfit() {
    $today = Carbon::now()->format('Y-m-d');
    $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
    $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

    $todaysSellTransaction = Transaction::whereBetween('date', [$today_starts, $today_ends])->where('transaction_type', 'sell');

    $todaysProfit = $todaysSellTransaction->sum('total') - $todaysSellTransaction->sum('total_cost_price');

    return $todaysProfit;
}

function cashStatus() {
    $now = Carbon::now()->format('Y-m-d');
    $status = CashRegister::where('date', $now)->count();
    return $status;
}

function cashNow() {
    $today = Carbon::now()->format('Y-m-d');
    $today_starts = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
    $today_ends = Carbon::createFromFormat('Y-m-d', $today)->endOfDay();

    $cash_in_hands = CashRegister::where('date', $today)->sum('cash_in_hands');
    $cash_received = Payment::whereBetween('created_at', [$today_starts, $today_ends])->where('type', 'credit')->where('method', 'cash')->sum('amount');
    $cash_given = Payment::whereBetween('created_at', [$today_starts, $today_ends])->where('type', '!=', 'credit')->where('method', 'cash')->sum('amount');
    $expense = Expense::whereBetween('created_at', [$today_starts, $today_ends])->sum('amount');

    $cash_now = ($cash_in_hands + $cash_received) - ($cash_given + $expense);

    return $cash_now;
}

function rtlLocale() {
    return app()->getLocale() === 'ar';
}

function twoPlaceDecimal($value) {
    return number_format((float)$value, 2, '.', '');
}

function numberFormatter($value) {
    $number = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return title_case($number->format($value));
}

function colSpanNumber() {
    return (settings('product_tax') == 1) ? 4 : 3;
}

function sellDetailsColSpanNumber() {
    return (settings('product_tax') == 1) ? 5 : 4;
}

function orderTax() {
    if (settings('invoice_tax') == 1) {
        $tax_rate = settings('invoice_tax_rate');
        $tax_type = (settings('invoice_tax_type') == 1) ? "%" : " ";
    } else {
        $tax_rate = "0";
        $tax_type = "%";
    }

    return $tax_rate . $tax_type;
}

function getNow() {
    return Carbon::now();
}

function title_case($string) {
    return mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
}
