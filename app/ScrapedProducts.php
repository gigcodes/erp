<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Brand;
use App\Product;
use App\ScrapStatistics;

class ScrapedProducts extends Model
{
    protected $casts = [
        'images' => 'array',
        'properties' => 'array',
    ];

    protected $fillable = [
        'sku',
        'website',
        'images',
        'properties',
        'title',
        'brand_id',
        'description',
        'url',
        'is_properly_updated',
        'is_price_updated',
        'is_enriched',
        'has_sku',
        'price',
        'can_be_deleted'
    ];

    public function bulkScrapeImport($arrBulkJson = [])
    {
        // Check array
        if (!is_array($arrBulkJson) || count($arrBulkJson) == 0) {
            // return false
            return false;
        }

        // Set count to 0
        $count = 0;

        // Loop over array
        foreach ($arrBulkJson as $json) {
            // Check for required values
            if (
                !empty($json->title) &&
                !empty($json->sku) &&
                !empty($json->brand_id) &&
                !empty($json->properties[ 'category' ])
            ) {
                $productsCreatorResult = Product::createProductByJson($json, 1);
            }

            // Product created successfully
            if ($productsCreatorResult) {
                // Add or update supplier / inventory
                SupplierInventory::firstOrCreate(['supplier' => $json->website, 'sku' => $json->sku, 'inventory' => $json->stock]);

                // Update count
                $count++;
            }
        }

        // Return count
        return $count;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function product()
    {
        return $this->hasOne('App\Product', 'sku', 'sku');
    }
}
