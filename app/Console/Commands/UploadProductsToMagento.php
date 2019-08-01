<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

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
        for ($i=0;$i<=1000;$i++) {
            $product = Product::where('is_approved', 1)->where('isListed', 0)->inRandomOrder()->first();
            $result = app('App\Http\Controllers\ProductListerController')->magentoSoapApiUpload($product);
            dump('Uploading....');
            dump($result);
        }
    }
}
