<?php

namespace App\Jobs;

use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Product;
use App\ProductPushErrorLog;
use App\ProductPushJourney;
use App\PushToMagentoCondition;
use App\StoreWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Loggers\LogListMagento;
class ImageApprovalPushProductOnlyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;
    
    
    /**
     * Create a new job instance.
     *
     * @param  Product  $product
     * @param  StoreWebsite  $website
     * @param  null  $log
     * @param  null  $mode
     */
    public function __construct(Product $product)
    {
        // Set product and website
        $this->_product = $product;
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
                $website =  $websiteArray;
                if ($website) {
                    \Log::info('Product started website found For website'.$website->website);
                    $log = LogListMagento::log($product->id, 'Start push to magento for product id '.$product->id.' status id '.$product->status_id, 'info', $website->id, 'initialization');
                    //currently we have 3 queues assigned for this task.
                    $log->queue = \App\Helpers::createQueueName($website->title);
                    $log->save();
                    ProductPushErrorLog::log('', $product->id, 'Started pushing '.$product->name, 'success', $website->id, null, null, $log->id, null);
                    try {
                        ImageApprovalPushToMagento::dispatch($product, $website, $log,null)->onQueue($log->queue);
                    } catch (\Exception $e) {
                        $error_msg = 'ImageApprovalPushToMagento failed: '.$e->getMessage();
                        $log->sync_status = 'error';
                        $log->message = $error_msg;
                        $log->save();
                        ProductPushErrorLog::log('', $product->id, $error_msg, 'error', $website->id, null, null, $log->id, null);
                    }
                    $i++;
                } else {
                    ProductPushErrorLog::log('', $product->id, 'Started pushing '.$product->name.' website for product not found', 'error', null, null, null, null, null);
                }
            }
            
            $product->isUploaded = 1;
            $product->save();

        } else {
            ProductPushErrorLog::log('', $product->id, 'No website found for product'.$product->name, 'error', null, null, null, null, null);
        }
    }

    public function failed(\Throwable $exception = null)
    {
        $product = $this->_product; 
        ProductPushErrorLog::log('', $product->id, 'ImageApprovalPushProductOnlyJob Failed Product'.$product->name, 'error', null, null, null, null, null);
    }

    public function tags()
    {
        return ['product_'.$this->_product->id];
    }
}
