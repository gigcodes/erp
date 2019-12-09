<?php

namespace App\Console\Commands\Manual;

use Illuminate\Console\Command;
use App\Product;
use App\Brand;

class ProductSpecialPriceEur extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:special';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get the price from price table and convert price to price_eur_special';

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
        //Getting all products and convert to special price
        $products = Product::select('id','price','price_eur_special','brand')->where('price_eur_special',0)->where('price','!=',0)->get();

        foreach($products as $product){
            $finalPrice = $product->price;
            if($product->brand == 0 || $product->brand == '' || $product->brand == null){
                continue;
            }

            $brand = Brand::find($product->brand);

            if($brand == null && $brand == ''){
                    continue;
            }

            if($brand->deduction_percentage == null || $brand->deduction_percentage == 0){
                continue;
            }

            $priceEurSpecial = $finalPrice - ($finalPrice * $brand->deduction_percentage) / 100;
            if($priceEurSpecial == 0){
                continue;
            }
            $product->price_eur_special = $priceEurSpecial;
            $product->update();
           
        }
        dump('All Prices Updated');
    }
}
