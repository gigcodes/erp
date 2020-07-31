<?php

namespace App\Console\Commands;

use App\Http\Controllers\LandingPageController;
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
        $client = new ShopifyClient();
        $landingProducts = LandingPageProduct::whereRaw('timestamp(end_date) < NOW()')->get();
        foreach ($landingProducts as $product) {
            $productData = [
                'product' => [
                    'published' => false,
                ],
            ];
            if ($product->shopify_id) {
                $response = $client->updateProduct($product->shopify_id, $productData);
            }
        }

    }
}
