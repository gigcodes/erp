<?php

namespace App\Http\Controllers;

use App\ProductLocation;
use Illuminate\Http\Request;

class ProductLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productLocation = ProductLocation::all();

        return view('product-location.index', compact('productLocation'));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $productLocation       = new ProductLocation();
        $productLocation->name = $request->get('name');
        $productLocation->save();

        return redirect()->back()->with('message', 'Location added successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productLocation = ProductLocation::find($id);

        if ($productLocation) {
            $productLocation->delete();
        }

        return redirect()->back()->with('message', 'Location deleted successfully!');
    }
}
