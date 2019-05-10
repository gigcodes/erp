<?php

namespace App\Http\Controllers;

use Auth;
use App\AutoReply;
use App\Setting;
use App\ScheduledMessage;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AutoReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $auto_replies = AutoReply::where('type', 'simple')->latest()->get()->groupBy('reply')->toArray();
      $priority_customers_replies = AutoReply::where('type', 'priority-customer')->latest()->paginate(Setting::get('pagination'), ['*'], 'priority-page');
      $show_automated_messages = Setting::get('show_automated_messages');

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = Setting::get('pagination');
  		$currentItems = array_slice($auto_replies, $perPage * ($currentPage - 1), $perPage);

  		$auto_replies = new LengthAwarePaginator($currentItems, count($auto_replies), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

      return view('autoreplies.index', [
        'auto_replies'  => $auto_replies,
        'priority_customers_replies'  => $priority_customers_replies,
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
        'type'          => 'required|string',
        'keyword'       => 'sometimes|nullable|string',
        'reply'         => 'required|min:3|string',
        'sending_time'  => 'sometimes|nullable|date',
        'repeat'        => 'sometimes|nullable|string'
      ]);

      $exploded = explode(',', $request->keyword);

      foreach ($exploded as $keyword) {
        $auto_reply = new AutoReply;
        $auto_reply->type = $request->type;
        $auto_reply->keyword = trim($keyword);
        $auto_reply->reply = $request->reply;
        $auto_reply->sending_time = $request->sending_time;
        $auto_reply->repeat = $request->repeat;
        $auto_reply->save();
      }

      if ($request->type == 'priority-customer') {
        if ($request->repeat == '') {
          $customers = Customer::where('is_priority', 1)->get();

          foreach ($customers as $customer) {
            ScheduledMessage::create([
              'user_id'       => Auth::id(),
              'customer_id'   => $customer->id,
              'message'       => $auto_reply->reply,
              'sending_time'  => $request->sending_time
            ]);
          }
        }
      }

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
        'type'          => 'required|string',
        'keyword'       => 'sometimes|nullable|string',
        'reply'         => 'required|min:3|string',
        'sending_time'  => 'sometimes|nullable|date',
        'repeat'        => 'sometimes|nullable|string'
      ]);

      $auto_reply = AutoReply::find($id);
      $auto_reply->type = $request->type;
      $auto_reply->keyword = $request->keyword;
      $auto_reply->reply = $request->reply;
      $auto_reply->sending_time = $request->sending_time;
      $auto_reply->repeat = $request->repeat;
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
