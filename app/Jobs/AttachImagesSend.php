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

    protected $_json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($json)
    {
        // Set product
        $this->_json = $json;
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
       
        app('App\Http\Controllers\WhatsAppController')->sendMessage($request, 'customer');
        
        return true;
    }
}