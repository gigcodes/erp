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
    $date = $request->sending_time ?? Carbon::now()->format('Y-m-d');
    $month_back = Carbon::parse($date)->subMonth();

    $message_queues = MessageQueue::latest()->where('sending_time', 'LIKE', "%$date%")->paginate(Setting::get('pagination'));
    // $last_group_id = MessageQueue::max('group_id');
    $last_set_completed = MessageQueue::where('sending_time', '>', "$month_back 00:00:00")->where('sent', 1);
    // $last_set_stopped_count = MessageQueue::where('group_id', $last_group_id)->where('status', 1)->count();

    $last_set_completed_count = $last_set_completed->count();
    $last_set_received_count = MessageQueue::with('chat_message')->where('sending_time', '>', "$month_back 00:00:00")->whereHas('chat_message', function ($query) {
      $query->where('sent', 1);
    })->count();

    $message_groups = MessageQueue::where('sending_time', '>', "$month_back 00:00:00")->get()->groupBy(['group_id', 'sent', 'status']);

    $message_groups_array = [];

    foreach ($message_groups as $group_id => $datas) {
      $sent_count = 0;
      $total_count = 0;
      foreach ($datas as $sent_status => $data) {

        foreach ($data as $stopped_status => $items) {
          if ($sent_status == 1) {
            $sent_count += count($items);
          }

          $total_count += count($items);

          if ($stopped_status == 0) {
            $can_be_stopped = true;
          } else {
            $can_be_stopped = false;
          }

          $message_groups_array[$group_id]['message'] = json_decode($items[0]->data, true)['message'];
          $message_groups_array[$group_id]['can_be_stopped'] = $can_be_stopped;
        }

        $message_groups_array[$group_id]['sent'] = $sent_count;
        $message_groups_array[$group_id]['total'] = $total_count;
      }
    }
    // dd($message_groups_array);




    // if ($last_set_stopped_count > 0) {
    //   $last_stopped = true;
    // } else {
    //   $last_stopped = false;
    // }

    $last_set_completed = $last_set_completed->paginate(Setting::get('pagination'), ['*'], 'completed-page');

    return view('customers.broadcast', [
      'message_queues'            => $message_queues,
      'message_groups'            => $message_groups_array,
      'date'                      => $date,
      'last_set_completed'        => $last_set_completed,
      // 'last_stopped'              => $last_stopped,
      'last_set_completed_count'  => $last_set_completed_count,
      'last_set_received_count'   => $last_set_received_count
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

  public function restart(Request $request)
  {
    $last_group_id = MessageQueue::max('group_id');

    $last_set_stopped = MessageQueue::where('group_id', $last_group_id)->where('status', 1)->where('sent', 0)->get();

    foreach ($last_set_stopped as $set) {
      $set->status = 0;
      $set->save();
    }

    return redirect()->route('broadcast.index')->withSuccess('You have successfully restarted last set!');
  }

  public function restartGroup(Request $request, $id)
  {
    $group = MessageQueue::where('group_id', $id)->where('status', 1)->where('sent', 0)->get();

    foreach ($group as $set) {
      $set->status = 0;
      $set->save();
    }

    return redirect()->route('broadcast.index')->withSuccess('You have successfully restarted group!');
  }

  public function stopGroup(Request $request, $id) {
    $message_queues = MessageQueue::where('group_id', $id)->where('sent', 0)->where('status', 0)->get();

    foreach ($message_queues as $message_queue) {
      $message_queue->status = 1;
      $message_queue->save();
    }

    return redirect()->route('broadcast.index')->with('success', 'Broadcast group has been stopped!');
  }
}
