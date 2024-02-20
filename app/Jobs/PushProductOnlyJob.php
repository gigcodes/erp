<?php

namespace App\Jobs;

use App\Product;
use App\StoreWebsite;
use App\ProductPushErrorLog;
use Illuminate\Bus\Queueable;
use App\Helpers\ProductHelper;
use App\Loggers\LogListMagento;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushProductOnlyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $product_index;

    protected $no_of_product;

    /**
     * Create a new job instance.
     *
     * @param  StoreWebsite  $website
     * @param  null  $log
     * @param  null  $mode
     */
    public function __construct(Product $product, protected $details)
    {
        // Set product and website
        $this->_product = $product;
        $this->product_index = (isset($details) && isset($details['product_index'])) ? $details['product_index'] : 0;
        $this->no_of_product = (isset($details) && isset($details['no_of_product'])) ? $details['no_of_product'] : 0;
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

        $product = $this->_product;

        $websiteArrays = ProductHelper::getStoreWebsiteNameByTag($product->id);
        if (! empty($websiteArrays)) {
            $i = 1;
            foreach ($websiteArrays as $websiteArray) {
                $website = $websiteArray;
                if ($website) {
                    \Log::info('Product started website found For website' . $website->website);
                    $log = LogListMagento::log($product->id, 'Start push to magento for product id ' . $product->id . ' status id ' . $product->status_id, 'info', $website->id, 'initialization');
                    //currently we have 3 queues assigned for this task.
                    $log->queue = \App\Helpers::createQueueName($website->title);
                    $log->save();
                    ProductPushErrorLog::log('', $product->id, 'Started pushing ' . $product->name, 'success', $website->id, null, null, $log->id, null);
                    try {
                        PushToMagento::dispatch($product, $website, $log, null, $this->details)->onQueue($log->queue);
                    } catch (\Exception $e) {
                        $error_msg = 'First Job failed: ' . $e->getMessage();
                        $log->sync_status = 'error';
                        $log->message = $error_msg;
                        $log->save();
                        ProductPushErrorLog::log('', $product->id, $error_msg, 'error', $website->id, null, null, $log->id, null);
                    }
                    $i++;
                } else {
                    ProductPushErrorLog::log('', $product->id, 'Started pushing ' . $product->name . ' website for product not found', 'error', null, null, null, null, null);
                }
            }
        } else {
            ProductPushErrorLog::log('', $product->id, 'No website found for product' . $product->name, 'error', null, null, null, null, null);
        }
    }

    public function failed(\Throwable $exception = null)
    {
        $product = $this->_product;
        ProductPushErrorLog::log('', $product->id, 'PushProductOnlyJob Failed Product' . $product->name, 'error', null, null, null, null, null);
    }

    public function tags()
    {
        return ['product_' . $this->_product->id, '#' . $this->product_index, $this->no_of_product];
    }
}
