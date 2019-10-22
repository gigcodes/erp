<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\SupplierCategory;
use App\Http\Controllers\Controller;
use DB;


class SupplierCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $suppliercategory = SupplierCategory::orderBy('id', 'DESC')->paginate(10);
        return view('supplier-category.index', compact('suppliercategory'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('supplier-category.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:supplier_category,name',
        ]);


        $department = SupplierCategory::create(['name' => $request->input('name')]);

        return redirect()->route('supplier-category.index')
            ->with('success', 'Supplier Category created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = SupplierCategory::find($id);


        return view('supplier-category.edit', compact('category'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);


        $department = SupplierCategory::find($id);
        $department->name = $request->input('name');
        $department->save();

        return redirect()->route('supplier-category.index')
            ->with('success', 'Supplier Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("supplier_category")->where('id', $id)->delete();
        return redirect()->route('supplier-category.index')
            ->with('success', 'Supplier Category deleted successfully');
    }
}