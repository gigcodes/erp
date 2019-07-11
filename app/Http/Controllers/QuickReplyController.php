<?php

namespace App\Http\Controllers;

use App\QuickReply;
use Illuminate\Http\Request;

class QuickReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $replies = QuickReply::all();

        return view('quick_reply.index', compact('replies'));
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
            'text' => 'required'
        ]);

        $r = new QuickReply();
        $r->text = $request->get('text');
        $r->save();

        return redirect()->back()->with('message', 'Quick reply added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reply = QuickReply::findOrFail($id);

        $reply->delete();

        return redirect()->back()->with('message', 'Deleted successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function edit(QuickReply $quickReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuickReply $quickReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuickReply $quickReply)
    {
        //
    }
}
