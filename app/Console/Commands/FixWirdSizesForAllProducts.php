<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class FixWirdSizesForAllProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sizes:fix-weird-sizes';

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
        Product::where('size', 'LIKE', "%½%")->chunk(1000, function($products) {
            foreach ($products as $product) {
                dump('Updating..');
                $product->size = str_replace(['½', ' ½'], '.5', $product->size);
                $product->save();
            }
        });
    }
}
