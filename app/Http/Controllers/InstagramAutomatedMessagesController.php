<?php

namespace App\Http\Controllers;

use App\AutomatedMessages;
use App\InstagramAutomatedMessages;
use Illuminate\Http\Request;

class InstagramAutomatedMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $replies = InstagramAutomatedMessages::all();

        return view('instagram.am.index', compact('replies'));

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
            'type' => 'required',
            'sender_type' => 'required',
            'receiver_type' => 'required',
            'reusable' => 'required',
            'message' => 'required',
        ]);

        $reply = new InstagramAutomatedMessages();
        $reply->type = $request->get('type');
        $reply->sender_type = $request->get('sender_type');
        $reply->receiver_type = $request->get('receiver_type');
        $reply->reusable = $request->get('reusable');
        $reply->message = $request->get('message');
        $reply->status = 1;
        $reply->save();


        return redirect()->back()->with('message', 'The automated reply added successfully!');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InstagramAutomatedMessages  $instagramAutomatedMessages
     * @return \Illuminate\Http\Response
     */
    public function show(InstagramAutomatedMessages $instagramAutomatedMessages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InstagramAutomatedMessages  $instagramAutomatedMessages
     * @return \Illuminate\Http\Response
     */
    public function edit(InstagramAutomatedMessages $instagramAutomatedMessages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InstagramAutomatedMessages  $instagramAutomatedMessages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InstagramAutomatedMessages $instagramAutomatedMessages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InstagramAutomatedMessages  $instagramAutomatedMessages
     * @return \Illuminate\Http\Response
     */
    public function destroy(InstagramAutomatedMessages $instagramAutomatedMessages)
    {
        //
    }
}
