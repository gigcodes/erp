<?php

namespace App\Http\Controllers;

use App\AutoReplyHashtags;
use Illuminate\Http\Request;

class AutoReplyHashtagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $h = new AutoReplyHashtags();
        $h->text = $request->get('hashtag');
        $h->type = 'hashtag';
        $h->status = 1;
        $h->save();

        return redirect()->back()->with('action', 'Comment Target hashtag added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function show(AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function edit(AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }
}
