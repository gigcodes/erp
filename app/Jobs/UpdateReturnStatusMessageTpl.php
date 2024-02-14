<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateReturnStatusMessageTpl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $returnId;

    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($returnId, $message = null)
    {
        $this->returnId = $returnId;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $return = \App\ReturnExchange::where('id', $this->returnId)->first();
            if ($return) {
                $statusModal = \App\ReturnExchangeStatus::where('id', $return->status)->first();
                if (! $this->message || $this->message == '') {
                    $defaultMessageTpl = \App\ReturnExchangeStatus::STATUS_TEMPLATE;
                    if ($statusModal && ! empty($statusModal->message)) {
                        $defaultMessageTpl = $statusModal->message;
                    }
                    $msg = str_replace(['#{id}', '#{status}'], [$return->id, $statusModal->status_name], $defaultMessageTpl);
                } else {
                    $msg = $this->message;
                }
                // start update the order status
                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add([
                    'customer_id' => $return->customer_id,
                    'message' => $msg,
                    'status' => 0,
                ]);

                app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'customer');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['customer_message', $this->returnId];
    }
}
