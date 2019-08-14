<?php

namespace App\Console\Commands;

use App\MagentoSoapHelper;
use Illuminate\Console\Command;
use App\Jobs\PushToMagento;

class UploadProductsToMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento:upload-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // Connect
        $magentoSoapHelper = new MagentoSoapHelper();

        // Get product
        $products = \App\Product::where( 'isListed', -5 )->get();

        // Loop over products
        if ( $products !== NULL ) {
            foreach ( $products as $product ) {
                // Dispatch
                PushToMagento::dispatch($product);

                // Update
                $product->isListed = 1;
                $product->save();
            }
        }
    }
}
