<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\CustomersController;
use App\Http\Controllers\Backend\ProductsController;
use App\Http\Controllers\Backend\SellController;
use App\Http\Controllers\ClientController;

// Définition des routes API
Route::get('/teste', function () {
    return response()->json(['message' => 'This is the teste route!']);
});

// Route pour obtenir les clients via API
Route::get('client', [ClientController::class, 'getClientAPI'])->name('client.api');

// Sauvegarde d'un client
Route::post('customer/save', [CustomersController::class, 'post'])->name('api.v1.customer.save');

// Produits fréquents
Route::get('products', [ProductsController::class, 'getFrequent'])->name('api.v1.products.frequent');

// Produits par catégorie
Route::get('category/{category}/products', [ProductsController::class, 'getCategoryProducts'])->name('api.v1.category.frequent');

// Produit par code-barres
Route::get('product-by-barcode/{barcode}', [ProductsController::class, 'getProductByBarcode'])->name('api.v1.product.by_barcode');

// Produit par recherche
Route::get('product-by-search/{search}', [ProductsController::class, 'getProductBySearch'])->name('api.v1.product.by_search');

// Sauvegarde des ventes par POS
Route::post('pos/save', [SellController::class, 'posPost'])->name('api.v1.sell.save');
