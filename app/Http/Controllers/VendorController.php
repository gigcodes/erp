<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\Setting;
use Illuminate\Http\Request;

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
      return view('vendors.product');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $vendor = Vendor::find($id);

      $vendor->delete();

      return redirect()->route('vendor.index')->withSuccess('You have successfully deleted a vendor');
    }
}
