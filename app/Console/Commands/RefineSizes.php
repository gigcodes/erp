<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;
use App\Services\Products\SizeReferences;

class RefineSizes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sizes:refine';

    private $enricher;

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
    public function __construct(SizeReferences $enricher)
    {
        $this->enricher = $enricher;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Product::where('size', '!=', '')->whereNotNull('size')->chunk(1000, function($products) {
            foreach ($products as $product) {
//                $this->enricher->basicRefining($product);
//                sleep(0.2);
//                $this->enricher->refineSizeToPintFive($product);
//                sleep(0.2);
                $this->enricher->refineSizeForIt($product);
//                sleep(0.2);
//                $this->enricher->refineForFr($product);
//                sleep(0.2);
//                $this->enricher->getSizeWithReferenceRoman($product);
//                sleep(0.2);
                if ($product->brand == 20 || $product->brand == 24) {
                    $this->enricher->refineForFemaleUSShoes($product);
                    $this->enricher->refineForMaleUSShoes($product);
                }
                if ($product->brand == 18 || $product->brand == 22 || $product->brand == 11) {
                    $this->enricher->refineForMaleUKShoes($product);
                    $this->enricher->refineForFemaleUKShoes($product);
                }
            }
        });
    }
}
