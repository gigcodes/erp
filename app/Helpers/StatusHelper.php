<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    public static $cropRejected = 18;
    public static $isBeingSequenced = 19;

    public static function updateStatus(\App\Product $product, $newStatus = 0)
    {
        // Update status to AI
        if ($newStatus == 3) {
            // Queue for AI
            ProductAi::dispatch($product)->onQueue('product');;
        }

        // Return
        return;
    }

    public static function getStatusCount($inStockOnly = 1)
    {
        // Get summary
        $productStats = DB::table('products')
            ->select('status_id', DB::raw('COUNT(id) as total'))
            ->where('stock', '>=', $inStockOnly)
            ->groupBy('status_id')
            ->pluck('total', 'status_id')->all();

        // Return array with stats
        return $productStats;
    }

    public static function getStatusCountByDateRange($startDate = '1900-01-01', $endDate = '2100-01-01', $inStockOnly = 1)
    {
        // Get summary
        $productStats = DB::table('products')
            ->select('status_id', DB::raw('COUNT(id) as total'))
            ->where('stock', '>=', $inStockOnly)
            ->whereBetween('created_at', [$startDate . ' 00:00', $endDate . ' 23:59'])
            ->groupBy('status_id')
            ->pluck('total', 'status_id')->all();

        // Return array with stats
        return $productStats;
    }

    public static function getCroppedCount($inStockOnly=1) {
        // Get status
        $status = self::getStatusCount($inStockOnly);

        // Return count for all statused beyond crop
        return array_sum($status) -
            (isset($status[self::$import]) ? $status[self::$import] : 0) -
            (isset($status[self::$scrape]) ? $status[self::$scrape] : 0) -
            (isset($status[self::$AI]) ? $status[self::$AI] : 0) -
            (isset($status[self::$autoCrop]) ? $status[self::$autoCrop] : 0) -
            (isset($status[self::$cropRejected]) ? $status[self::$cropRejected] : 0) -
            (isset($status[self::$cropSkipped]) ? $status[self::$cropSkipped] : 0) -
            (isset($status[self::$unableToScrape]) ? $status[self::$unableToScrape] : 0) -
            (isset($status[self::$unableToScrapeImages]) ? $status[self::$unableToScrapeImages] : 0);
    }

    public static function getCropApprovedCount($inStockOnly=1) {
        // Get status
        $status = self::getStatusCount($inStockOnly);

        // Return count
        return (isset($status[self::$cropSequencing]) ? $status[self::$cropSequencing] : 0) +
            (isset($status[self::$cropApprovalConfirmation]) ? $status[self::$cropApprovalConfirmation] : 0) +
            (isset($status[self::$isBeingSequenced]) ? $status[self::$isBeingSequenced] : 0) +
            (isset($status[self::$imageEnhancement]) ? $status[self::$imageEnhancement] : 0) +
            (isset($status[self::$isBeingEnhanced]) ? $status[self::$isBeingEnhanced] : 0) +
            (isset($status[self::$cropApprovalConfirmation]) ? $status[self::$cropApprovalConfirmation] : 0) +
            (isset($status[self::$finalApproval]) ? $status[self::$finalApproval] : 0) +
            (isset($status[self::$pushToMagento]) ? $status[self::$pushToMagento] : 0) +
            (isset($status[self::$inMagento]) ? $status[self::$inMagento] : 0);
    }

    public static function getCropRejectedCount($inStockOnly=1) {
        // Get status
        $status = self::getStatusCount($inStockOnly);

        // Return count
        return (isset($status[self::$cropRejected]) ? $status[self::$cropRejected] : 0);
    }

    public static function getTotalProductsScraped($inStockOnly=1) {
        // Get status
        $status = self::getStatusCount($inStockOnly);

        // Return count
        return array_sum($status) -
            (isset($status[self::$import]) ? $status[self::$import] : 0) -
            (isset($status[self::$scrape]) ? $status[self::$scrape] : 0) -
            (isset($status[self::$unableToScrape]) ? $status[self::$unableToScrape] : 0) -
            (isset($status[self::$unableToScrapeImages]) ? $status[self::$unableToScrapeImages] : 0);
    }
}
