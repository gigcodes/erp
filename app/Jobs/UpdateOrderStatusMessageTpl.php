<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrderStatusMessageTpl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    private $orderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = \App\Order::where('id', $this->orderId)->first();
        if ($order) {
            $statusModal       = \App\OrderStatus::where("id", $order->order_status_id)->first();
            $defaultMessageTpl = \App\Order::ORDER_STATUS_TEMPLATE;
            if ($statusModal && !empty($statusModal->message_text_tpl)) {
                $defaultMessageTpl = $statusModal->message_text_tpl;
            }

            // start update the order status
            $requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add([
                'customer_id' => $order->customer_id,
                'message'     => str_replace(["#{order_id}", "#{order_status}"], [$order->order_id, $order->order_status], $defaultMessageTpl),
                'status'      => 0,
                'order_id'    => $order->id,
            ]);
            app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');
        }

    }
}
