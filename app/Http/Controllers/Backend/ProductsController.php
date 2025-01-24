<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class ProductsController extends Controller
{
    /**
     * Get frequent products
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFrequent(Request $request)
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $products = Product::whereHas('sells', function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end])
                ->selectRaw('product_id, count(*) as sells')
                ->groupBy('product_id')
                ->orderBy('sells', 'desc');
        })->take(25)->get();

        if (count($products) == 0) {
            $products = Product::latest()->take(25)->get();
        }

        return response()->json($products);
    }

    /**
     * Get products by category
     *
     * @param  \App\Models\Category  $category
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCategoryProducts(Category $category, Request $request)
    {
        $products = $category->products()->orderBy('name', 'asc')->get();

        return response()->json($products);
    }

    /**
     * Get product by barcode
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $barcode
     * @return \Illuminate\Http\Response
     */
    public function getProductByBarcode(Request $request, $barcode)
    {
        $product = Product::where('code', $barcode)->first();

        $found = (bool) $product;

        $data = $found ? $product : [];

        return response()->json([
            'found' => $found,
            'product' => $data,
        ]);
    }

    /**
     * Get product by search
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $search
     * @return \Illuminate\Http\Response
     */
    public function getProductBySearch(Request $request, $search)
    {
        $products = Product::orderBy('name', 'asc');

        $products->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', '%' . $search . '%');
        });

        $found = $products->count() > 0 ? "yes" : "no";

        if ($found == "no") {
            $products = Product::orderBy('name', 'asc')->where('code', 'LIKE', '%' . $search . '%');
        }

        return response()->json($products->get());
    }
}

