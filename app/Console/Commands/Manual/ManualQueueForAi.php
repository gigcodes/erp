<?php

namespace App\Console\Commands\Manual;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProductAi;
use App\Product;

class ManualQueueForAi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:queue-manually';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue all products manually';

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
        // Get all products queued for AI
        $products = Product::where('status_id', '>', 2)->where('stock', '>', 0)->limit(10)->get();

        // Loop over products
        foreach ( $products as $product ) {
            // Output product ID
            echo $product->id . "\n";

            // Queue for AI
            ProductAi::dispatch($product)->onQueue('product');
        }
    }
}
