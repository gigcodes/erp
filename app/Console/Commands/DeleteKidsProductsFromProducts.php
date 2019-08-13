<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteKidsProductsFromProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:kids-products';

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
        Product::where('name', 'LIKE', '%kids%')->orWhere('short_description', 'LIKE', '%kids%')->chunk(1000, function($products) {
            foreach ($products as $product) {
                DB::table('log_scraper_vs_ai')->where('product_id', $product->id)->delete();
                DB::table('product_suppliers')->where('product_id', $product->id)->delete();
                DB::table('scraped_products')->where('sku', $product->sku)->delete();
                DB::table('product_references')->where('product_id', $product->id)->delete();
                DB::table('user_products')->where('product_id', $product->id)->delete();
                DB::table('suggestion_products')->where('product_id', $product->id)->delete();
                $product->forceDelete();
            }
        });
    }
}
