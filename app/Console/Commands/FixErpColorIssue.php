<?php

namespace App\Console\Commands;

use App\Helpers\LogHelper;
use App\Helpers\StatusHelper;
use App\Product;
use App\ScrapedProducts;
use Illuminate\Console\Command;

class FixErpColorIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-erp-color-issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Erp color issue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $products = Product::join('scraped_products as sp', 'sp.product_id', 'products.id')->where('products.status_id', StatusHelper::$unknownColor)->where('products.supplier_id', '>', 0)
                ->where(function ($q) {
                    $q->where('sp.color', '!=', '')->where('sp.color', '!=', '0');
                })->where(function ($q) {
                    $q->orWhereNull('products.color')->orWhere('products.color', '=', '');
                })->select('products.*')->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product model query was finished']);

            if (! $products->isEmpty()) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Products record found']);

                foreach ($products as $product) {
                    $this->info('Started for product id :' . $product->id);
                    $scrapedProduct = ScrapedProducts::where('product_id', $product->id)->where(function ($q) {
                        $q->orWhereNotNull('color')->orWhere('color', '!=', '');
                    })->first();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'ScrapedProducts model query was finished']);

                    if ($scrapedProduct) {
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraped products found for product id:' . $product->id]);

                        $this->info('Started for product id :' . $product->id . ' and find the scraped product');

                        $color = \App\ColorNamesReference::getColorRequest(
                            $scrapedProduct->color,
                            $scrapedProduct->url,
                            $scrapedProduct->title,
                            $scrapedProduct->description
                        );

                        $this->info('Started for product id :' . $product->id . ' and find the color =>' . $color);
                        if ($color) {
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Color found for product id:' . $product->id]);

                            // check for the auto crop
                            $product->color = $color;
                            $needToCheckStatus = [
                                StatusHelper::$requestForExternalScraper,
                                StatusHelper::$unknownComposition,
                                StatusHelper::$unknownColor,
                                StatusHelper::$unknownCategory,
                                StatusHelper::$unknownMeasurement,
                                StatusHelper::$unknownSize,
                            ];

                            if (! in_array($product->status_id, $needToCheckStatus)) {
                                $product->status_id = StatusHelper::$autoCrop;
                            }
                            $product->save();
                            $product->checkExternalScraperNeed();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Updated products detail or product id:' . $product->id]);
                        } else {
                            $product->status_id = StatusHelper::$unknownColor;
                            $product->save();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Updated products detail or product id:' . $product->id]);
                        }
                    } else {
                        $product->status_id = StatusHelper::$unknownColor;
                        $product->save();

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Updated products detail or product id:' . $product->id]);
                    }
                }
            }
        } catch(\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
