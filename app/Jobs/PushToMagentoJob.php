<?php

namespace App\Jobs;

use App\Library\Magento\MagentoService;
use App\Product;
use App\StoreWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushToMagentoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;
    protected $_website;
    protected $log;
    protected $category;

    /**
     * Create a new job instance.
     *
     * @param  Product  $product
     * @param  StoreWebsite  $website
     * @param  null $category
     * @param  null  $log
     */
    public function __construct(Product $product, StoreWebsite $website, $log = null, $category = null)
    {
        $this->_product = $product;
        $this->_website = $website;
        $this->log = $log;
        $this->category = $category;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        $magentoService = new MagentoService($this->_product, $this->_website, $this->log, $this->category);
        $magentoService->pushProduct();
    }
}
