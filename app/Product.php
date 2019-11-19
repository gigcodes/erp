<?php

namespace App;

use App\Helpers\StatusHelper;
use Dompdf\Exception;
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
use App\ProductQuicksellGroup;
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
    protected $dates = ['deleted_at'];
    protected $appends = [];
    protected $communication = '';
    protected $image_url = '';

    /**
     * Create new or update existing (scraped) product by JSON
     * This is only for Excel imports at the moment
     * @param $json
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function createProductByJson($json, $isExcel = 0, $nextExcelStatus = 2)
    {
        // Log before validating
        LogScraper::LogScrapeValidationUsingRequest($json, $isExcel);

        // Check for required values
        if (
            !empty($json->title) &&
            !empty($json->sku) &&
            !empty($json->brand_id)
        ) {
            // Check for unique product
            $data[ 'sku' ] = ProductHelper::getSku($json->sku);
            $validator = Validator::make($data, [
                'sku' => 'unique:products,sku'
            ]);

            // Get formatted prices
            $formattedPrices = self::_getPriceArray($json);

            // If validator fails we have an existing product
            if ($validator->fails()) {
                // Get the product from the database
                $product = Product::where('sku', $data[ 'sku' ])->first();

                // Return false if no product is found
                if (!$product) {
                    return false;
                }

                // Update from scrape to manual images
                if (!$product->is_approved && !$product->is_listing_rejected && $product->status_id == StatusHelper::$scrape && (int)$nextExcelStatus == StatusHelper::$unableToScrapeImages) {
                    $product->status_id = StatusHelper::$unableToScrapeImages;
                }

                // Update the name and description if the product is not approved and not rejected
                if (!$product->is_approved && !$product->is_listing_rejected) {
                    $product->name = ProductHelper::getRedactedText($json->title, 'name');
                    $product->short_description = ProductHelper::getRedactedText($json->description, 'short_description');
                }

                // Update color, composition and material used if the product is not approved
                if (!$product->is_approved) {
                    // Set color
                    if (isset($json->properties[ 'color' ])) {
                        $product->color = trim($json->properties[ 'color' ] ?? '');
                    }

                    // Set composition
                    if (isset($json->properties[ 'composition' ])) {
                        $product->composition = ProductHelper::getRedactedText(trim($image->properties[ 'composition' ] ?? ''), 'composition');
                    }

                    // Set material used
                    if (isset($image->properties[ 'material_used' ])) {
                        $product->composition = ProductHelper::getRedactedText(trim($image->properties[ 'material_used' ] ?? ''), 'composition');
                    }
                }

                // Add sizes to the product
                if (isset($json->properties[ 'size' ]) && is_array($json->properties[ 'size' ]) && count($json->properties[ 'size' ]) > 0) {
                    // Implode the keys
                    $product->size = implode(',', array_keys($json->properties[ 'size' ]));

                    // Replace texts in sizes
                    $product->size = ProductHelper::getRedactedText($product->size, 'composition');
                }

                // Set product values
                $product->lmeasurement = isset($json->properties[ 'lmeasurement' ]) && $json->properties[ 'lmeasurement' ] > 0 ? $json->properties[ 'lmeasurement' ] : null;
                $product->hmeasurement = isset($json->properties[ 'hmeasurement' ]) && $json->properties[ 'hmeasurement' ] > 0 ? $json->properties[ 'hmeasurement' ] : null;
                $product->dmeasurement = isset($json->properties[ 'dmeasurement' ]) && $json->properties[ 'dmeasurement' ] > 0 ? $json->properties[ 'dmeasurement' ] : null;
                $product->price = $formattedPrices[ 'price' ];
                $product->price_inr = $formattedPrices[ 'price_inr' ];
                $product->price_special = $formattedPrices[ 'price_special' ];
                $product->is_scraped = $isExcel == 1 ? 0 : 1;
                $product->save();
                if($product){
                    foreach ($json->images as $image) {
                        try {
                             $jpg = \Image::make($image)->encode('jpg');
                        } catch (\Exception $e) {
                             $jpg = \Image::make(public_path() . '/uploads/excel-import/5dd3a2caa85299.71906716.A2.png')->encode('jpg');
                        }
                        $filename = substr($image, strrpos($image, '/'));
                        $filename = str_replace("/","",$filename);
                        $media = MediaUploader::fromString($jpg)->useFilename($filename)->upload();
                        $product->attachMedia($media, config('constants.excelimporter'));
                    }

                }
                
                // Update the product status
                ProductStatus::updateStatus($product->id, 'UPDATED_EXISTING_PRODUCT_BY_JSON', 1);

                // Set on sale
                if ($json->is_sale) {
                    $product->is_on_sale = 1;
                    $product->save();
                }

                // Check for valid supplier and store details linked to supplier
                if ($dbSupplier = Supplier::where('scraper_name', $json->website)->first()) {
                    if ($product) {
                        $product->suppliers()->syncWithoutDetaching([
                            $dbSupplier->id => [
                                'title' => ProductHelper::getRedactedText($json->title, 'name'),
                                'description' => ProductHelper::getRedactedText($json->description, 'short_description'),
                                'supplier_link' => $json->url,
                                'stock' => $json->stock,
                                'price' => $formattedPrices[ 'price' ],
                                'price_discounted' => $formattedPrices[ 'price_discounted' ],
                                'size' => $json->properties[ 'size' ] ?? null,
                                'color' => $json->properties[ 'color' ],
                                'composition' => ProductHelper::getRedactedText($json->properties[ 'composition' ], 'composition'),
                                'sku' => $json->original_sku
                            ]
                        ]);
                    }
                }

                // Set duplicate count to 0
                $duplicateCount = 0;

                // Set empty array to hold supplier prices
                $supplierPrices = [];

                // Loop over each supplier
                foreach ($product->suppliers_info as $info) {
                    if ($info->price != '') {
                        $supplierPrices[] = $info->price;
                    }
                }

                // Loop over supplierPrices to find duplicates
                foreach (array_count_values($supplierPrices) as $price => $count) {
                    $duplicateCount++;
                }

                if ($duplicateCount > 1) {
                    // Different price
                    $product->is_price_different = 1;
                } else {
                    // Same price
                    $product->is_price_different = 0;
                }

                // Add 1 to stock - TODO: We can calculate the real stock across all suppliers
                $product->stock += 1;
                $product->save();

                // Set parameters for scrap activity
                $params = [
                    'website' => $json->website,
                    'scraped_product_id' => $product->id,
                    'status' => 1
                ];

                // Log scrap activity
                ScrapActivity::create($params);

                // Return
                return true;
            } else {
                // Create new product
                $product = new Product;

                // Return false if product could not be created
                if ($product == null) {
                    return false;
                }

                // Set product values
                $product->status_id = ($isExcel == 1 ? $nextExcelStatus : 3);
                $product->sku = $data[ 'sku' ];
                $product->supplier = $json->website;
                $product->brand = $json->brand_id;
                $product->category = $json->properties[ 'category' ] ?? 0;
                $product->name = ProductHelper::getRedactedText($json->title, 'name');
                $product->short_description = ProductHelper::getRedactedText($json->description, 'short_description');
                $product->supplier_link = $json->url;
                $product->stage = 3;
                $product->is_scraped = $isExcel == 1 ? 0 : 1;
                $product->stock = 1;
                $product->is_without_image = 1;
                $product->is_on_sale = $json->is_sale ? 1 : 0;
                $product->composition = ProductHelper::getRedactedText($json->properties[ 'composition' ], 'composition');
                $product->color = $json->properties[ 'color' ] ?? null;
                $product->size = $json->properties[ 'size' ] ?? null;
                $product->lmeasurement = isset($json->properties[ 'lmeasurement' ]) && $json->properties[ 'lmeasurement' ] > 0 ? $json->properties[ 'lmeasurement' ] : null;
                $product->hmeasurement = isset($json->properties[ 'hmeasurement' ]) && $json->properties[ 'hmeasurement' ] > 0 ? $json->properties[ 'hmeasurement' ] : null;
                $product->dmeasurement = isset($json->properties[ 'dmeasurement' ]) && $json->properties[ 'dmeasurement' ] > 0 ? $json->properties[ 'dmeasurement' ] : null;
                $product->measurement_size_type = $json->properties[ 'measurement_size_type' ];
                $product->made_in = $json->properties[ 'made_in' ] ?? null;
                $product->price = $formattedPrices[ 'price' ];
                $product->price_inr = $formattedPrices[ 'price_inr' ];
                $product->price_special = $formattedPrices[ 'price_special' ];

                // Try to save the product
                try {
                    $product->save();
                } catch (\Exception $exception) {
                    return false;
                }

                // Update the product status
                ProductStatus::updateStatus($product->id, 'CREATED_NEW_PRODUCT_BY_JSON', 1);

                // Check for valid supplier and store details linked to supplier
                if ($dbSupplier = Supplier::where('scraper_name', $json->website)->first()) {
                    if ($product) {
                        $product->suppliers()->syncWithoutDetaching([
                            $dbSupplier->id => [
                                'title' => ProductHelper::getRedactedText($json->title, 'name'),
                                'description' => ProductHelper::getRedactedText($json->description, 'short_description'),
                                'supplier_link' => $json->url,
                                'stock' => $json->stock,
                                'price' => $formattedPrices[ 'price' ],
                                'price_discounted' => $formattedPrices[ 'price_discounted' ],
                                'size' => $json->properties[ 'size' ] ?? null,
                                'color' => $json->properties[ 'color' ],
                                'composition' => ProductHelper::getRedactedText($json->properties[ 'composition' ], 'composition'),
                                'sku' => $json->original_sku
                            ]
                        ]);
                    }
                }

                // Return true
                return true;
            }
        }

        // Return false by default
        return false;
    }

    private static function _getPriceArray($json)
    {
        // Get brand object by brand ID
        $brand = Brand::find($json->brand_id);

        if (strpos($json->price, ',') !== false) {
            if (strpos($json->price, '.') !== false) {
                if (strpos($json->price, ',') < strpos($json->price, '.')) {
                    $finalPrice = str_replace(',', '', $json->price);
                } else {
                    $finalPrice = str_replace(',', '|', $json->price);
                    $finalPrice = str_replace('.', ',', $finalPrice);
                    $finalPrice = str_replace('|', '.', $finalPrice);
                    $finalPrice = str_replace(',', '', $finalPrice);
                }
            } else {
                $finalPrice = str_replace(',', '.', $json->price);
            }
        } else {
            $finalPrice = $json->price;
        }

        // Get numbers and trim final price
        $finalPrice = trim(preg_replace('/[^0-9\.]/i', '', $finalPrice));

        //
        if (strpos($finalPrice, '.') !== false) {
            // Explode price
            $exploded = explode('.', $finalPrice);

            // Check if there are numbers after the dot
            if (strlen($exploded[ 1 ]) > 2) {
                if (count($exploded) > 2) {
                    $sliced = array_slice($exploded, 0, 2);
                } else {
                    $sliced = $exploded;
                }

                // Convert price to the lowest minor unit
                $finalPrice = implode('', $sliced);
            }
        }

        // Set price to rounded finalPrice
        $price = round($finalPrice);

        // Check if the euro to rupee rate is set
        if (!empty($brand->euro_to_inr)) {
            $priceInr = $brand->euro_to_inr * $price;
        } else {
            $priceInr = Setting::get('euro_to_inr') * $price;
        }

        // Build price in INR and special price
        $priceInr = round($priceInr, -3);
        $priceSpecial = $priceInr - ($priceInr * $brand->deduction_percentage) / 100;
        $priceSpecial = round($priceSpecial, -3);

        // Make discounted price in the correct format
        if (strpos($json->discounted_price, ',') !== false) {
            if (strpos($json->discounted_price, '.') !== false) {
                if (strpos($json->discounted_price, ',') < strpos($json->discounted_price, '.')) {
                    $finalDiscountedPrice = str_replace(',', '', $json->discounted_price);
                } else {
                    $finalDiscountedPrice = str_replace(',', '|', $json->discounted_price);
                    $finalDiscountedPrice = str_replace('.', ',', $finalDiscountedPrice);
                    $finalDiscountedPrice = str_replace('|', '.', $finalDiscountedPrice);
                    $finalDiscountedPrice = str_replace(',', '', $finalDiscountedPrice);
                }
            } else {
                $finalDiscountedPrice = str_replace(',', '.', $json->discounted_price);
            }
        } else {
            $finalDiscountedPrice = $json->discounted_price;
        }

        // Convert the price to the lowest minor unit
        $finalDiscountedPrice = trim(preg_replace('/[^0-9\.]/i', '', $finalDiscountedPrice));

        if (strpos($finalDiscountedPrice, '.') !== false) {
            $exploded = explode('.', $finalDiscountedPrice);

            if (strlen($exploded[ 1 ]) > 2) {
                if (count($exploded) > 2) {
                    $sliced = array_slice($exploded, 0, 2);
                } else {
                    $sliced = $exploded;
                }

                $finalDiscountedPrice = implode('', $sliced);
            }
        }

        // Return array with prices.
        return [
            'price' => $price,
            'price_discounted' => round($finalDiscountedPrice),
            'price_inr' => $priceInr,
            'price_special' => $priceSpecial
        ];
    }

    public function messages()
    {
        return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'product')->latest()->first();
    }

    public function product_category()
    {
        return $this->belongsTo('App\Category', 'category');
    }

    public function log_scraper_vs_ai()
    {
        return $this->hasMany('App\LogScraperVsAi');
    }

    public function getCommunicationAttribute()
    {
        return $this->messages();
    }

    public function getImageurlAttribute()
    {
        return $this->getMedia(config('constants.media_tags'))->first() ? $this->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification');
    }

    public function suppliers()
    {
        return $this->belongsToMany('App\Supplier', 'product_suppliers', 'product_id', 'supplier_id');
    }

    public function suppliers_info()
    {
        return $this->hasMany('App\ProductSupplier');
    }

    public function private_views()
    {
        return $this->belongsToMany('App\PrivateView', 'private_view_products', 'product_id', 'private_view_id');
    }

    public function suggestions()
    {
        return $this->belongsToMany('App\Suggestion', 'suggestion_products', 'product_id', 'suggestion_id');
    }

    public function amends()
    {
        return $this->hasMany(CropAmends::class, 'product_id', 'id');
    }

    public function brands()
    {
        return $this->hasOne('App\Brand', 'id', 'brand');
    }

    public function references()
    {
        return $this->hasMany('App\ProductReference');
    }

    public static function getPendingProductsCount($roleType)
    {
        $stage = new Stage();
        $stage_no = intval($stage->getID($roleType));

        return DB::table('products')
            ->where('stage', $stage_no - 1)
            ->where('isApproved', '!=', -1)
            ->whereNull('dnf')
            ->whereNull('deleted_at')
            ->count();
    }

    public function purchases()
    {
        return $this->belongsToMany('App\Purchase', 'purchase_products', 'product_id', 'purchase_id');
    }

    public function sizes()
    {
        return $this->hasMany(ProductSizes::class);
    }

    public function orderproducts()
    {
        return $this->hasMany('App\OrderProduct', 'sku', 'sku');
    }

    public function scraped_products()
    {
        return $this->hasOne('App\ScrapedProducts', 'sku', 'sku');
    }

    public function many_scraped_products()
    {
        return $this->hasMany('App\ScrapedProducts', 'sku', 'sku');
    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_products', 'product_id', 'user_id');
    }

    public function cropApprover()
    {
        return $this->belongsTo(User::class, 'crop_approved_by', 'id');
    }

    public function cropRejector()
    {
        return $this->belongsTo(User::class, 'crop_rejected_by', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'listing_rejected_by', 'id');
    }

    public function cropOrderer()
    {
        return $this->belongsTo(User::class, 'crop_ordered_by', 'id');
    }

    public function rejectedCropApprover()
    {
        return $this->hasOne(User::class, 'reject_approved_by', 'id');
    }

    public function activities()
    {
        return $this->hasMany(ListingHistory::class, 'product_id', 'id');
    }

    public function statuses()
    {
        return $this->hasMany(ProductStatus::class, 'product_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany(ProductQuicksellGroup::class, 'product_id', 'id');
    }

}
