<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Setting;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

use App\QuickSell;

class QuickSellController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $products = Product::where('quick_product', 1)->paginate(Setting::get('pagination'));

      return view('quicksell.index')->withProducts($products);
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
      $this->validate($request,[
  			'sku'    => 'sometimes|unique:products',
  			'images.*' => 'required | mimes:jpeg,bmp,png,jpg',
  		]);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $product = new Product();

          if ($request->sku) {
            $product->sku = $request->sku;
          } else {
            $product->sku = $this->generateRandomSku();
          }

      		$product->quick_product = 1;
      		$product->save();

      		$media = MediaUploader::fromSource($image)->upload();
      		$product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('quicksell.index')->with('success', 'You have successfully uploaded image');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
      $this->validate($request,[
  			'images.*' => 'sometimes | mimes:jpeg,bmp,png,jpg',
  		]);

      $product = Product::find($id);

      $product->supplier = $request->supplier;
      $product->price = $request->price;
      $product->size = $request->size;
      $product->save();

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
      		$media = MediaUploader::fromSource($image)->upload();
      		$product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('quicksell.index')->with('success', 'You have successfully updated Quick Product');
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

    public function generateRandomSku()
    {
      $sku = Product::where('sku', 'LIKE', "%QCKPRO%")->latest()->select(['sku'])->first();

      if ($sku) {
        $exploded = explode('-', $sku->sku);
        $new_sku = 'QCKPRO-' . (intval( $exploded[1] ) + 1);

  			return $new_sku;
      }

      return 'QCKPRO-000001';
    }
}
