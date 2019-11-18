<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Product;

class ProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($json)
    {
        // Set product
        $this->_json = $json;
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

        // Load App\Product
        $scrapedProduct = new \App\ScrapedProducts();

        // Check for nextExcelStatus
        $nextExcelStatus = $this->_json->nextExcelStatus ?? 2;

        // Remove nextExcelStatus from json
        if (isset($this->_json->nextExcelStatus)) {
            $arrJson = json_decode($this->_json, true);
            unset($arrJson[ 'nextExcelStatus' ]);
            $this->_json = json_encode($arrJson);
        }

        // ItemsAdded
        $itemsAdded = $scrapedProduct->bulkScrapeImport($this->_json, 1, $nextExcelStatus);

        // Check for result
        if ((int)$itemsAdded > 0) {
            // Log info
            Log::channel('productUpdates')->info("[Queued job result] Successfully imported " . $itemsAdded . " products");
        } else {
            // Log alert
            Log::channel('productUpdates')->alert("[Queued job result] Failed importing products");
        }
    }
}