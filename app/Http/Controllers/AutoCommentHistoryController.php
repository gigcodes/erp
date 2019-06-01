<?php

namespace App\Http\Controllers;

use App\AutoCommentHistory;
use App\AutoReplyHashtags;
use Illuminate\Http\Request;

class AutoCommentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = AutoCommentHistory::orderBy('created_at', 'DESC')->paginate(50);
        $hashtags = AutoReplyHashtags::all();


        return view('instagram.auto_comments.report', compact('comments', 'hashtags'));
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
     * @param  \App\AutoCommentHistory  $autoCommentHistory
     * @return \Illuminate\Http\Response
     */
    public function show(AutoCommentHistory $autoCommentHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AutoCommentHistory  $autoCommentHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(AutoCommentHistory $autoCommentHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AutoCommentHistory  $autoCommentHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AutoCommentHistory $autoCommentHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AutoCommentHistory  $autoCommentHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutoCommentHistory $autoCommentHistory)
    {
        //
    }
}
