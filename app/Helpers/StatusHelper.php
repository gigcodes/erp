<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\ProductAi;

class StatusHelper extends Model
{
    public static $import = 1;
    public static $scrape = 2;
    public static $AI = 3;
    public static $autoCrop = 4;
    public static $cropApproval = 5;
    public static $cropSequencing = 6;
    public static $imageEnhancement = 7;
    public static $cropApprovalConfirmation = 8;
    public static $finalApproval = 9;
    public static $manualAttribute = 10;
    public static $pushToMagento = 11;
    public static $inMagento = 12;
    public static $unableToScrape = 13;
    public static $unableToScrapeImages = 14;
    public static $isBeingCropped = 15;
    public static $cropSkipped = 16;
    public static $isBeingEnhanced = 17;

    public static function updateStatus(Product $product, $newStatus=0) {
        // Update status to AI
        if ( $newStatus == 3 ) {
            // Queue for AI
            ProductAi::dispatch( $product )->onQueue('product');;
        }

        // Return
        return;
    }

}
