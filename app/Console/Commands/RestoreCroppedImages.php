<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class RestoreCroppedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:cropped-images';

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
        $products = Product::where('is_image_processed', 1)->get();

        foreach ($products as $product) {
            dd($product->media()->get());
        }
    }
}
