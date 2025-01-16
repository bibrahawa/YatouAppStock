<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Http\Requests\SubcategoryFormRequest;

class SubcategoryController extends Controller
{
    private $searchParams = ['name'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subcategories = Subcategory::orderBy('name', 'asc');

        if ($request->get('name')) {
            $subcategories->where('name', 'LIKE', '%' . $request->get('name') . '%');
        }

        return view('subcategories.index', ['subcategories' => $subcategories->paginate(20)]);
    }

    /**
     * Handle the post request for the index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->route('subcategories.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subcategory = new Subcategory;
        $categories = Category::pluck('category_name', 'id');
        return view('subcategories.form', ['subcategory' => $subcategory, 'categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SubcategoryFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubcategoryFormRequest $request)
    {
        $subcategory = $request->subcategory_id ? Subcategory::findOrFail($request->subcategory_id) : new Subcategory;
        $subcategory->name = ucfirst($request->get('name'));
        $subcategory->category_id = $request->get('category_id');
        $subcategory->save();

        $message = trans('core.changes_saved');
        return redirect()->route('subcategories.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subcategory  $subcategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Subcategory $subcategory)
    {
        $categories = Category::pluck('category_name', 'id');
        return view('subcategories.form', ['subcategory' => $subcategory, 'categories' => $categories]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subcategory  $subcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subcategory $subcategory)
    {
        if ($subcategory->products()->count() === 0) {
            $subcategory->delete();
            $success = trans('core.subcategory_deleted');
            return redirect()->back()->with('success', $success);
        }

        $warning = trans('core.subcategory_has_products');
        return redirect()->back()->with('warning', $warning);
    }

    /**
     * View the list of products of a subcategory.
     *
     * @param  \App\Models\Subcategory  $subcategory
     * @return \Illuminate\Http\Response
     */
    public function products(Subcategory $subcategory)
    {
        $products = $subcategory->products()->paginate(20);
        return view('subcategories.products', ['products' => $products]);
    }
}
