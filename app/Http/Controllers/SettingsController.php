<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Tax;
use App\Models\User;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function getIndex()
    {
        $setting = Setting::firstOrNew(['id' => 1]);
        $taxes = Tax::all();
        $tax_rate = $setting->invoice_tax_rate ?? 0;

        return view('settings.index', compact('setting', 'taxes', 'tax_rate'));
    }

    public function postIndex(Request $request)
    {
        $request->validate([
            'site_name' => 'required|max:255',
            'email' => 'required|email',
            'address' => 'required|max:255',
            'phone' => 'required',
        ]);

        if ($request->get('invoice_tax') == 1) {
            $request->validate(
                ['invoice_tax_id' => 'required'],
                ['invoice_tax_id.required' => 'When you enable Order Tax, you must select Order Tax rate']
            );
        }

        $setting = Setting::findOrFail(1);
        $setting->fill($request->only([
            'site_name', 'slogan', 'address', 'email', 'phone', 'owner_name', 
            'currency_code', 'theme', 'enable_purchaser', 'enable_customer', 
            'vat_no', 'pos_invoice_footer_text', 'dashboard_style'
        ]));
        $setting->product_tax = 0;
        $setting->invoice_tax = $request->get('invoice_tax') ? 1 : 0;
        $setting->invoice_tax_rate = $request->get('invoice_tax_id') ? Tax::find($request->get('invoice_tax_id'))->rate : 0;
        $setting->invoice_tax_type = $request->get('invoice_tax_id') ? Tax::find($request->get('invoice_tax_id'))->type : 2;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageSize = getimagesize($file);
            $width = $imageSize[0];
            $height = $imageSize[1];
            if ($width > 190 || $height > 34) {
                return redirect()->back()->with('warning', 'Invalid Image Size');
            }
            $file_name = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $destination_path = public_path('/uploads/site/');
            $file->move($destination_path, $file_name);
            $setting->site_logo = $file_name;
        }

        $setting->save();
        return redirect()->back()->with('success', trans('core.changes_saved'));
    }

    public function switchLocale(Request $request, $locale)
    {
        session(['APP_LOCALE' => $locale]);
        return redirect()->back();
    }

    public function getBackup()
    {
        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');

        $mysqli = new \mysqli($host, $username, $password, $database);
        $mysqli->select_db($database);
        $mysqli->query("SET NAMES 'utf8'");

        $queryTables = $mysqli->query('SHOW TABLES');
        $target_tables = [];
        while ($row = $queryTables->fetch_row()) {
            $target_tables[] = $row[0];
        }

        $content = '';
        foreach ($target_tables as $table) {
            $result = $mysqli->query('SELECT * FROM ' . $table);
            $fields_amount = $result->field_count;
            $rows_num = $mysqli->affected_rows;
            $res = $mysqli->query('SHOW CREATE TABLE ' . $table);
            $TableMLine = $res->fetch_row();
            $content .= "\n\n" . $TableMLine[1] . ";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetch_row()) {
                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\nINSERT INTO " . $table . " VALUES";
                    }

                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                        $content .= isset($row[$j]) ? '"' . $row[$j] . '"' : '""';
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";";
                    } else {
                        $content .= ",";
                    }
                    $st_counter++;
                }
            }
            $content .= "\n\n\n";
        }

        $backup_name = $database . "_" . date('H-i-s') . "_" . date('d-m-Y') . "_" . Str::random(5) . ".sql";
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $backup_name . "\"");
        echo $content;
        exit;
    }

    public function verifyPurchase()
    {
        return view('auth.verify-purchase');
    }

    public function postVerifyPurchase(Request $request)
    {
        Storage::disk('local')->put('purchase_code', trim($request->get('code')));
        return redirect()->to('install');
    }

    public function refresh()
    {
        \Artisan::call('migrate:refresh');
        \Artisan::call('migrate');
        \Artisan::call('db:seed');

        return redirect()->route('home')->with('success', 'Database refreshed & seeded!!');
    }

    public function postRefresh(Request $request)
    {
        $action = $request->get('action');

        if ($request->get('password') == 'nopassword') {
            if ($action == 'all') {
                \Artisan::call('migrate:refresh');
                \Artisan::call('db:seed');
                $success = "Database refreshed & seeded!!";
            } elseif ($action == 'only-migrate') {
                \Artisan::call('migrate');
                $success = "Database Migrated!!";
            } elseif ($action == 'only-seed') {
                \Artisan::call('db:seed');
                $success = "Database seeded!!";
            }

            return redirect()->route('home')->with('success', $success);
        } else {
            return redirect()->back()->with('warning', 'Wrong Password!!');
        }
    }
}
