<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MessageQueue;
use App\Setting;
use App\Customer;
use App\BroadcastImage;
use App\ApiKey;
use App\CronJob;
use Carbon\Carbon;
use File;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\ImQueue;
use App\Marketing\WhatsappConfig;
use DB;

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

        if ($request->sending_time != '') {
            $message_groups = ImQueue::where('created_at', '>', "$month_back 00:00:00")->whereNotNull('broadcast_id')->get()->groupBy('broadcast_id');
        } else {
            $message_groups = ImQueue::where('created_at', '>', "$month_back 00:00:00")->whereNotNull('broadcast_id')->get()->groupBy('broadcast_id');
            
        }

        // dd($message_groups);

        $message_groups_array = [];

        // dd();

        $new_data = [];

        
        if ($request->sending_time) {
            $new_data[ $request->sending_time ] = [];
        } else {
            while ($month_back->lte(Carbon::parse($date))) {
                $new_data[ $month_back->copy()->format('Y-m-d') ] = [];
                $month_back->addDay();
            }
        }

        // dd($new_data);

        foreach ($message_groups as $group_id => $datas) {
            $sent_count = 0;
            $received_count = 0;
            $stopped_count = 0;
            $total_count = 0;
            foreach ($datas as $data) {
            if($data->sent_at != null){
                    $received_count++;
                    $sent_count++;
                }

                $can_be_stopped = true;

                if($data->send_after == null){
                    $stopped_count++;
                    $can_be_stopped = false;
                }

                if($data->sent_at == null){
                    $sent_count++;
                }

                $message_groups_array[ $group_id ][ 'message' ] = $data->text;
                $message_groups_array[ $group_id ][ 'image' ] = $data->image;
                $message_groups_array[ $group_id ][ 'can_be_stopped' ] = $can_be_stopped;
                $message_groups_array[ $group_id ][ 'sending_time' ] = $data->send_after;
                $message_groups_array[ $group_id ][ 'whatsapp_number' ] = $data->number_from;
                $total_count++;
                }

                $message_groups_array[ $group_id ][ 'sent' ] = $sent_count;
                $message_groups_array[ $group_id ][ 'received' ] = $received_count;
                $message_groups_array[ $group_id ][ 'stopped' ] = $stopped_count;
                $message_groups_array[ $group_id ][ 'total' ] = $total_count;
                $message_groups_array[ $group_id ][ 'expecting_time' ] = '';
                $message_groups['datas'] = $message_groups_array;

                $new_data[ Carbon::parse($message_groups_array[ $group_id ][ 'sending_time' ])->format('Y-m-d') ][ $group_id ] = $message_groups_array[ $group_id ];
            }

            // dd($message_groups_array[$group_id]);
            
            
        // Get all numbers from config
        $configWhatsApp = WhatsappConfig::select('id','number')->where('status',1)->get();
        

        // Loop over numbers
        $arrCustomerNumbers = [];
        foreach ( $configWhatsApp as $whatsAppNumber => $whatsAppConfig ) {
            if ( $whatsAppConfig['customer_number'] ) {
                $arrCustomerNumbers[] = $whatsAppNumber;
            }
        }

        $message_queues = $message_queues->orderBy('sending_time', 'ASC')->paginate(Setting::get('pagination'));
        $last_set_completed = $last_set_completed->orderBy('sending_time', 'ASC')->paginate(Setting::get('pagination'), ['*'], 'completed-page');
        $customers_all = Customer::select(['id', 'name', 'phone', 'email'])->get();
        $broadcast_images = BroadcastImage::orderBy('id', 'DESC')->paginate(Setting::get('pagination'));
        $cron_job = CronJob::where('signature', 'run:message-queues')->first();
        $pending_messages_count = MessageQueue::where('sent', 0)->where('status', '!=', 1)->where('sending_time', '<', Carbon::now())->count();

        $shoe_size_group = Customer::selectRaw('shoe_size, count(id) as counts')
                                ->whereNotNull('shoe_size')
                                ->groupBy('shoe_size')
                                ->pluck('counts', 'shoe_size');

        $clothing_size_group = Customer::selectRaw('clothing_size, count(id) as counts')
                                        ->whereNotNull('clothing_size')
                                        ->groupBy('clothing_size')
                                        ->pluck('counts', 'clothing_size');

        
        return view('customers.broadcast', [
            'message_queues' => $message_queues,
            'message_groups' => $new_data,
            'date' => $date,
            'last_set_completed' => $last_set_completed,
            'last_set_completed_count' => $last_set_completed_count,
            'last_set_stopped_count' => $last_set_stopped_count,
            'last_set_received_count' => $last_set_received_count,
            'customers_all' => $customers_all,
            'selected_customer' => $selected_customer,
            'api_keys' => $configWhatsApp,
            'broadcast_images' => $broadcast_images,
            'cron_job' => $cron_job,
            'pending_messages_count' => $pending_messages_count,
            'shoe_size_group' => $shoe_size_group,
            'clothing_size_group' => $clothing_size_group,
        ]);
    }

    public function doNotDisturb(Request $request, $id)
    {
        $customer = Customer::find($id);
        $customer->do_not_disturb = 1;
         \Log::channel('customerDnd')->debug("(Customer ID " . $customer->id . " line " . $customer->name. " " . $customer->number . ": Added To DND");
       
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
        $broadcast_images = BroadcastImage::orderBy('id', 'DESC')->paginate(Setting::get('pagination'));
        $api_keys = ApiKey::select('number')->get();

        return view('customers.broadcast-images', [
            'broadcast_images' => $broadcast_images,
            'api_keys' => $api_keys
        ]);
    }

    public function imagesUpload(Request $request)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $broadcast_image = BroadcastImage::create([
                    'sending_time' => $request->sending_time
                ]);

                $media = MediaUploader::fromSource($image)->toDirectory('broadcast-images')->upload();
                $broadcast_image->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('broadcast.index')->withSuccess('You have successfully uploaded images!');
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
            $filtered_messages[ $group_id ] = $message_queue[ 0 ];
        }

        return view('customers.broadcast-calendar', [
            'message_queues' => $filtered_messages
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
        $groups = ImQueue::where('broadcast_id',$id)->get();

        foreach ($groups as $group) {
            # code...
        
            $whatappConfig = WhatsappConfig::find($request->whatsapp_number);
            
            $maxTime = ImQueue::select(DB::raw('IF(MAX(send_after)>MAX(sent_at), MAX(send_after), MAX(sent_at)) AS maxTime'))->where('number_from', $whatappConfig->number)->first();

            
            // Convert maxTime to unixtime
            $maxTime = strtotime($maxTime->maxTime);

            // Add interval
            $maxTime = $maxTime + (3600 / $whatappConfig->frequency);
            
            // Check if it's in the future
            if ($maxTime < time()) {
                $maxTime = time();
            }

            
            // Check for decent times
            if (date('H', $maxTime) < $whatappConfig->send_start) {
                $sendAfter = date('Y-m-d 0' . $whatappConfig->send_start . ':00:00', $maxTime);
            } elseif (date('H', $maxTime) > $whatappConfig->send_end) {
                $sendAfter = date('Y-m-d 0' . $whatappConfig->send_start . ':00:00', $maxTime + 86400);
            } else {
                $sendAfter = date('Y-m-d H:i:s', $maxTime);
            }

            $group->send_after = $sendAfter;
            
            $group->update();

        }    
       
        return redirect()->route('broadcast.index')->withSuccess('You have successfully restarted group!');
    }

    public function DeleteGroup(Request $request, $id)
    {
        ImQueue::where('broadcast_id', $id)->delete();

        return redirect()->route('broadcast.index')->withSuccess('You have successfully deleted group!');
    }

    public function stopGroup(Request $request, $id)
    {

        $messageQueues = ImQueue::where('broadcast_id',$id)->whereNull('sent_at')->get();
        foreach ($messageQueues as $messageQueue) {
           $messageQueue->send_after = null;
           $messageQueue->update();
        }

        return redirect()->route('broadcast.index')->with('success', 'Broadcast group has been stopped!');
    }
}
