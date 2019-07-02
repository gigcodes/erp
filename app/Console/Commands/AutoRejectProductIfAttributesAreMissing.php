<?php

namespace App\Console\Commands;

use App\ListingHistory;
use App\Product;
use Illuminate\Console\Command;

class AutoRejectProductIfAttributesAreMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:reject-if-attribute-is-missing';

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
        Product::where('is_farfetched', 0)
            ->where('is_listing_rejected_automatically', 0)
            ->where('is_listing_rejected', 0)
            ->where('was_auto_rejected', 0)
            ->where('is_approved', 0)
            ->where(function($query) {
                $query = $query->where('short_description', '')
                ->orWhere('composition', '')
                ->orWhere('size', '')
                ->orWhere('price', 0);
                })->chunk(1000, function($products) {
                    foreach ($products as $product) {
                        dump('Rejected...');
                        $product->is_listing_rejected = 1;
                        $product->is_listing_rejected_automatically = 1;
                        $product->save();

                        $l = new ListingHistory();
                        $l->user_id = null;
                        $l->product_id = $product->id;
                        $l->action = 'AUTO_REJECTED_ATTRIBUTE_MISSING';
                        $l->content = ['action' => 'AUTO_REJECTED_ATTRIBUTE_MISSING'];
                        $l->save();
                    }
            });

    }
}
