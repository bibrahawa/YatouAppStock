<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private $searchParams = ['name'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Category::query();
        if ($request->get('name')) {
            $query->whereRaw('LOWER(category_name) LIKE ?', ['%' . strtolower($request->get('name')) . '%']);
        }
        $query->orderBy('category_name', 'asc');

        $categories = $query->paginate(20);
        return view('categories.index', compact('categories'));
    }

    /**
     * Post method of index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([CategoryController::class, 'index'], $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = new Category;
        return view('categories.form', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {

        $category = $request->id ? Category::find($request->id) : new Category;
        $category->category_name = $request->get('name');
        $category->save();

        $message = trans('core.changes_saved');
        return redirect()->route('category.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if ($category->subcategories()->count() == 0 && $category->products()->count() == 0) {
            $category->delete();
            $success = trans('core.deleted');
            return redirect()->back()->with('success', $success);
        } else {
            $warning = trans('core.category_has_subcategories');
            return redirect()->back()->with('warning', $warning);
        }
    }

    /**
     * Load Subcategory of a category
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loadSubcategories(Request $request)
    {
        $category_id = $request->get('categoryID');
        $subcategories = Subcategory::where('category_id', $category_id)->get();
        return view('categories.subcategory', compact('subcategories'));
    }
}
