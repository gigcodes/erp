<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock;
use App\Setting;
use App\Product;
use App\PrivateView;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->input('orderby') == '')
          $orderby = 'asc';
      else
          $orderby = 'desc';

      $stocks = Stock::latest()->paginate(Setting::get('pagination'));

      return view('stock.index', [
        'stocks'  => $stocks,
        'orderby' => $orderby
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('stock.create');
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
        'courier'     => 'required|string|min:3|max:255',
        'from'        => 'sometimes|nullable|string|min:3|max:255',
        'date'        => 'sometimes|nullable',
        'awb'         => 'required|min:3|max:255',
        'l_dimension' => 'sometimes|nullable|numeric',
        'w_dimension' => 'sometimes|nullable|numeric',
        'h_dimension' => 'sometimes|nullable|numeric',
        'weight'      => 'sometimes|nullable|numeric',
        'pcs'         => 'sometimes|nullable|numeric',
      ]);

      Stock::create($request->except('_token'));

      return redirect()->route('stock.index')->with('success', 'You have successfully created stock');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $stock = Stock::find($id);

      return view('stock.show', [
        'stock' => $stock
      ]);
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
      $this->validate($request, [
        'courier'     => 'required|string|min:3|max:255',
        'from'        => 'sometimes|nullable|string|min:3|max:255',
        'date'        => 'sometimes|nullable',
        'awb'         => 'required|min:3|max:255',
        'l_dimension' => 'sometimes|nullable|numeric',
        'w_dimension' => 'sometimes|nullable|numeric',
        'h_dimension' => 'sometimes|nullable|numeric',
        'weight'      => 'sometimes|nullable|numeric',
        'pcs'         => 'sometimes|nullable|numeric',
      ]);

      Stock::find($id)->update($request->except('_token'));

      return redirect()->route('stock.show', $id)->with('success', 'You have successfully updated stock!');
    }

    public function privateViewing()
    {
      $private_views = PrivateView::paginate(Setting::get('pagination'));

      return view('instock.private-viewing', [
        'private_views' => $private_views
      ]);
    }

    public function privateViewingStore(Request $request)
    {
      $products = json_decode($request->products);

      foreach ($products as $product_id) {
        $private_view = new PrivateView;
        $private_view->customer_id = $request->customer_id;
        $private_view->date = $request->date;
        $private_view->save();

        $private_view->products()->attach($product_id);

        $product = Product::find($product_id);
        $product->supplier = '';
        $product->save();
      }

      return redirect()->route('customer.show', $request->customer_id)->with('success', 'You have successfully added products for private viewing!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Stock::find($id)->delete();

      return redirect()->route('stock.index')->with('success', 'You have successfully archived stock');
    }

    public function permanentDelete($id)
    {
      Stock::find($id)->forceDelete();

      return redirect()->route('stock.index')->with('success', 'You have successfully deleted stock');
    }
}
