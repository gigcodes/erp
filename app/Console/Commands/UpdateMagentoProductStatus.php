<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;


class UpdateMagentoProductStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:magento-product-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $scraper;

    /**
     * Create a new command instance.
     *
     * @param GebnegozionlineProductDetailsScraper $scraper
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
      $options   = array(
        'trace'              => true,
        'connection_timeout' => 120,
        'wsdl_cache'         => WSDL_CACHE_NONE,
      );

      $proxy     = new \SoapClient(config('magentoapi.url'), $options);
      $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

      // $magento_products = $proxy->catalogProductList($sessionId);

      $products = Product::select(['sku', 'isUploaded', 'isFinal'])->get();

      foreach ($products as $key => $product) {
        $error_message = '';

        try {
          $magento_product = json_decode(json_encode($proxy->catalogProductInfo($sessionId, $product->sku)), true);
        } catch (\Exception $e) {
          $error_message = $e->getMessage();
        }

        if ($error_message == 'Product not exists.') {
          $product->isUploaded = 0;
          $product->isFinal = 0;

          dump("$key Does not Exist");
        } else {
          $product->isUploaded = 1;

          $visibility = $magento_product['visibility'];
          // 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search

          if ($visibility == 1) {
            $product->isFinal = 0;

            dump("$key Not Visible");
          } elseif ($visibility == 2 || $visibility == 3 || $visibility == 4) {
            $product->isFinal = 1;

            dump("$key Visible");
          }
        }

        $product->save();
      }
    }
}
