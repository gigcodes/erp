<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Password;
use App\Setting;
use App\SkuFormat;
use App\User;
use Illuminate\Http\Request;
use DataTables;
use Input;


class SkuFormatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('title','asc')->get();
        $brands = Brand::orderBy('name','asc')->get();
        $skus = SkuFormat::all();
        return view('sku-format.index',compact('categories','brands','skus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $this->validate($request, [
           'category_id'   => 'required',
           'brand_id'       => 'required',
           'sku_format'  => 'required|min:3|max:255',
       ]);

        $sku = new SkuFormat();
        $sku->category_id = $request->category_id;
        $sku->brand_id = $request->brand_id;
        $sku->sku_examples = $request->sku_examples;
        $sku->sku_format = $request->sku_format;
        $sku->save();

       return redirect()->back()->withSuccess('You have successfully saved SKU');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SkuFormat  $skuFormat
     * @return \Illuminate\Http\Response
     */
    public function show(SkuFormat $skuFormat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SkuFormat  $skuFormat
     * @return \Illuminate\Http\Response
     */
    public function edit(SkuFormat $skuFormat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SkuFormat  $skuFormat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    
       $this->validate($request, [
           'category_id'   => 'required',
           'brand_id'       => 'required',
           'sku_format'  => 'required|min:3|max:255',
       ]);

        $sku = SkuFormat::findorfail($request->id);
        $sku->category_id = $request->category_id;
        $sku->brand_id = $request->brand_id;
        $sku->sku_examples = $request->sku_examples;
        $sku->sku_format = $request->sku_format;
        $sku->update();

       return response()->json(['success' => 'success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SkuFormat  $skuFormat
     * @return \Illuminate\Http\Response
     */
    public function destroy(SkuFormat $skuFormat)
    {
        //
    }

    public function getData(Request $request){

        if(!empty($request->from_date)) {
            $skulogs = SkuFormat::select(['brand_id', 'category_id','sku_examples','sku_format'])->whereBetween('created_at', array($request->from_date, $request->to_date))->get();
            return Datatables::of($skulogs)
                ->addColumn('category', function ($skulogs) {
                    return '<h6>' . $skulogs->category->name . '</h6>';
                })
                ->addColumn('brand', function ($skulogs) {
                    return $skulogs->brand->name;
                })
                ->addColumn('actions', function ($skulogs) {
                    return '<button class=btn btn-default" onclick="editSKU('.$skulogs->id.')">Edit</button>';
                })
                ->rawColumns(['category'])
                ->rawColumns(['brand'])
                ->rawColumns(['actions'])
               ->make(true);
        }else{
            $skulogs = SkuFormat::select(['id','brand_id', 'category_id', 'sku_examples','sku_format']);
            return Datatables::of($skulogs)
                ->addColumn('category', function ($skulogs) {
                    return $skulogs->category->title;
                })
                ->addColumn('brand', function ($skulogs) {
                    return $skulogs->brand->name;
                })
                ->addColumn('actions', function ($skulogs) {
                    return '<button class=btn btn-default" onclick="editSKU('.$skulogs->id.')">Edit</button>';
                })
                ->rawColumns(['category'])
                ->rawColumns(['brand'])
                ->rawColumns(['actions'])
                ->make(true);

        }
    }
}
