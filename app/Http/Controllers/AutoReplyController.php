<?php

namespace App\Http\Controllers;

use App\AutoReply;
use App\Setting;
use Illuminate\Http\Request;

class AutoReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $auto_replies = AutoReply::paginate(Setting::get('pagination'));
      $show_automated_messages = Setting::get('show_automated_messages');

      return view('autoreplies.index', [
        'auto_replies'  => $auto_replies,
        'show_automated_messages'  => $show_automated_messages
      ]);
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
        'keyword' => 'required|string',
        'reply'   => 'required|min:3|string'
      ]);

      $auto_reply = new AutoReply;
      $auto_reply->keyword = $request->keyword;
      $auto_reply->reply = $request->reply;
      $auto_reply->save();

      return redirect()->route('autoreply.index')->withSuccess('You have successfully created auto reply!');
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
        'keyword' => 'required|string',
        'reply'   => 'required|min:3|string'
      ]);

      $auto_reply = AutoReply::find($id);
      $auto_reply->keyword = $request->keyword;
      $auto_reply->reply = $request->reply;
      $auto_reply->save();

      return redirect()->route('autoreply.index')->withSuccess('You have successfully updated auto reply!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      AutoReply::find($id)->delete();

      return redirect()->route('autoreply.index')->withSuccess('You have successfully deleted auto reply!');
    }
}
