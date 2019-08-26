<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Traits\LogsActivity;
use App\ScrapedProducts;
use App\ScrapActivity;
use App\SupplierInventory;
use App\Helpers\ProductHelper;
use App\Loggers\LogScraper;
use App\Services\Products\ProductsCreator;

class Product extends Model
{

//	use LogsActivity;
    use Mediable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku'
    ];
    protected $dates = [ 'deleted_at' ];
    protected $appends = [];
    protected $communication = '';
    protected $image_url = '';

    /**
     * Create new or update existing (scraped) product by JSON
     * This is only for Excel imports at the moment
     * @param $json
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function createProductByJson( $json, $isExcel=0 )
    {
        // Log before validating
        LogScraper::LogScrapeValidationUsingRequest($json);

        // Check for required values
        if (
            !empty( $json->title ) &&
            !empty( $json->sku ) &&
            !empty( $json->brand_id ) &&
            !empty( $json->properties['category'] )
        ) {

            // Get SKU
            $sku = ProductHelper::getSku($json->sku);

            // Get brand
            $brand = Brand::where('name', $json->brand)->first();

            // No brand found?
            if (!$brand) {
                // Check for reference
                $brand = Brand::where('references', 'LIKE', '%' . $json->brand . '%');

                if (!$brand) {
                    return response()->json([
                        'status' => 'invalid_brand'
                    ]);
                }
            }

            // Get this product from scraped products
            $scrapedProduct = ScrapedProducts::where('sku', $sku)->where('website', $json->website)->first();
            if ($scrapedProduct) {
                // Add scrape statistics
                $scrapStatistics = new ScrapStatistics();
                $scrapStatistics->supplier = $json->website;
                $scrapStatistics->type = 'EXISTING_SCRAP_PRODUCT';
                $scrapStatistics->brand = $brand->name;
                $scrapStatistics->url = $json->url;
                $scrapStatistics->description = $json->sku;
                $scrapStatistics->save();

                // Set values for existing scraped product
                $scrapedProduct->is_excel = 1;
                $scrapedProduct->properties = $json->properties;
                $scrapedProduct->is_sale = $json->is_sale ?? 0;
                $scrapedProduct->title = $json->title;
                $scrapedProduct->brand_id = $brand->id;
                $scrapedProduct->currency = $json->currency;
                $scrapedProduct->price = $json->price;
                if ($json->currency) {
                    $scrapedProduct->price_eur = (float)$json->price;
                }
                $scrapedProduct->discounted_price = $json->discounted_price;
                $scrapedProduct->original_sku = trim($json->sku);
                $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
                $scrapedProduct->save();
                $scrapedProduct->touch();
            } else {
                // Add scrape statistics
                $scrapStatistics = new ScrapStatistics();
                $scrapStatistics->supplier = $json->website;
                $scrapStatistics->type = 'NEW_SCRAP_PRODUCT';
                $scrapStatistics->brand = $brand->name;
                $scrapStatistics->url = $json->url;
                $scrapStatistics->description = $json->sku;
                $scrapStatistics->save();

                // Create new scraped product
                $scrapedProduct = new ScrapedProducts();
                $images = $isExcel == 1 && $json->images ?? [];
                $scrapedProduct->images = $images;
                $scrapedProduct->is_excel = 1;
                $scrapedProduct->sku = $sku;
                $scrapedProduct->original_sku = trim($json->sku);
                $scrapedProduct->discounted_price = $json->discounted_price;
                $scrapedProduct->is_sale = $json->is_sale ?? 0;
                $scrapedProduct->has_sku = 1;
                $scrapedProduct->url = $json->url;
                $scrapedProduct->title = $json->title ?? 'N/A';
                $scrapedProduct->description = $json->description;
                $scrapedProduct->properties = $json->properties;
                $scrapedProduct->currency = $json->currency;
                $scrapedProduct->price = $json->price;
                if ($json->currency == 'EUR' ) {
                    $scrapedProduct->price_eur = (float)$json->price;
                }
                $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
                $scrapedProduct->website = $json->website;
                $scrapedProduct->brand_id = $brand->id;
                $scrapedProduct->save();
            }

            // Create or update product
            app(ProductsCreator::class)->createProduct($scrapedProduct, 1);

            // Return response
            return response()->json([
                'status' => 'Added items successfuly!'
            ]);
        }

        // Return false by default
        return false;
    }

    private static function _getPriceArray( $json )
    {
        // Get brand object by brand ID
        $brand = Brand::find( $json->brand_id );

        if ( strpos( $json->price, ',' ) !== false ) {
            if ( strpos( $json->price, '.' ) !== false ) {
                if ( strpos( $json->price, ',' ) < strpos( $json->price, '.' ) ) {
                    $finalPrice = str_replace( ',', '', $json->price );
                } else {
                    $finalPrice = str_replace( ',', '|', $json->price );
                    $finalPrice = str_replace( '.', ',', $finalPrice );
                    $finalPrice = str_replace( '|', '.', $finalPrice );
                    $finalPrice = str_replace( ',', '', $finalPrice );
                }
            } else {
                $finalPrice = str_replace( ',', '.', $json->price );
            }
        } else {
            $finalPrice = $json->price;
        }

        // Get numbers and trim final price
        $finalPrice = trim( preg_replace( '/[^0-9\.]/i', '', $finalPrice ) );

        //
        if ( strpos( $finalPrice, '.' ) !== false ) {
            // Explode price
            $exploded = explode( '.', $finalPrice );

            // Check if there are numbers after the dot
            if ( strlen( $exploded[ 1 ] ) > 2 ) {
                if ( count( $exploded ) > 2 ) {
                    $sliced = array_slice( $exploded, 0, 2 );
                } else {
                    $sliced = $exploded;
                }

                // Convert price to the lowest minor unit
                $finalPrice = implode( '', $sliced );
            }
        }

        // Set price to rounded finalPrice
        $price = round( $finalPrice );

        // Check if the euro to rupee rate is set
        if ( !empty( $brand->euro_to_inr ) ) {
            $priceInr = $brand->euro_to_inr * $price;
        } else {
            $priceInr = Setting::get( 'euro_to_inr' ) * $price;
        }

        // Build price in INR and special price
        $priceInr = round( $priceInr, -3 );
        $priceSpecial = $priceInr - ( $priceInr * $brand->deduction_percentage ) / 100;
        $priceSpecial = round( $priceSpecial, -3 );

        // Make discounted price in the correct format
        if ( strpos( $json->discounted_price, ',' ) !== false ) {
            if ( strpos( $json->discounted_price, '.' ) !== false ) {
                if ( strpos( $json->discounted_price, ',' ) < strpos( $json->discounted_price, '.' ) ) {
                    $finalDiscountedPrice = str_replace( ',', '', $json->discounted_price );
                } else {
                    $finalDiscountedPrice = str_replace( ',', '|', $json->discounted_price );
                    $finalDiscountedPrice = str_replace( '.', ',', $finalDiscountedPrice );
                    $finalDiscountedPrice = str_replace( '|', '.', $finalDiscountedPrice );
                    $finalDiscountedPrice = str_replace( ',', '', $finalDiscountedPrice );
                }
            } else {
                $finalDiscountedPrice = str_replace( ',', '.', $json->discounted_price );
            }
        } else {
            $finalDiscountedPrice = $json->discounted_price;
        }

        // Convert the price to the lowest minor unit
        $finalDiscountedPrice = trim( preg_replace( '/[^0-9\.]/i', '', $finalDiscountedPrice ) );

        if ( strpos( $finalDiscountedPrice, '.' ) !== false ) {
            $exploded = explode( '.', $finalDiscountedPrice );

            if ( strlen( $exploded[ 1 ] ) > 2 ) {
                if ( count( $exploded ) > 2 ) {
                    $sliced = array_slice( $exploded, 0, 2 );
                } else {
                    $sliced = $exploded;
                }

                $finalDiscountedPrice = implode( '', $sliced );
            }
        }

        // Return array with prices.
        return [
            'price' => $price,
            'price_discounted' => round( $finalDiscountedPrice ),
            'price_inr' => $priceInr,
            'price_special' => $priceSpecial
        ];
    }

    public function messages()
    {
        return $this->hasMany( 'App\Message', 'moduleid' )->where( 'moduletype', 'product' )->latest()->first();
    }

    public function product_category()
    {
        return $this->belongsTo( 'App\Category', 'category' );
    }

    public function log_scraper_vs_ai()
    {
        return $this->hasMany( 'App\LogScraperVsAi' );
    }

    public function getCommunicationAttribute()
    {
        return $this->messages();
    }

    public function getImageurlAttribute()
    {
        return $this->getMedia( config( 'constants.media_tags' ) )->first() ? $this->getMedia( config( 'constants.media_tags' ) )->first()->getUrl() : '';
    }

    public function notifications()
    {
        return $this->hasMany( 'App\Notification' );
    }

    public function suppliers()
    {
        return $this->belongsToMany( 'App\Supplier', 'product_suppliers', 'product_id', 'supplier_id' );
    }

    public function suppliers_info()
    {
        return $this->hasMany( 'App\ProductSupplier' );
    }

    public function private_views()
    {
        return $this->belongsToMany( 'App\PrivateView', 'private_view_products', 'product_id', 'private_view_id' );
    }

    public function suggestions()
    {
        return $this->belongsToMany( 'App\Suggestion', 'suggestion_products', 'product_id', 'suggestion_id' );
    }

    public function amends()
    {
        return $this->hasMany( CropAmends::class, 'product_id', 'id' );
    }

    public function brands()
    {
        return $this->hasOne( 'App\Brand', 'id', 'brand' );
    }

    public function references()
    {
        return $this->hasMany( 'App\ProductReference' );
    }

    public static function getPendingProductsCount( $roleType )
    {

        $stage = new Stage();
        $stage_no = intval( $stage->getID( $roleType ) );

        return DB::table( 'products' )
            ->where( 'stage', $stage_no - 1 )
            ->where( 'isApproved', '!=', -1 )
            ->whereNull( 'dnf' )
            ->whereNull( 'deleted_at' )
            ->count();
    }

    public function purchases()
    {
        return $this->belongsToMany( 'App\Purchase', 'purchase_products', 'product_id', 'purchase_id' );
    }

    public function sizes()
    {
        return $this->hasMany( ProductSizes::class );
    }

    public function orderproducts()
    {
        return $this->hasMany( 'App\OrderProduct', 'sku', 'sku' );
    }

    public function scraped_products()
    {
        return $this->hasOne( 'App\ScrapedProducts', 'sku', 'sku' );
    }

    public function many_scraped_products()
    {
        return $this->hasMany( 'App\ScrapedProducts', 'sku', 'sku' );
    }

    public function user()
    {
        return $this->belongsToMany( 'App\User', 'user_products', 'product_id', 'user_id' );
    }

    public function cropApprover()
    {
        return $this->belongsTo( User::class, 'crop_approved_by', 'id' );
    }

    public function cropRejector()
    {
        return $this->belongsTo( User::class, 'crop_rejected_by', 'id' );
    }

    public function approver()
    {
        return $this->belongsTo( User::class, 'approved_by', 'id' );
    }

    public function rejector()
    {
        return $this->belongsTo( User::class, 'listing_rejected_by', 'id' );
    }

    public function cropOrderer()
    {
        return $this->belongsTo( User::class, 'crop_ordered_by', 'id' );
    }

    public function rejectedCropApprover()
    {
        return $this->hasOne( User::class, 'reject_approved_by', 'id' );
    }

    public function activities()
    {
        return $this->hasMany( ListingHistory::class, 'product_id', 'id' );
    }

    public function statuses()
    {
        return $this->hasMany( ProductStatus::class, 'product_id', 'id' );
    }
}
