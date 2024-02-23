<?php

namespace App\Console\Commands;

use App\Helpers\LogHelper;
use App\LandingPageProduct;
use Illuminate\Console\Command;
use App\Library\Shopify\Client as ShopifyClient;

class CheckLandingProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:landing-page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate landing products from shopify after end time';

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

            $client = new ShopifyClient();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Connecting to ShopifyClient']);

            $landingProducts = LandingPageProduct::whereRaw('timestamp(end_date) < NOW()')->orWhere('status', 0)->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'LandingPageProduct model query was finished']);

            foreach ($landingProducts as $product) {
                $productData = [
                    'product' => [
                        'published' => false,
                        'published_scope' => false,
                    ],
                ];
                if ($product->shopify_id) {
                    $response = $client->updateProduct($product->shopify_id, $productData, $product->store_website_id);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Updating landing page product by shopify ID:' . $product->shopify_id]);
                }
            }

            $landingProducts = LandingPageProduct::whereRaw('timestamp(start_date) < NOW() AND timestamp(end_date) > NOW()')->get();
            foreach ($landingProducts as $landingPage) {
                // Set data for Shopify
                $landingPageProduct = $landingPage->product;

                $productData = $landingPage->getShopifyPushData();
                if ($productData == false) {
                    continue;
                }

                if ($landingPage->shopify_id) {
                    $response = $client->updateProduct($landingPage->shopify_id, $productData, $landingPage->store_website_id);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Updating landing page product by shopify ID:' . $landingPage->shopify_id]);
                } else {
                    $response = $client->addProduct($productData, $landingPage->store_website_id);
                }
            }
        } catch(\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
