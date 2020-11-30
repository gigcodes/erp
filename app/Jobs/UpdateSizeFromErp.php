<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateSizeFromErp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $params;
    public $from;
    public $to;
    public $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->from    = $params["from"];
        $this->to      = $params["to"];
        $this->user_id = isset($params["user_id"]) ? $params["user_id"] : 6;
        $this->params  = $params;
    }

    public static function putLog($message)
    {
        \Log::channel('update_color_job')->info($message);
        return true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        self::putLog("Job update product sizes from erp start time : " . date("Y-m-d H:i:s"));

        $affectedProducts = ScrapedProducts::matchedComposition($this->from);

        //$sku = [];
        if (!empty($affectedProducts)) {
            foreach ($affectedProducts as $affectedProduct) {
                $to = str_replace($this->from, $this->to, $affectedProduct->sizes );
                $affectedProduct->sizes = $to;
                $affectedProduct->save();
                //$sku[] = $affectedProduct->sku;
            }
        }

        //\Log::info(print_r($sku,true));

        self::putLog("Job update product sizes from erp end time : " . date("Y-m-d H:i:s"));

        return true;
    }
}
