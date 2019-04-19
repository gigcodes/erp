<?php

namespace App\Console\Commands;

use App\Order;
use App\Customer;
use App\ChatMessage;
use App\CommunicationHistory;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoMessager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:auto-messager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $params = [
        'number'      => NULL,
        'user_id'     => 6,
        'approved'    => 0,
        'status'      => 1,
      ];

      $communication_histories = CommunicationHistory::where('type', 'refund-initiated')->where('model_type', 'App\Order')->where('method', 'email')->get();
      $now = Carbon::now();

      foreach ($communication_histories as $history) {
        $time_diff = Carbon::parse($history->created_at)->diffInHours($now);

        if ($time_diff == 12) {
          $order = Order::find($history->model_id);
          $params['customer_id'] = $order->customer_id;
          $params['message'] = 'This is Alternative';

          $chat_message = ChatMessage::create($params);

          // try {
          // app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($order->customer->phone, $order->customer->whatsapp_number, $params['message'], false, $chat_message->id);
          // } catch {
          //   // ok
          // }

          // $chat_message->update([
          //   'approved'  => 1
          // ]);
        }
      }

      // Follow Up Sequence
      $follow_ups = CommunicationHistory::where('type', 'initiate-followup')->where('model_type', 'App\Customer')->where('method', 'whatsapp')->get();
      $now = Carbon::now();

      foreach ($follow_ups as $follow_up) {
        $time_diff = Carbon::parse($follow_up->created_at)->diffInHours($now);

        if ($time_diff == 24) {
          $customer = Customer::find($follow_up->model_id);
          $params['customer_id'] = $customer->id;
          $params['message'] = 'This is follow up after 24 hours';

          $chat_message = ChatMessage::create($params);

          // try {
          // app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($order->customer->phone, $order->customer->whatsapp_number, $params['message'], false, $chat_message->id);
          // } catch {
          //   // ok
          // }

          // $chat_message->update([
          //   'approved'  => 1
          // ]);
        }

        if ($time_diff == 48) {
          $customer = Customer::find($follow_up->model_id);
          $params['customer_id'] = $customer->id;
          $params['message'] = 'This is follow up after 48 hours';

          $chat_message = ChatMessage::create($params);

          // try {
          // app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($order->customer->phone, $order->customer->whatsapp_number, $params['message'], false, $chat_message->id);
          // } catch {
          //   // ok
          // }

          // $chat_message->update([
          //   'approved'  => 1
          // ]);
        }

        if ($time_diff == 72) {
          $customer = Customer::find($follow_up->model_id);
          $params['customer_id'] = $customer->id;
          $params['message'] = 'This is follow up after 72 hours';

          $chat_message = ChatMessage::create($params);

          // try {
          // app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($order->customer->phone, $order->customer->whatsapp_number, $params['message'], false, $chat_message->id);
          // } catch {
          //   // ok
          // }

          // $chat_message->update([
          //   'approved'  => 1
          // ]);
        }
      }

      // Refunds Workflow

      $refunded_orders = Order::where('refund_answer', 'no')->get();
      $now = Carbon::now();

      foreach ($refunded_orders as $order) {
        $time_diff = Carbon::parse($order->refund_answer_date)->diffInHours($now);
        dump($time_diff);
        if ($time_diff == 48) {
          $params['customer_id'] = $order->customer_id;
          $params['message'] = 'After 48 hours - Refund is in process';

          $chat_message = ChatMessage::create($params);

          // try {
          // app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($order->customer->phone, $order->customer->whatsapp_number, $params['message'], false, $chat_message->id);
          // } catch {
          //   // ok
          // }

          // $chat_message->update([
          //   'approved'  => 1
          // ]);

          // CommunicationHistory::create([
    			// 	'model_id'		=> $order->id,
    			// 	'model_type'	=> Order::class,
    			// 	'type'				=> 'refund-inprocess',
    			// 	'method'			=> 'whatsapp'
    			// ]);
        }

        if ($time_diff == 72) {
          $params['customer_id'] = $order->customer_id;
          $params['message'] = 'After 72 hours - Some Products';

          $chat_message = ChatMessage::create($params);

          // try {
          // app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($order->customer->phone, $order->customer->whatsapp_number, $params['message'], false, $chat_message->id);
          // } catch {
          //   // ok
          // }

          // $chat_message->update([
          //   'approved'  => 1
          // ]);

          // CommunicationHistory::create([
    			// 	'model_id'		=> $order->id,
    			// 	'model_type'	=> Order::class,
    			// 	'type'				=> 'products-suggestion',
    			// 	'method'			=> 'whatsapp'
    			// ]);

          sleep(5);

          $params['message'] = 'This is transfer enter amount [AMOUNT], now ok, [ADDRESS]. Finish!';
          $chat_message = ChatMessage::create($params);
        }
      }
    }
}
