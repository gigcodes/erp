<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use seo2websites\MagentoHelper\MagentoHelper;

class CallHelperForZeroStockQtyUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $zeroStock;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $zeroStock )
    {
        $this->zeroStock = $zeroStock;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
            
            MagentoHelper::callHelperForZeroStockQtyUpdate($this->zeroStock);

            \Log::info('inventory:update Jobs Run');
        }
    }
}
