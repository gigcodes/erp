<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\VendorProduct;
use App\Setting;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $vendors = Vendor::latest()->paginate(Setting::get('pagination'));

      return view('vendors.index', [
        'vendors' => $vendors
      ]);
    }

    public function product()
    {
      $products = VendorProduct::with('vendor')->latest()->paginate(Setting::get('pagination'));
      $vendors = Vendor::select(['id', 'name'])->get();

      return view('vendors.product', [
        'products'  => $products,
        'vendors'  => $vendors
      ]);
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
        'name'          => 'required|string|max:255',
        'address'       => 'sometimes|nullable|string',
        'phone'         => 'sometimes|nullable|numeric',
        'email'         => 'sometimes|nullable|email',
        'social_handle' => 'sometimes|nullable',
        'gst'           => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');

      Vendor::create($data);

      return redirect()->route('vendor.index')->withSuccess('You have successfully saved a vendor!');
    }

    public function productStore(Request $request)
    {
      $this->validate($request, [
        'vendor_id'       => 'required|numeric',
        'images.*'        => 'sometimes|nullable|image',
        'date_of_order'   => 'required|date',
        'name'            => 'required|string|max:255',
        'qty'             => 'sometimes|nullable|numeric',
        'price'           => 'sometimes|nullable|numeric',
        'payment_terms'   => 'sometimes|nullable|string',
        'delivery_date'   => 'sometimes|nullable|date',
        'received_by'     => 'sometimes|nullable|string',
        'approved_by'     => 'sometimes|nullable|string',
        'payment_details' => 'sometimes|nullable|string'
      ]);

      $data = $request->except('_token');

      $product = VendorProduct::create($data);

      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('vendor.product.index')->withSuccess('You have successfully saved a vendor product!');
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
      $this->validate($request, [
        'name'          => 'required|string|max:255',
        'address'       => 'sometimes|nullable|string',
        'phone'         => 'sometimes|nullable|numeric',
        'email'         => 'sometimes|nullable|email',
        'social_handle' => 'sometimes|nullable',
        'gst'           => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');

      Vendor::find($id)->update($data);

      return redirect()->route('vendor.index')->withSuccess('You have successfully updated a vendor!');
    }

    public function productUpdate(Request $request, $id)
    {
      $this->validate($request, [
        'vendor_id'       => 'required|numeric',
        'images.*'        => 'sometimes|nullable|image',
        'date_of_order'   => 'required|date',
        'name'            => 'required|string|max:255',
        'qty'             => 'sometimes|nullable|numeric',
        'price'           => 'sometimes|nullable|numeric',
        'payment_terms'   => 'sometimes|nullable|string',
        'delivery_date'   => 'sometimes|nullable|date',
        'received_by'     => 'sometimes|nullable|string',
        'approved_by'     => 'sometimes|nullable|string',
        'payment_details' => 'sometimes|nullable|string'
      ]);

      $data = $request->except('_token');

      $product = VendorProduct::find($id);
      $product->update($data);

      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('vendor.product.index')->withSuccess('You have successfully updated a vendor product!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $vendor = Vendor::find($id);

      foreach ($vendor->products as $product) {
        $product->detachMediaTags(config('constants.media_tags'));
      }

      $vendor->products()->delete();
      $vendor->delete();

      return redirect()->route('vendor.index')->withSuccess('You have successfully deleted a vendor');
    }

    public function productDestroy($id)
    {
      $product = VendorProduct::find($id);

      $product->detachMediaTags(config('constants.media_tags'));
      $product->delete();

      return redirect()->route('vendor.product.index')->withSuccess('You have successfully deleted a vendor product!');
    }
}
