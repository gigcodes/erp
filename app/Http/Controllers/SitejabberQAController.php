<?php

namespace App\Http\Controllers;

use App\Account;
use App\Review;
use App\SitejabberQA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SitejabberQAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sjs = SitejabberQA::where('type', 'question')->get();

        return view('sitejabber.index', compact('sjs'));

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
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function show(SitejabberQA $sitejabberQA)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function edit(SitejabberQA $sitejabberQA)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $sj = SitejabberQA::findOrFail($id);

        $sju = new SitejabberQA();
        $sju->parent_id = $id;
        $sju->url = $sj->url;
        $sju->text = $request->get('reply');
        $sju->type = 'reply';
        $sju->author = 'TBD';
        $sju->status = 0;
        $sju->save();

        return redirect()->back()->with('message', 'Comment added successfully! And will be posted anytime within 24 hours!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function destroy(SitejabberQA $sitejabberQA)
    {
        //
    }

    public function accounts() {
        $accounts = Account::where('platform', 'sitejabber')->get();

        return view('sitejabber.accounts', compact('accounts'));
    }

    public function reviews() {
        $reviews = Review::where('platform', 'sitejabber')->get();

        return view('sitejabber.reviews', compact('reviews'));
    }
}
