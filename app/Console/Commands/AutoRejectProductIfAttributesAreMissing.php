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
        // Get all products with missing details
        $products = Product::where( [ [ 'is_farfetched', 0 ], [ 'is_listing_rejected', 0 ], [ 'is_listing_rejected_automatically', 0 ] ] )->where( function ( $query ) {
            $query->where( 'name', '=', '' )
                ->orWhere( 'short_description', '=', '' )
                ->orWhere( 'composition', '=', '' )
                ->orWhere( 'size', '=', '' );
        } )->get();

        // Loop over products
        foreach ( $products as $product ) {
            // Set to auto rejected
            $product->is_listing_rejected = 1;
            $product->is_listing_rejected_automatically = 1;
            $product->save();

            // Update listing history
            $listingHistory = new ListingHistory();
            $listingHistory->user_id = NULL;
            $listingHistory->product_id = $product->id;
            $listingHistory->action = 'AUTO_REJECTED_ATTRIBUTE_MISSING';
            $listingHistory->content = [ 'action' => 'AUTO_REJECTED_ATTRIBUTE_MISSING' ];
            $listingHistory->save();
        }
    }
}
