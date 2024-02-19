<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Helpers\ProductHelper;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use seo2websites\MagentoHelper\MagentoHelper;

class CallHelperForZeroStockQtyUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private $products)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('CallHelperForZeroStockQtyUpdate JOB');
        try {
            \Log::info('CallHelperForZeroStockQtyUpdate TRY');
            $zeroStock = [];
            if (! empty($this->products)) {
                foreach ($this->products as $item) {
                    \Log::info('Item :' . json_encode($item));
                    $websiteArrays = ProductHelper::getStoreWebsiteNameFromPushed($item['id']);
                    \Log::info('websiteArrays:' . json_encode($websiteArrays));
                    if (count($websiteArrays) > 0) {
                        foreach ($websiteArrays as $websiteArray) {
                            $zeroStock[$websiteArray]['stock'][] = ['sku' => $item['sku'], 'qty' => 0];
                            \App\StoreWebsiteProduct::where('product_id', $item['id'])
                                ->where('store_website_id', $websiteArray)->delete();
                        }
                    }
                }
            }
            \Log::info('zeroStock:' . json_encode($zeroStock));
            if (! empty($zeroStock)) {
                \Log::info('Inside block zeroStock:' . json_encode($zeroStock));
                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                    MagentoHelper::callHelperForZeroStockQtyUpdate($zeroStock);
                    \Log::info('inventory:update Jobs Run');
                }
            }
        } catch (\Exception $e) {
            \Log::info('CallHelperForZeroStockQtyUpdate END');
            \Log::info('Issue fom MagentoHelperForZeroStockQtyUpdate ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        if (! empty($this->products)) {
            return ['MagentoHelperForZeroStockQtyUpdate', $this->products[0]['id']];
        } else {
            return ['MagentoHelperForZeroStockQtyUpdate', 'No product found'];
        }
    }
}
