<?php

namespace App\Jobs;

use App\Product;
use App\ProductPushErrorLog;
use App\StoreWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use seo2websites\MagentoHelper\MagentoHelper;

class PushToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;
    protected $_website;
    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product, StoreWebsite $website, $log = null)
    {
        // Set product and website
        $this->_product = $product;
        $this->_website = $website;
        $this->log      = $log;
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

        $date_time = date("Y-m-d h:i:s");
        // Load product and website
        $product = $this->_product;
        $website = $this->_website;

        try {

            //$jobId = app(JobRepository::class)->id;

            if ($this->log) {
                $this->log->sync_status    = "started_push";
                $this->log->message         = "Product has been started to push";
                $this->log->queue_id       = $this->job->getJobId();
                $this->log->job_start_time = $date_time;
                $this->log->save();
            }

            if (!$website->website_source || $website->website_source == '') {
                ProductPushErrorLog::log('', $product->id, 'Website Source not found', 'error', $website->id, null, null, $this->log->id);
                $this->log->message         = "Website source not found";
                $this->log->sync_status  = "error";
                $this->log->job_end_time = $date_time;
                $this->log->save();
                return false;
            }

            if ($website->disable_push == 1) {
                ProductPushErrorLog::log('', $product->id, 'Website is disable for push product', 'error', $website->id, null, null, $this->log->id);
                $this->log->message         = "Website is disable for push product";
                $this->log->sync_status  = "error";
                $this->log->job_end_time = $date_time;
                $this->log->save();
                return false;
            }

            // started to check the validation for the category size is available or not and if not then throw the error
            $categorym = $product->categories;
            if ($categorym) {
                $categoryparent = $categorym->parent;
                if ($categoryparent && $categoryparent->size_chart_needed == 1 && empty($categoryparent->getSizeChart($website->id))) {
                    ProductPushErrorLog::log('', $product->id, 'Size chart is needed for push product', 'error', $website->id, null, null, $this->log->id);
                    $this->log->message         = "Size chart is needed for push product";
                    $this->log->sync_status  = "size_chart_needed";
                    $this->log->job_end_time = $date_time;
                    $this->log->save();
                    return false;
                }

                if ($categorym && $categorym->size_chart_needed == 1 && empty($categorym->getSizeChart($website->id))) {
                    ProductPushErrorLog::log('', $product->id, 'Size chart is needed for push product', 'error', $website->id, null, null, $this->log->id);
                    $this->log->message         = "Size chart is needed for push product";
                    $this->log->sync_status  = "size_chart_needed";
                    $this->log->job_end_time = $date_time;
                    $this->log->save();
                    return false;
                }
            }

            if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                MagentoHelper::callHelperForProductUpload($product, $website, $this->log);
                return false;
            } else {
                ProductPushErrorLog::log('', $product->id, 'Magento helper class not found', 'error', $website->id, null, null, $this->log->id);
                return false;
            }

        } catch (\Exception $e) {
            if ($this->log) {
                ProductPushErrorLog::log('', $product->id, $e->getMessage(), 'error', $website->id, null, null, $this->log->id);
                $this->log->message         = $e->getMessage();
                $this->log->sync_status  = "error";
                $this->log->queue_id     = $this->job->getJobId();
                $this->log->job_end_time = $date_time;
                $this->log->save();
            } else {
                \Log::error($e);
            }
        }

        // Load Magento Soap Helper
        // $magentoSoapHelper = new MagentoSoapHelper();

        // // Push product to Magento
        // $result = $magentoSoapHelper->pushProductToMagento( $product );

        // Check for result
        // if ( !$result ) {
        //     // Log alert
        //     Log::channel('listMagento')->alert( "[Queued job result] Pushing product with ID " . $product->id . " to Magento failed" );

        //     // Set product to isListed is 0
        //     $product->isListed = 0;
        //     $product->save();
        // } else {
        //     // Log info
        //     Log::channel('listMagento')->info( "[Queued job result] Successfully pushed product with ID " . $product->id . " to Magento" );
        // }
    }
}
