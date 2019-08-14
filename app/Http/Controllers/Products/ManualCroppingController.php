<?php

namespace App\Http\Controllers\Products;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManualCroppingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::where('manual_crop', 1)
            ->where('is_crop_approved', 0)
            ->whereIn('id', DB::table('user_manual_crop')->where('user_id', Auth::id())->pluck('product_id')->toArray())
            ->get();

        return view('products.crop.manual.index', compact('products'));
    }

    public function assignProductsToUser() {
        $currentUser = Auth::user();

        $reservedProductIds = DB::table('user_manual_crop')->pluck('product_id')->toArray();
        $products = Product::whereNotIn('id', $reservedProductIds)->where('manual_crop', 1)->where('is_approved', 0)->take(25)->get();

        if ($products->count() === 0) {
            return redirect()->back()->with('message', 'There are no products to be assigned!');
        }

        $currentUser->manualCropProducts()->attach($products);

        return redirect()->back()->with('message', $products->count() .' new products assigned successfully!');

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->action('Products\ManualCroppingController@index')->with('message', 'The product you were trying to open does not exist anymore.');
        }

        return view('products.crop.manual.show', compact('product'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
