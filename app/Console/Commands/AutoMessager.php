<?php

namespace App\Console\Commands;

use App\Order;
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
        }
      }
    }
}
