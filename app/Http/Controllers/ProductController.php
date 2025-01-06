<?php
namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Client;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    private $searchParams = ['name', 'code'];

    public function getIndex(Request $request)
    {
        $products = Product::orderBy('name', 'asc');

        if($request->get('name')) {
            $products->where('name', 'LIKE', '%' . $request->get('name') . '%');
        }

        if($request->get('code')) {
            $products->where('code','LIKE', '%' . $request->get('code') . '%');
        }

        return view('products.index')->withProducts($products->paginate(20));
    }

    public function postIndex(Request $request) {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([ProductController::class, 'getIndex'], $params);
    }

    public function getNewProduct()
    {
        $current_locale = app()->getLocale();
        app()->setLocale('ar');
        $secondary_lang = __('core');
        app()->setLocale($current_locale);

        $product = new Product;
        $categories = Category::pluck('category_name','id');
        $subcategories = Subcategory::pluck('name','id');
        $taxes = Tax::pluck('name', 'id');
        return view('products.form')
                    ->withProduct($product)
                    ->withCategories($categories)
                    ->withSubcategories($subcategories)
                    ->withTaxes($taxes)
                    ->with('secondary_lang',$secondary_lang);
    }

    public function postProduct(ProductRequest $request, Product $product)
    {
        $checkProductByCode = Product::where('code', $request->get('code'))->get();
        $checkProductByName = Product::where('name', $request->get('name'))->get();

        if($product->id == null){
            if($checkProductByName->count() != 0 ){
                $errors = $request->get('name'). " Already Exist!" ;
                return redirect()->back()->withInput($request->input())->withErrors($errors);
            }

            if($checkProductByCode->count() != 0){
                $errors = "Duplicate Product Code (" . $request->get('code'). ") !" ;
                return redirect()->back()->withInput($request->input())->withErrors($errors);
            }
        }

        $product->category_id = $request->get('category_id');
        $product->subcategory_id = $request->get('subcategory_id');
        $product->name = $request->get('name');
        $product->code = strtoupper($request->get('code'));
        $product->cost_price = $request->get('cost_price');
        $product->mrp = $request->get('mrp');
        $product->minimum_retail_price = $request->get('minimum_retail_price');
        $product->unit = $request->get('unit');
        $product->status = $request->get('status') ? $request->get('status') : 0;

        if($product->id){
            $current_stock = $product->quantity;
            $previous_opening_stock = $product->opening_stock;
        }else{
            $current_stock = 0;
            $previous_opening_stock = 0;
        }

        $product->quantity = $current_stock + $request->get('opening_stock') - $previous_opening_stock;
        $product->opening_stock = $request->get('opening_stock');
        $product->alert_quantity = $request->get('alert_quantity');

        if($request->hasFile('image')){
            $file = $request->file('image');
            $file_extension = $file->getClientOriginalExtension();
            $random_name = str()->random(12);
            $destination_path = public_path().'/uploads/products/';
            $filename = $random_name.'.'.$file_extension;
            $request->file('image')->move($destination_path,$filename);

            $product->image = $filename;
        }

        $message = __('core.changes_saved');
        $product->save();

        return redirect()->route('product.index')->withSuccess($message);
    }

    public function getEditProduct(Product $product)
    {
        $current_locale = app()->getLocale();
        app()->setLocale('ar');
        $secondary_lang = __('core');
        app()->setLocale($current_locale);

        $categories = Category::pluck('category_name', 'id');
        $subcategories = Subcategory::pluck('name','id');
        $taxes = Tax::pluck('name', 'id');
        return view('products.form')
                ->withProduct($product)
                ->withSubcategories($subcategories)
                ->withCategories($categories)
                ->withTaxes($taxes)
                ->with('secondary_lang',$secondary_lang);
    }

    public function getProductDetails(Product $product)
    {
        return view('products.details')->withProduct($product);
    }

    public function deleteProduct(Product $product)
    {
        if($product->sells->count() == 0 && $product->purchases->count() == 0){
            $product->delete();
            $success = __('core.deleted');
            return redirect()->back()->withSuccess($success);
        }else{
            $warning = __('core.product_has_sells');
            return redirect()->back()->withWarning($warning);
        }
    }

    public function updatePrice(Request $request){
        $id = $request->get('product_id');
        $product = Product::find($id);
        $product->cost_price = $request->get('cost_price');
        $product->mrp = $request->get('mrp');
        $product->save();

        $message = __('core.product_price_updated');
        return redirect()->route('product.index')->withSuccess($message);
    }

    public function alertProduct()
    {
        $products = Product::orderBy('name', 'asc')->get();
        $alert_products = [];
        foreach($products as $product){
            $alert_quantity = $product->alert_quantity;
            if($alert_quantity >= $product->quantity){
                $alert_products[$product->id]['name'] = $product->name;
                $alert_products[$product->id]['stock'] = $product->quantity." ".$product->unit;
                $alert_products[$product->id]['alert'] = $product->alert_quantity." ".$product->unit;
            }
        }

        return view('products.alert-products-list', compact('alert_products'));
    }

    public function printAllProduct(){
        $products = Product::all();
        return view('products.print', compact('products'));
    }

    public function printBarcode(){
        $products = Product::orderBy('name', 'asc')->where('status', 1)->select('id','name','cost_price', 'mrp','minimum_retail_price','quantity', 'tax_id', 'code')->get();

        return view('products.print-barcode', compact('products'));
    }

    public function printSingleBarcode(Product $product){
        return view('products.print-single-barcode', compact('product'));
    }

    public function printBarcodeByPurchase(){
        $purchases = Transaction::where('transaction_type', 'purchase')->select(['reference_no', 'id'])->get();
        return view('products.barcode.print-barcode-by-purchase', compact('purchases'));
    }

    public function uploadBulkProduct () {
        $categories = Category::pluck('category_name', 'id');
        return view('products.bulk-upload.form', compact('categories'));
    }

    public function postBulkProduct (Request $request) {
        $request->validate([
            'category_id' => 'required',
            'excel' => 'required',
        ]);

        if($request->hasFile('excel')){
            $file = $request->file('excel');
        }

        $category_id = $request->get('category_id');
        $rows = Excel::toCollection(new ProductImport, $file)->take(9999);

        foreach($rows[0] as $row){
            $rowData = $row->toArray();
            if ($rowData['name'] === 'name') {
                continue;
            }
            if($rowData['name'] && $rowData['cost_price'] && $rowData['mrp']){
                $code = $rowData['name'][0].strtoupper($rowData['name'][1]).mt_rand(1000, 99999);
                $code_exists = Product::where('code', $code)->count();
                if($code_exists > 0){
                    $code = $rowData['name'][0].strtoupper($rowData['name'][1]).mt_rand(1000, 99999);
                }

                $product = new Product;
                $product->name = $rowData['name'];
                $product->code = $code;
                $product->category_id = $category_id;
                $product->subcategory_id = null;
                $product->quantity = $rowData['opening_stock'];
                $product->details = $rowData['details'];
                $product->cost_price = $rowData['cost_price'];
                $product->mrp = $rowData['mrp'];
                $product->minimum_retail_price = $rowData['minimum_retail_price'];
                $product->unit = $rowData['unit'];
                $product->status = 1;
                $product->opening_stock = $rowData['opening_stock'];
                $product->save();
            }
        }

        $message = __('core.changes_saved');
        return redirect()->route('product.index')->withSuccess($message);
    }

    public function getExcelDownload () {
        $products = Product::all();
        return Excel::download(new ProductExport($products), 'product.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
