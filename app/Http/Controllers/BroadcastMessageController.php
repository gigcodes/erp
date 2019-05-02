<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MessageQueue;
use App\Setting;
use App\Customer;
use App\BroadcastImage;
use App\ApiKey;
use Carbon\Carbon;
use File;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class BroadcastMessageController extends Controller
{
  public function index(Request $request)
  {
    $date = $request->sending_time ?? Carbon::now()->format('Y-m-d');
    $selected_customer = $request->customer ?? '';
    $month_back = Carbon::parse($date)->subMonth();

    $message_queues = MessageQueue::latest()->where('sending_time', 'LIKE', "%$date%");
    $last_set_completed = MessageQueue::where('sending_time', '>', "$month_back 00:00:00")->where('sent', 1);

    if ($request->customer != '') {
      $message_queues = $message_queues->where('customer_id', $selected_customer);
      $last_set_completed = $last_set_completed->where('customer_id', $selected_customer);
    }

    $last_set_completed_count = $last_set_completed->count();
    $last_set_stopped_count = MessageQueue::where('sending_time', '>', "$month_back 00:00:00")->where('status', 1)->count();
    $last_set_received_count = MessageQueue::with('chat_message')->where('sending_time', '>', "$month_back 00:00:00")->whereHas('chat_message', function ($query) {
      $query->where('sent', 1);
    })->count();

    $message_groups = MessageQueue::where('sending_time', '>', "$month_back 00:00:00")->get()->groupBy(['group_id', 'sent', 'status']);

    $message_groups_array = [];

    foreach ($message_groups as $group_id => $datas) {
      $sent_count = 0;
      $received_count = 0;
      $stopped_count = 0;
      $total_count = 0;
      foreach ($datas as $sent_status => $data) {

        foreach ($data as $stopped_status => $items) {
          if ($sent_status == 1) {
            $sent_count += count($items);

            foreach ($items as $item) {
              $received_count += ($item->chat_message && $item->chat_message->sent = 1) ? 1 : 0;
            }
          }

          $total_count += count($items);

          if ($stopped_status == 0) {
            $can_be_stopped = true;
          } else {
            $can_be_stopped = false;
            $stopped_count += count($items);
          }

          $message_groups_array[$group_id]['message'] = json_decode($items[0]->data, true)['message'];
          $message_groups_array[$group_id]['can_be_stopped'] = $can_be_stopped;
        }

        $message_groups_array[$group_id]['sent'] = $sent_count;
        $message_groups_array[$group_id]['received'] = $received_count;
        $message_groups_array[$group_id]['stopped'] = $stopped_count;
        $message_groups_array[$group_id]['total'] = $total_count;
      }
    }
    // dd($message_groups_array);

    $message_queues = $message_queues->paginate(Setting::get('pagination'));
    $last_set_completed = $last_set_completed->paginate(Setting::get('pagination'), ['*'], 'completed-page');
    $customers_all = Customer::select(['id', 'name', 'phone', 'email'])->get();

    return view('customers.broadcast', [
      'message_queues'            => $message_queues,
      'message_groups'            => $message_groups_array,
      'date'                      => $date,
      'last_set_completed'        => $last_set_completed,
      'last_set_completed_count'  => $last_set_completed_count,
      'last_set_stopped_count'    => $last_set_stopped_count,
      'last_set_received_count'   => $last_set_received_count,
      'customers_all'             => $customers_all,
      'selected_customer'         => $selected_customer
    ]);
  }

  public function doNotDisturb(Request $request, $id)
  {
    $customer = Customer::find($id);
    $customer->do_not_disturb = 1;
    $customer->save();

    MessageQueue::where('sent', 0)->where('customer_id', $id)->delete();

    // foreach ($message_queues as $message_queue) {
    //   $message_queue->status = 1; // Message STOPPED
    //   $message_queue->save();
    // }

    return redirect()->route('broadcast.index')->with('success', 'You have successfully changed status!');
  }

  public function images()
  {
    $broadcast_images = BroadcastImage::paginate(Setting::get('pagination'));
    $api_keys = ApiKey::select('number')->get();

    return view('customers.broadcast-images', [
      'broadcast_images'  => $broadcast_images,
      'api_keys'  => $api_keys
    ]);
  }

  public function imagesUpload(Request $request)
  {
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        $broadcast_image = BroadcastImage::create();

        $media = MediaUploader::fromSource($image)->toDirectory('broadcast-images')->upload();
        $broadcast_image->attachMedia($media,config('constants.media_tags'));
      }
    }

    return redirect()->route('broadcast.images')->withSuccess('You have successfully uploaded images!');
  }

  public function imagesLink(Request $request)
  {
    $image = BroadcastImage::find($request->moduleid);
    $image->products = $request->products;
    $image->save();

    return redirect()->route('broadcast.images')->withSuccess('You have successfully linked products!');
  }

  public function imagesDelete($id)
  {
    $image = BroadcastImage::find($id);

    $path = $image->hasMedia(config('constants.media_tags')) ? $image->getMedia(config('constants.media_tags'))->first()->getAbsolutePath() : '';

    File::delete($path);

    $image->delete();

    return redirect()->route('broadcast.images')->withSuccess('You have successfully deleted images!');
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

  public function DeleteGroup(Request $request, $id)
  {
    MessageQueue::where('group_id', $id)->delete();

    return redirect()->route('broadcast.index')->withSuccess('You have successfully deleted group!');
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
