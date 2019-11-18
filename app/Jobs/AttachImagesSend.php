<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Product;

class AttachImagesSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_token;
    protected $send_pdf;
    protected $images;
    protected $image;
    protected $screenshot_path;
    protected $message;
    protected $customer_id;
    protected $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        // Set product
        $this->_token = $data['_token'];
        $this->send_pdf = $data['send_pdf'];
        $this->images = $data['images'];
        $this->image = $data['image'];
        $this->screenshot_path = $data['screenshot_path'];
        $this->message = $data['message'];
        $this->customer_id = $data['customer_id'];
        $this->status = $data['status'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        // Set time limit
        set_time_limit(0);
        $request->request->add(['_token' => $this->_token , 'send_pdf' => $this->send_pdf , 'images' => $this->images , 'image' => $this->image , 'screenshot_path' => $this->screenshot_path , 'message' => $this->message , 'customer_id' => $this->customer_id , 'status' => $this->status ]);
        
        app('App\Helpers\InstantMessagingHelper')->sendWhatsAppMessage($request, 'customer');
        
    }




}