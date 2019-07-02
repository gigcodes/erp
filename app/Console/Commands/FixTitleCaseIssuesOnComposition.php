<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class FixTitleCaseIssuesOnComposition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:fix-titlecase-for-composition';

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
        Product::chunk(1000, function($products) {
            foreach ($products as $product) {
                dump($product->id);
                $product->composition = title_case($product->composition);
                $product->save();
            }
        });
    }
}
