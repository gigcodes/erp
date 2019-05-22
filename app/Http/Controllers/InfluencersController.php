<?php

namespace App\Http\Controllers;

use App\Influencers;
use App\InfluencersDM;
use Illuminate\Http\Request;

class InfluencersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hashtags = Influencers::all();

        return view('instagram.influencers.index', compact('hashtags'));
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
            'name' => 'required',
        ]);

        $i = new Influencers();
        $i->username = $request->get('name');
        $i->brand_name = $request->get('brand_name');
        $i->blogger = $request->get('blogger');
        $i->city = $request->get('city');
        $i->save();

        return redirect()->back()->with('message', 'Added instagram influencer.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comments = InfluencersDM::all();

        return view('instagram.influencers.comments', compact('comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function edit(Influencers $influencers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Influencers $influencers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Influencers $influencers)
    {
        //
    }
}
