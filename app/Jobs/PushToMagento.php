<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\MagentoSoapHelper;
use App\Product;

class PushToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Product $product )
    {
        // Set product
        $this->_product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Load product
        $product = $this->_product;

        // Load Magento Soap Helper
        $magentoSoapHelper = new MagentoSoapHelper();

        // Push product to Magento
        $result = $magentoSoapHelper->pushProductToMagento( $product );

        // Check for result
        if ( !$result ) {
            // Log alert
            Log::alert( "[Queued job result] Pushing product with ID " . $product->id . " to Magento failed" );

            // Set product to isListed is 0
            $product->isListed = 0;
            $product->save();
        } else {
            // Log info
            Log::info( "[Queued job result] Successfully pushed product with ID " . $product->id . " to Magento" );
        }
    }
}
