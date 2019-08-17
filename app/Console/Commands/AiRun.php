<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LogScraperVsAi;
use App\Product;
use seo2websites\GoogleVision\GoogleVisionHelper;

class AiRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do a run with AI APIs on product listings';

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
        // Get all listings
        $orderByPritority = "CASE WHEN products.brand IN (27, 42, 11, 19, 24) AND products.supplier IN ('G & B Negozionline', 'Tory Burch', 'Wise Boutique', 'Biffi Boutique (S.P.A.)', 'MARIA STORE', 'Lino Ricci Lei', 'Al Duca d\'Aosta', 'Tiziana Fausti', 'Leam') THEN 0 ELSE 1 END";
        $products = Product::with( 'product_category' )->leftJoin( 'log_scraper_vs_ai', 'products.id', '=', 'log_scraper_vs_ai.product_id' )->where( 'ai_name', NULL )->orderByRaw($orderByPritority)->orderBy( 'products.listing_approved_at', 'DESC' )->limit( 250 )->get( [ 'products.id' ] );

        // Loop over products
        foreach ( $products as $product ) {
            // Check if the product already exists
            $logScraperVsAi = LogScraperVsAi::where( [ 'product_id' => $product->id ] )->first();

            // Continue if the product is already scraped
            if ( $logScraperVsAi !== NULL ) {
                continue;
            }

            // Get full product
            $product = Product::with( 'product_category' )->where( 'id', $product->id )->first();

            // Output something to the command line to know we are still running
            echo "Start Vision for product " . $product->id . "\n";

            // Get array product images
            $arrMedia = $product->getMedia( 'gallery' );

            // Set empty array for image URLs
            $arrImages = [];

            // Loop over media to get URLs
            foreach ( $arrMedia as $media ) {
                $arrImages[] = 'https://erp.amourint.com/' . $media->disk . '/' . $media->filename . '.' . $media->extension;
            }

            // Skip to the next one if there are no images
            if ( count( $arrImages ) == 0 ) {
                continue;
            }

            // Set json with original data
            $resultScraper = [
                'category' => $product->product_category->title,
                'color' => $product->color,
                'composite' => $product->composition,
                'gender' => ''
            ];

            // Run test
            $resultAI = GoogleVisionHelper::getPropertiesFromImageSet( $arrImages );

            // Log result
            $logScraperVsAi = new LogScraperVsAi();
            $logScraperVsAi->product_id = $product->id;
            $logScraperVsAi->ai_name = 'Google Vision';
            $logScraperVsAi->media_input = json_encode( $arrImages );
            $logScraperVsAi->result_scraper = json_encode( $resultScraper );
            $logScraperVsAi->result_ai = json_encode( $resultAI );
            $logScraperVsAi->save();

            // Remove 'is_listing_rejected' from product
            $product->is_listing_rejected = 0;
            $product->save();
        }
    }
}
