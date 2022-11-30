<?php

namespace App\Jobs;

use App\Helpers\ProductHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use seo2websites\MagentoHelper\MagentoHelper;

class CallHelperForZeroStockQtyUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $products;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $zeroStock = [];
            if (! empty($this->products)) {
                foreach ($this->products as $item) {
                    $websiteArrays = ProductHelper::getStoreWebsiteNameFromPushed($item['id']);
                    if (count($websiteArrays) > 0) {
                        foreach ($websiteArrays as $websiteArray) {
                            $zeroStock[$websiteArray]['stock'][] = ['sku' => $item['sku'], 'qty' => 0];
                            \App\StoreWebsiteProduct::where('product_id', $item['id'])
                ->where('store_website_id', $websiteArray['id'])->delete();
                        }
                    }
                }
            }

            if (! empty($zeroStock)) {
                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                    MagentoHelper::callHelperForZeroStockQtyUpdate($zeroStock);
                    \Log::info('inventory:update Jobs Run');
                }
            }
        } catch (\Exception $e) {
            \Log::info('Issue fom MagentoHelperForZeroStockQtyUpdate '.$e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['MagentoHelperForZeroStockQtyUpdate', $this->products[0]->id];
    }
}
