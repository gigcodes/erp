<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\LogScraperVsAi;
use App\Product;

class logScraperVsAiController extends Controller
{
    public function index( Request $request )
    {
        // Set empty alert
        $alert = '';

        // Load product
        $product = Product::find( $request->id );

        // Check for submit
        if ( !empty( $request->id ) && !empty( $request->category ) && !empty( $request->color ) ) {
            // Product not found
            if ( $product === NULL ) {
                return redirect()->back()->with( 'alert', 'Product not found' );
            }

            // Update product
            // TODO

            // Redirect to rejected listing
            return redirect()->action('ProductController@showRejectedListedProducts');
        } elseif ( !empty( $request->id ) && !empty( $request->category ) && empty( $request->color ) ) {
            return redirect()->back()->with( 'alert', 'Color not set' );
        } elseif ( !empty( $request->id ) && empty( $request->category ) && !empty( $request->color ) ) {
            return redirect()->back()->with( 'alert', 'Category not set' );
        }

        // Get results
        $results = LogScraperVsAi::where( 'product_id', $request->id )->orderBy( 'created_at', 'desc' )->get();

        // Get keywords by result
        $keywords = LogScraperVsAi::getAiKeywordsFromResults( $results );

        // Get gender by scraper category
        $genderScraper = \App\LogScraperVsAi::getGenderByCategoryId((int) $product->category);

        // Return view
        return view( 'log-scraper-vs-ai.index', compact( 'results', 'keywords', 'genderScraper' ) );
    }
}
