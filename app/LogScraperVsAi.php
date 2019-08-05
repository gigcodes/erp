<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use seo2websites\GoogleVision\LogGoogleVision;

class LogScraperVsAi extends Model
{
    protected $table = 'log_scraper_vs_ai';

    public static function getAiKeywordsFromResults( $results )
    {
        // Set empty array for images
        $arrImages = [];

        // Loop over results
        foreach ( $results as $result ) {
            // Get all images
            $jsonImages = json_decode( $result->media_input );

            // Loop over jsonImages and add them to array
            foreach ( $jsonImages as $key => $image ) {
                $arrImages[] = $image;
            }
        }

        // Create array with unique values
        $arrImages = array_unique( $arrImages );

        // Set empty array to hold keywords
        $arrKeywords = [];

        // Query results
        $logResults = LogGoogleVision::whereIn( 'image_url', $arrImages )->get();

        // Loop over results
        foreach ( $logResults as $logResult ) {
            // Explode response by newline
            $response = explode( "\n", $logResult->response );

            // Loop over response
            foreach ( $response as $row ) {
                // Store best guess label
                if ( substr( $row, 0, 17 ) == 'Best guess label:' ) {
                    $arrKeywords = self::_addKeyword( $arrKeywords, substr( $row, 18 ) );
                }

                // Store Object
                if ( substr( $row, 0, 7 ) == 'Object:' ) {
                    $arrKeywords = self::_addKeyword( $arrKeywords, substr( $row, 8, strpos($row, ',') -8 ) );
                }

                // Store Entity
                if ( substr( $row, 0, 7 ) == 'Entity:' ) {
                    $arrKeywords = self::_addKeyword( $arrKeywords, substr( $row, 8, strpos($row, ',') -8 ) );
                }
            }
        }

        // Reverse sort arrey by value
        arsort($arrKeywords);

        // Filter for categories
        $arrCategories = self::_filterCategories($arrKeywords);

        // Return array with keywords
        return $arrCategories;
    }

    private static function _addKeyword($arrKeywords, $keyword) {
        // Check if key (keyword) exists
        if ( key_exists($keyword, $arrKeywords) ) {
            // Add 1 to the keyword
            $arrKeywords[$keyword]++;
        } else {
            // Add the keyword with value 1
            $arrKeywords[$keyword] = 1;
        }

        // Return array
        return $arrKeywords;
    }

    public static function _filterCategories($arrKeywords) {
        // Set empty array for categories
        $arrCategories = [];

        // Loop over keywords
        foreach ( $arrKeywords as $keyword ) {
            // Skip empty keywords
            if ( !empty( $keyword ) ) {
                // Check database for result
                $dbResult = Category::where( 'title', $keyword )->orWhere('references', 'like', '%' . $keyword . '%')->first();

                // Result? Add the keyword
                if ( $dbResult !== NULL ) {
                    $arrCategories = $keyword;
                }
            }
        }

        // Return categories
        return $arrCategories;
    }

    public static function getCategoryByKeyword($keyword) {
        //
    }
}
