<?php

namespace App\Jobs;

use App\Library\Magento\MagentoService;
use App\Product;
use App\StoreWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MagentoServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $_website;

    protected $log;

    /**
     * Create a new job instance.
     *
     * @param  Product  $product
     * @param  StoreWebsite  $website
     * @param  null  $log
     */
    public function __construct(Product $product, StoreWebsite $website, $log = null)
    {
        // Set product and website
        $this->_product = $product;
        $this->_website = $website;
        $this->log = $log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Set time limit
        set_time_limit(0);
        $magentoService = new MagentoService($this->_product, $this->_website, $this->log);
        $magentoService->pushProduct();
    }
}
