<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\ReturnSellController;
use App\Http\Controllers\posController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PurchaserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\DamageController;

Route::get('/val', function () {
    dd(user());
});

Route::get('/', [HomeController::class, 'getIndex'])->name('home');
Route::get('home', [HomeController::class, 'getIndex'])->name('home');
Route::get('logout',[UserController::class, 'logout'])->name('logout');
Route::get('lock', [UserController::class, 'lock'])->name('lock');
Route::get('locked', [UserController::class, 'locked'])->name('locked');
Route::post('locked', [UserController::class, 'unlock'])->name('unlock');

Route::group(['prefix'=>'admin'], function(){

    Route::get('locale/{locale}', [SettingsController::class, 'switchLocale'])->name('locale.set');

    Route::get('/cashin/today', [HomeController::class, 'todayCashIn'])->name('cashin.today');
    Route::get('/cashout/today', [HomeController::class, 'todayCashOut'])->name('cashout.today');
    Route::get('/transactions/today', [HomeController::class, 'todayTransaction'])->name('transactions.today');
    Route::get('/invoice/today', [HomeController::class, 'todayInvoice'])->name('invoice.today');
    Route::post('/invoice/today', [HomeController::class, 'postTodayInvoice']);

    Route::get('/bill/today', [HomeController::class, 'todaysBill'])->name('bill.today');
    Route::post('/bill/today', [HomeController::class, 'postTodayBill']);

    Route::get('/expense/today', [HomeController::class, 'todayExpense'])->name('expense.today');
    Route::get('profit/details', [HomeController::class, 'profitDetails'])->name('profit.details');

    Route::get('category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('category', [CategoryController::class, 'postIndex']);

    Route::get('category/new', [CategoryController::class, 'create'])->name('category.new');
    Route::post('category/new', [CategoryController::class, 'store'])->name('category.store');

    Route::get('category/{category}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('category/{category}', [CategoryController::class, 'store'])->name('category.update');
    Route::delete('category/delete/{category}', [CategoryController::class, 'destroy'])->name('category.delete');

    Route::get('product/ajaxData', [CategoryController::class, 'ajaxRequest'])->name('category.subcategory');

    Route::get('subcategory', [SubcategoryController::class, 'index'])->name('subcategories.index');
    Route::post('subcategory', [SubcategoryController::class, 'postIndex']);
    Route::get('subcategory/new', [SubcategoryController::class, 'create'])->name('subcategories.new');
    Route::post('subcategory/new', [SubcategoryController::class, 'store'])->name('subcategories.store');
    Route::get('subcategory/{subcategory}', [SubcategoryController::class, 'edit'])->name('subcategories.edit');
    Route::put('subcategory/{subcategory}', [SubcategoryController::class, 'store'])->name('subcategories.update');
    Route::get('subcategory/delete/{subcategory}', [SubcategoryController::class, 'destroy'])->name('subcategories.delete');
    Route::get('subcategory/{subcategory}/product', [SubcategoryController::class, 'products'])->name('subcategory.products');


    Route::get('product', [ProductController::class, 'getIndex'])->name('product.index');
    Route::post('product', [ProductController::class, 'postIndex']);

    Route::get('product/new', [ProductController::class, 'getNewProduct'])->name('product.new');
    Route::post('product/new', [ProductController::class, 'postProduct'])->name('product.post');

    Route::get('product/{product}', [ProductController::class, 'getEditProduct'])->name('product.edit');
    Route::put('product/{product}', [ProductController::class, 'postProduct'])->name('product.update');

    Route::get('product/{product}/details', [ProductController::class, 'getProductDetails'])->name('product.details');

    Route::delete('product/delete/{product}', [ProductController::class, 'deleteProduct'])->name('product.delete');

    Route::get('product/print/all', [ProductController::class, 'printAllProduct'])->name('product.printall');

    Route::get('damaged-products', [DamageController::class, 'getDamageList'])->name('damage.index');
    Route::get('damaged-products/add', [DamageController::class, 'addDamageItem'])->name('damage.new');
    Route::post('damaged-products/add', [DamageController::class, 'postDamageItem'])->name('damage.post');

    Route::get('product/{product}/print/barcode', [ProductController::class, 'printSingleBarcode'])->name('single.print_barcode');
    Route::get('product/print/barcode', [ProductController::class, 'printBarcode'])->name('product.print_barcode');
    Route::get('print-barcode-by-purchase', [ProductController::class, 'printBarcodeByPurchase'])->name('product.print_barcode_by_purchase');

    Route::post('product/price/update', [ProductController::class, 'updatePrice'])->name('product.price.update');

    Route::get('product/alert/all', [ProductController::class, 'alertProduct'])->name('product.alert');

    Route::get('upload-bulk-product', [ProductController::class, 'uploadBulkProduct'])->name('product.upload');
    Route::post('upload-bulk-product', [ProductController::class, 'postBulkProduct'])->name('post.product.upload');

    Route::get('download/excel', [ProductController::class, 'getExcelDownload'])->name('product.export.excel');

    Route::get('purchase', [PurchaseController::class, 'getIndex'])->name('purchase.index');
    Route::post('purchase', [PurchaseController::class, 'postIndex']);

    Route::get('purchase/new', [PurchaseController::class, 'getNewPurchase'])->name('purchase.item');
    Route::post('purchase/new', [PurchaseController::class, 'postPurchase'])->name('purchase.post');

    Route::get('purchase/details/{transaction}', [PurchaseController::class, 'purchaseDetails'])->name('purchase.details');
    Route::get('purchase/{transaction}/invoice', [PurchaseController::class, 'purchasingInvoice'])->name('purchase.invoice');
    Route::post('purchase/delete',[PurchaseController::class, 'deletePurchase'])->name('purchase.delete');

    Route::get('sell', [SellController::class, 'getIndex'])->name('sell.index');
    Route::post('sell', [SellController::class, 'postIndex']);

    Route::get('sell/new', [SellController::class, 'getNewsell'])->name('sell.form');
    Route::post('sell/new', [SellController::class, 'postSell'])->name('sell.post');

    Route::get('sells/details/{transaction}', [SellController::class, 'sellsDetails'])->name('sells.details');

    Route::get('sell/{transaction}/invoice', [SellController::class, 'sellingInvoice'])->name('sell.invoice');

    Route::get('sell/return/{transaction}', [SellController::class, 'returnSell'])->name('sell.return');
    Route::post('sell/return/{transaction}', [SellController::class, 'returnSellPost'])->name('return.post');

    Route::get('return/sell', [ReturnSellController::class, 'getReturnSell'])->name('return.sell');
    Route::get('return/search/invoice/{invoice_no}', [ReturnSellController::class, 'searchInvoice'])->name('search.invoice');
    Route::post('return/new', [ReturnSellController::class, 'postReturnSell'])->name('return.post');

    Route::delete('sell/delete/{transaction}', [SellController::class, 'deleteSell'])->name('sell.delete');

    Route::get('pos', [posController::class, 'getPOS'])->name('sell.pos');
    Route::post('pos/sell/save', [posController::class, 'posPost'])->name('api.v1.sell.save');
    Route::get('pos/sell/invoice/{id}', [posController::class, 'posInvoice'])->name('pos.invoice');

    Route::post('payment', [PaymentController::class, 'postPayment'])->name('payment.post');

    Route::get('transaction/all', [PaymentController::class, 'getIndex'])->name('payment.list');
    Route::post('transaction/all', [PaymentController::class, 'postIndex']);

    Route::get('print/{payment}/receipt', [PaymentController::class, 'printReceipt'])->name('payment.voucher');

    Route::get('client', [ClientController::class, 'getIndex'])->name('client.index');
    Route::post('client', [ClientController::class, 'postIndex']);

    Route::get('client/new', [ClientController::class, 'getNewClient'])->name('client.new');
    Route::get('client/{client}', [ClientController::class, 'getEditClient'])->name('client.edit');

    Route::post('client/save', [ClientController::class, 'postClient'])->name('client.save');

    Route::get('client/invoices/{client}', [ClientController::class, 'getClientInvoices'])->name('client.invoices');
    Route::get('client/{client}/details', [ClientController::class, 'getDetailsClient'])->name('client.details');
    Route::get('client/transaction/{id}', [ClientController::class, 'paymentList'])->name('client.payment.list');
    Route::delete('client/delete/{client}', [ClientController::class, 'deleteClient'])->name('client.delete');

    Route::get('purchaser', [PurchaserController::class, 'getIndex'])->name('purchaser.index');
    Route::get('purchaser/{purchaser}', [PurchaserController::class, 'getEditPurchaser'])->name('purchaser.edit');
    Route::post('purchaser', [PurchaserController::class, 'postIndex']);
    Route::get('purchaser/new', [PurchaserController::class, 'getNewPurchaser'])->name('purchaser.new');
    Route::post('purchaser/new', [PurchaserController::class, 'postPurchaser'])->name('purchaser.store');

    Route::get('user', [UserController::class, 'getIndex'])->name('user.index');
    Route::post('user', [UserController::class, 'postIndex']);
    Route::get('user/new', [UserController::class, 'getNewUser'])->name('user.new');
    Route::post('user/new', [UserController::class, 'postUser'])->name('user.store');
    Route::get('user/profile', [UserController::class, 'viewProfile'])->name('user.profile');
    Route::post('user/verify-old-password', [UserController::class, 'verifyOldPassword'])->name('user.old-password');
    Route::post('user/profile', [UserController::class, 'postProfile'])->name('user.profile.post');
    Route::post('change/password', [UserController::class, 'changePassword'])->name('change.password');
    Route::post('user/status', [UserController::class, 'postStatus'])->name('user.status');
    Route::get('user/{user}/edit', [UserController::class, 'getEditUser'])->name('user.edit');
    Route::put('user/{user}/update', [UserController::class, 'postUser'])->name('user.store');

    Route::get('expense', [ExpenseController::class, 'getIndex'])->name('expense.index');
    Route::post('expense', [ExpenseController::class, 'postIndex'])->name('expense.post');

    Route::post('expense/search', [ExpenseController::class, 'postSearch'])->name('expense.search');

    Route::post('expense/edit', [ExpenseController::class, 'editExpense'])->name('expense.edit');
    Route::post('expense/delete', [ExpenseController::class, 'deleteExpense'])->name('expense.delete');

    Route::get('expense-category', [ExpenseCategoryController::class, 'getIndex'])->name('expense.category.index');
    Route::post('expense-category', [ExpenseCategoryController::class, 'postIndex'])->name('expense.category.post');
    Route::post('expense-category/edit', [ExpenseCategoryController::class, 'editExpenseCategory'])->name('expense.category.edit');
    Route::post('expense-category/delete', [ExpenseCategoryController::class, 'deleteExpenseCategory'])->name('expense.category.delete');

    Route::get('cash/details', [HomeController::class, 'cashDetails'])->name('cash.details');
    Route::post('cash_register', [ExpenseController::class, 'cashRegister'])->name('cash_register.post');

    Route::get('settings', [SettingsController::class, 'getIndex'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'postIndex'])->name('settings.post');
    Route::get('settings/backup', [SettingsController::class, 'getBackup'])->name('settings.backup');

    Route::get('report', [ReportingController::class, 'getIndex'])->name('report.index');
    Route::post('report/purchase-report', [ReportingController::class, 'postPurchaseReport'])->name('report.purchase');
    Route::post('report/sells-report', [ReportingController::class, 'postSellsReport'])->name('report.sells');
    Route::post('report/product-report', [ReportingController::class, 'postProductReport'])->name('report.product');
    Route::post('report/client-report', [ReportingController::class, 'postClientReport'])->name('report.client');
    Route::post('report/stock-report', [ReportingController::class, 'postStockReport'])->name('report.stock');
    Route::post('report/category-report', [ReportingController::class, 'postCategoryReport'])->name('report.category');
    Route::post('report/subcategory-report', [ReportingController::class, 'postSubCategoryReport'])->name('report.subcategory');
    Route::post('report/branch', [ReportingController::class, 'postBranchReport'])->name('report.branch');
    Route::post('report/profit', [ReportingController::class, 'postProfitReport'])->name('report.profit');
    Route::post('report/due-report', [ReportingController::class, 'postDueReport'])->name('report.due');

    Route::get('role', [RolePermissionController::class, 'getIndex'])->name('role.index');
    Route::post('role', [RolePermissionController::class, 'postRole'])->name('role.post');

    Route::get('role/{role}/permission', [RolePermissionController::class, 'setRolePermissions'])->name('role.permission');
    Route::post('role/{role}/permission', [RolePermissionController::class, 'postRolePermissions'])->name('post.role.permission');

    Route::get('vat', [TaxController::class, 'getIndex'])->name('tax.index');
    Route::post('vat', [TaxController::class, 'postTax'])->name('tax.post');
    Route::post('vat/delete',[TaxController::class, 'deleteTax'])->name('tax.delete');
    Route::post('vat/edit', [TaxController::class, 'editTax'])->name('tax.edit');

    Route::get('warehouse', [WarehouseController::class, 'getIndex'])->name('warehouse.index');

    Route::get('warehouse/new', [WarehouseController::class, 'getNewWarehouse'])->name('warehouse.new');
    Route::post('warehouse/new', [WarehouseController::class, 'postWarehouse'])->name('warehouse.post');

    Route::get('warehouse/{warehouse}', [WarehouseController::class, 'getWarehouse'])->name('warehouse.show');

    Route::post('warehouse/{warehouse}', [WarehouseController::class, 'postWarehouse'])->name('warehouse.post');
	Route::delete('warehouse/delete/{warehouse}', [WarehouseController::class, 'deleteWarehouse'])->name('warehouse.delete');


	// Products by purchase ID
	Route::get('api/v1/purchase/{id}/products', [PurchaseController::class, 'getProductsByPurchaseId']);

});

//Route for Artisan commands
Route::get('refreshpos', [SettingsController::class, 'refresh']);
Route::post('refreshpos', [SettingsController::class, 'postRefresh']);

Auth::routes();
