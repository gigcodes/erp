<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MessageQueue;
use App\Setting;
use App\Customer;
use Carbon\Carbon;

class BroadcastMessageController extends Controller
{
  public function index(Request $request)
  {
    $date = $request->sending_time ? $request->sending_time : Carbon::now()->format('Y-m-d');
    $message_queues = MessageQueue::latest()->where('sending_time', 'LIKE', "%$date%")->paginate(Setting::get('pagination'));

    return view('customers.broadcast', [
      'message_queues'  => $message_queues,
      'date'            => $date
    ]);
  }

  public function doNotDisturb(Request $request, $id)
  {
    $customer = Customer::find($id);
    $customer->do_not_disturb = 1;
    $customer->save();

    $message_queues = MessageQueue::where('sent', 0)->where('customer_id', $id)->get();

    foreach ($message_queues as $message_queue) {
      $message_queue->status = 1; // Message STOPPED
      $message_queue->save();
    }

    return redirect()->route('broadcast.index')->with('success', 'You have successfully changed status!');
  }

  public function calendar()
  {
    $message_queues = MessageQueue::latest()->get()->groupBy('group_id');
    $filtered_messages = [];

    foreach ($message_queues as $group_id => $message_queue) {
      $filtered_messages[$group_id] = $message_queue[0];
    }

    return view('customers.broadcast-calendar', [
      'message_queues'  => $filtered_messages
    ]);
  }
}
