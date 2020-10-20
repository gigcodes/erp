<?php

namespace Modules\StoreWebsite\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\StoreWebsiteColor;
use App\StoreWebsite;
use App\Colors;
use Log;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Colors | Store Website";
        $store_colors = StoreWebsiteColor::all();

        // Check for keyword search
        if ($request->keyword != null) {
            $store_colors = $store_colors->where("erp_color", "like", "%" . $request->keyword . "%");
        }

        return view('storewebsite::color.index', [
            'erp_colors' => (new Colors())->all(),
            'store_websites' => StoreWebsite::pluck('title', 'id')->toArray(),
            'store_colors' => $store_colors,
            'title' => $title
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
            'store_website_id' => 'required|integer',
            'store_color' => 'required|string|max:255',
            'erp_color' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        StoreWebsiteColor::create( $data );
        return redirect()->route('store-website.color.list')->withSuccess('New Color added successfully.' );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
            'store_website_id' => 'required|integer',
            'store_color' => 'required|string|max:255',
            'erp_color' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        Log::debug(print_r($data,true));
        StoreWebsiteColor::find($id)->update($data);

        return redirect()->route('store-website.color.list')->withSuccess('You have successfully updated a store color!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $storeColor = StoreWebsiteColor::where("id", $id)->first();
        if ($storeColor) {
            $storeColor->delete();
            return redirect()->route('store-website.color.list')->withSuccess('You have successfully deleted a store color');
        }
        return redirect()->route('store-website.color.list')->withErrors('Unable to delete a store color');
    }
}
