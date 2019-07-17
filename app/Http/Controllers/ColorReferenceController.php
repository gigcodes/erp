<?php

namespace App\Http\Controllers;

use App\ColorNamesReference;
use App\ColorReference;
use Illuminate\Http\Request;



class ColorReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $colors = ColorNamesReference::whereNull('erp_name')->get();
        return view('color_references.index', compact('colors'));
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
            'colors' => 'required|array'
        ]);

        $colors = $request->get('colors');
        foreach ($colors as $key=>$color) {
            if (!$color) {
                continue;
            }
            $c = ColorNamesReference::find($key);
            $c->erp_name = $color;
            $c->save();
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ColorReference  $colorReference
     * @return \Illuminate\Http\Response
     */
    public function show(ColorReference $colorReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ColorReference  $colorReference
     * @return \Illuminate\Http\Response
     */
    public function edit(ColorReference $colorReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ColorReference  $colorReference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ColorReference $colorReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ColorReference  $colorReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(ColorReference $colorReference)
    {
        //
    }
}
