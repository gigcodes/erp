<?php

namespace App;

use App\Helpers\StatusHelper;
use Dompdf\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
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

    CONST STOCK_STATUS = [
        1 => "Active",
        2 => "Reserved",
        3 => "Damaged",
        4 => "On Hold"
    ];

//  use LogsActivity;
    use Mediable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
<<<<<<< HEAD
        'name',
        'brand',
        'category',
        'short_description',
        'price',
        'status_id',
=======
        'id',
>>>>>>> master
        'sku',
        'is_barcode_check',
        'has_mediables',
        'size_eu',
        'stock_status',
        'shopify_id',
        'scrap_priority',
        'assigned_to',
        'quick_product'
    ];

    protected $dates = ['deleted_at'];
    protected $appends = [];
    protected $communication = '';
    protected $image_url = '';

    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $flag = 0;
            if ($model->hasMedia(config('constants.attach_image_tag'))) {
                $flag = 1;
            }
            if($model->has_mediables != $flag) {
                \DB::table("products")->where("id", $model->id)->update(["has_mediables" => $flag]);
            }
        });

        static::updating(function ($product) {
            $oldCatID = $product->category;
            $newCatID = $product->getOriginal('category');
            if($oldCatID != $newCatID) {
                \DB::table("products")->where("id", $product->id)->update(["status_id" => StatusHelper::$autoCrop]);     
            }
        });

        static::created(function ($model) {
            $flag = 0;
            if ($model->hasMedia(config('constants.attach_image_tag'))) {
                $flag = 1;
            }
            if($model->has_mediables != $flag) {
                \DB::table("products")->where("id", $model->id)->update(["has_mediables" => $flag]);
            }
        });
    }

    /**
     * Create new or update existing (scraped) product by JSON
     * This is only for Excel imports at the moment
     * @param $json
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function createProductByJson($json, $isExcel = 0, $nextExcelStatus = 2)
    {
        // Log before validating
        //LogScraper::LogScrapeValidationUsingRequest($json, $isExcel);

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
                try {
                    
                    if($json->product_id > 0) {
                        $product = Product::where('id', $json->product_id)->first();
                    }else{
                        $product = Product::where('sku', $data[ 'sku' ])->first();
                    }

                } catch (\Exception $e) {
                    $product = Product::where('sku', $data[ 'sku' ])->first();
                }
                

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

                //Check if its json
                if (isset($json->properties[ 'size' ]) && is_array($json->properties[ 'size' ])) {
                    $json->properties[ 'size' ] = implode(',', $json->properties[ 'size' ]);
                }

                // Add sizes to the product
                if (isset($json->properties[ 'size' ]) && is_array($json->properties[ 'size' ]) && count($json->properties[ 'size' ]) > 0) {
                    // Implode the keys
                    $product->size = implode(',', array_values($json->properties[ 'size' ]));

                    // Replace texts in sizes
                    $product->size = ProductHelper::getRedactedText($product->size, 'composition');

                } elseif (isset($json->properties[ 'size' ]) && $json->properties[ 'size' ] != null) {
                    $product->size = $json->properties[ 'size' ];

                }

                // Set product values
                $product->lmeasurement = isset($json->properties[ 'lmeasurement' ]) && $json->properties[ 'lmeasurement' ] > 0 ? $json->properties[ 'lmeasurement' ] : null;
                $product->hmeasurement = isset($json->properties[ 'hmeasurement' ]) && $json->properties[ 'hmeasurement' ] > 0 ? $json->properties[ 'hmeasurement' ] : null;
                $product->dmeasurement = isset($json->properties[ 'dmeasurement' ]) && $json->properties[ 'dmeasurement' ] > 0 ? $json->properties[ 'dmeasurement' ] : null;
                $product->price = $formattedPrices[ 'price_eur' ];
                $product->price_inr = $formattedPrices[ 'price_inr' ];
                $product->price_inr_special = $formattedPrices[ 'price_inr_special' ];
                $product->price_inr_discounted = $formattedPrices[ 'price_inr_discounted' ];
                $product->price_eur_special = $formattedPrices[ 'price_eur_special' ];
                $product->price_eur_discounted = $formattedPrices[ 'price_eur_discounted' ];
                $product->is_scraped = $isExcel == 1 ? 0 : 1;
                $product->save();

                if ($product) {
                    if ($isExcel == 1) {
                        if (!$product->hasMedia(\Config('constants.excelimporter'))) {
                            foreach ($json->images as $image) {
                                try {
                                    $jpg = \Image::make($image)->encode('jpg');
                                } catch (\Exception $e) {
                                    $array = explode('/', $image);
                                    $filename_path = end($array);
                                    $jpg = \Image::make(public_path() . '/uploads/excel-import/' . $filename_path)->encode('jpg');
                                }
                                $filename = substr($image, strrpos($image, '/'));
                                $filename = str_replace(['/', '.JPEG', '.JPG', '.jpeg', '.jpg', '.PNG', '.png'], '', $filename);
                                $media = MediaUploader::fromString($jpg)->toDirectory('/product/' . floor($product->id / 10000) . '/' . $product->id)->useFilename($filename)->upload();
                                $product->attachMedia($media, config('constants.excelimporter'));
                            }
                        }
                    }

                }

                $product->checkExternalScraperNeed();


                // Update the product status
                ProductStatus::updateStatus($product->id, 'UPDATED_EXISTING_PRODUCT_BY_JSON', 1);

                // Set on sale
                if ($json->is_sale) {
                    $product->is_on_sale = 1;
                    $product->save();
                }

                // Check for valid supplier and store details linked to supplier
                if ($dbSupplier = Supplier::select('suppliers.id')->leftJoin("scrapers as sc", "sc.supplier_id", "suppliers.id")->where(function ($query) use ($json) {
                    $query->where('supplier', '=', $json->website)->orWhere('sc.scraper_name', '=', $json->website);
                })->first()) {
                    if ($product) {
                        $product->suppliers()->syncWithoutDetaching([
                            $dbSupplier->supplier_id => [
                                'title' => ProductHelper::getRedactedText($json->title, 'name'),
                                'description' => ProductHelper::getRedactedText($json->description, 'short_description'),
                                'supplier_link' => $json->url,
                                'stock' => $json->stock,
                                'price' => $formattedPrices[ 'price_eur' ],
                                'price_special' => $formattedPrices[ 'price_eur_special' ],
                                'supplier_id' => $dbSupplier->id,
                                'price_discounted' => $formattedPrices[ 'price_eur_discounted' ],
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
                //ScrapActivity::create($params);

                // Return
                //returning 1 for Product Updated
                return array('product_created' => 0, 'product_updated' => 1);
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
                $product->price = $formattedPrices[ 'price_eur' ];
                $product->price_eur_special = $formattedPrices[ 'price_eur_special' ];
                $product->price_eur_discounted = $formattedPrices[ 'price_eur_discounted' ];
                $product->price_inr = $formattedPrices[ 'price_inr' ];
                $product->price_inr_special = $formattedPrices[ 'price_inr_special' ];
                $product->price_inr_discounted = $formattedPrices[ 'price_inr_discounted' ];

                // Try to save the product
                try {
                    $product->save();
                    $product->checkExternalScraperNeed();
                    //$json->product_id = $product->id;
                    //$json->save();
                } catch (\Exception $exception) {
                    $product->save();
                    return false;
                }

                // Update the product status
                ProductStatus::updateStatus($product->id, 'CREATED_NEW_PRODUCT_BY_JSON', 1);

                // Check for valid supplier and store details linked to supplier
                if ($dbSupplier = Supplier::select('suppliers.id')->leftJoin("scrapers as sc", "sc.supplier_id", "suppliers.id")->where(function ($query) use ($json) {
                    $query->where('supplier', '=', $json->website)->orWhere('sc.scraper_name', '=', $json->website);
                })->first()) {
                    if ($product) {
                        $product->suppliers()->syncWithoutDetaching([
                            $dbSupplier->supplier_id => [
                                'title' => ProductHelper::getRedactedText($json->title, 'name'),
                                'description' => ProductHelper::getRedactedText($json->description, 'short_description'),
                                'supplier_link' => $json->url,
                                'stock' => $json->stock,
                                'price' => $formattedPrices[ 'price_eur' ],
                                'price_special' => $formattedPrices[ 'price_eur_special' ],
                                'supplier_id' => $dbSupplier->id,
                                'price_discounted' => $formattedPrices[ 'price_eur_discounted' ],
                                'size' => $json->properties[ 'size' ] ?? null,
                                'color' => $json->properties[ 'color' ],
                                'composition' => ProductHelper::getRedactedText($json->properties[ 'composition' ], 'composition'),
                                'sku' => $json->original_sku
                            ]
                        ]);
                    }
                }

                // Return true Product Created
                return array('product_created' => 1, 'product_updated' => 0);
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
                    $priceEur = str_replace(',', '', $json->price);
                } else {
                    $priceEur = str_replace(',', '|', $json->price);
                    $priceEur = str_replace('.', ',', $priceEur);
                    $priceEur = str_replace('|', '.', $priceEur);
                    $priceEur = str_replace(',', '', $priceEur);
                }
            } else {
                $priceEur = str_replace(',', '.', $json->price);
            }
        } else {
            $priceEur = $json->price;
        }

        // Get numbers and trim final price
        $priceEur = trim(preg_replace('/[^0-9\.]/i', '', $priceEur));

        //
        if (strpos($priceEur, '.') !== false) {
            // Explode price
            $exploded = explode('.', $priceEur);

            // Check if there are numbers after the dot
            if (strlen($exploded[ 1 ]) > 2) {
                if (count($exploded) > 2) {
                    $sliced = array_slice($exploded, 0, 2);
                } else {
                    $sliced = $exploded;
                }

                // Convert price to the lowest minor unit
                $priceEur = implode('', $sliced);
            }
        }

        // Set price to rounded finalPrice
        $priceEur = round($priceEur);

        // Check if the euro to rupee rate is set
        if (!empty($brand->euro_to_inr)) {
            $priceInr = $brand->euro_to_inr * $priceEur;
        } else {
            $priceInr = Setting::get('euro_to_inr') * $priceEur;
        }

        // Build price in INR and special price
        $priceInr = round($priceInr, -3);

        //Build Special Price In EUR
        if (!empty($priceEur) && !empty($priceInr)) {
            $priceEurSpecial = $priceEur - ($priceEur * $brand->deduction_percentage) / 100;
            $priceInrSpecial = $priceInr - ($priceInr * $brand->deduction_percentage) / 100;
        } else {
            $priceEurSpecial = '';
            $priceInrSpecial = '';
        }

        // Product on sale?
        if ($json->is_sale == 1 && $brand->sales_discount > 0 && !empty($priceEurSpecial)) {
            $priceEurDiscounted = $priceEurSpecial - ($priceEurSpecial * $brand->sales_discount) / 100;
            $priceInrDiscounted = $priceInrSpecial - ($priceInrSpecial * $brand->sales_discount) / 100;
        } else {
            $priceEurDiscounted = 0;
            $priceInrDiscounted = 0;
        }

        // Return prices
        return [
            'price_eur' => $priceEur,
            'price_eur_special' => $priceEurSpecial,
            'price_eur_discounted' => $priceEurDiscounted,
            'price_inr' => $priceInr,
            'price_inr_special' => $priceInrSpecial,
            'price_inr_discounted' => $priceInrDiscounted
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
        return $this->hasMany('App\OrderProduct', 'product_id', 'id');
    }

    public function scraped_products()
    {
        return $this->hasOne('App\ScrapedProducts', 'product_id', 'id');
    }

    public function many_scraped_products()
    {
        return $this->hasMany('App\ScrapedProducts', 'product_id', 'id');
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

    public function attachImagesToProduct($arrImages = null)
    {

        // check media exist or
        $mediaRecords = false;
        if ($this->hasMedia(\Config('constants.media_original_tag'))) {
            foreach($this->getMedia(\Config('constants.media_original_tag')) as $mRecord) {
                if(file_exists($mRecord->getAbsolutePath())) {
                    $mediaRecords = true;
                }
            }
        }
        
        if (!$mediaRecords || is_array($arrImages)) {
            // images given
            if (is_array($arrImages) && count($arrImages) > 0) {
                $scrapedProduct = true;
            } else {
                //getting image details from scraped Products
                $scrapedProduct = ScrapedProducts::where('sku', $this->sku)->orderBy('updated_at','desc')->first();
            }

            if ($scrapedProduct != null and $scrapedProduct != '') {
                //Looping through Product Images
                $countImageUpdated = 0;

                // Set arr images
                if (!is_array($arrImages)) {
                    $arrImages = $scrapedProduct->images;
                }

                foreach ($arrImages as $image) {
                    //check if image has http or https link
                    if (strpos($image, 'http') === false) {
                        continue;
                    }

                    try {
                        //generating image from image
                        //this was quick fix for redirect url issue
                        $redirect = \App\Helpers::findUltimateDestination($image,2);
                        if($redirect != null) {
                           $image = str_replace(" ","%20",$redirect);
                        }
                        $jpg = \Image::make($image)->encode('jpg');
                    } catch (\Exception $e) {
                        // if images are null
                        $jpg = null;
                        // need to define error update
                        if($scrapedProduct && is_object($scrapedProduct)) {
                            $lastScraper = ScrapedProducts::where("sku", $this->sku)->latest()->first();
                            if($lastScraper) {
                                $lastScraper->validation_result = $lastScraper->validation_result.PHP_EOL."[error] One or more images has an invalid URL : ".$image.PHP_EOL;
                                $lastScraper->save();
                            }
                        }

                    }
                    if ($jpg != null) {
                        $filename = substr($image, strrpos($image, '/'));
                        $filename = str_replace(['/', '.JPEG', '.JPG', '.jpeg', '.jpg', '.PNG', '.png'], '', urldecode($filename));

                        //save image to media
                        $media = MediaUploader::fromString($jpg)->toDirectory('/product/' . floor($this->id / 10000) . '/' . $this->id)->useFilename($filename)->onDuplicateReplace()->upload();
                        $this->attachMedia($media, config('constants.media_original_tag'));
                        $countImageUpdated++;
                    }
                }
                if ($countImageUpdated != 0) {
                    //Updating the Product Status
                    $this->status_id = StatusHelper::$AI;
                    $this->save();
                    // Call status update handler
                    StatusHelper::updateStatus($this, StatusHelper::$AI);
                }

            }
        }
    }

    // public function commonComposition($category,$composition)
    // {

    //     $hscodeList = HsCodeGroupsCategoriesComposition::where('category_id', $category)->where('composition',$composition)->first();

    //     if($hscodeList != null && $hscodeList != '')
    //     {
    //         $groupId = $hscodeList->hs_code_group_id;
    //         $group = HsCodeGroup::find($groupId);
    //         $hscodeDetails = SimplyDutyCategory::find($group->hs_code_id);
    //         if($hscodeDetails != null && $hscodeDetails != ''){
    //             if($hscodeDetails->correct_composition != null){
    //                 return $hscodeDetails->correct_composition;
    //             }else{
    //                 return $composition;
    //             }
                
    //         }else{
    //             return $composition;
    //         }
    //     }else{
    //         return $composition;
    //     }

    // }

     public function commonComposition($category,$composition)
    {

        $hscodeList = HsCodeGroupsCategoriesComposition::where('category_id', $category)->where('composition',$composition)->first();

        if($hscodeList != null && $hscodeList != '')
        {
            $groupId = $hscodeList->hs_code_group_id;
            $group = HsCodeGroup::find($groupId);
            if($group != null && $group != '' && $group->composition != null){
                return $group->composition;
            }else{
                $hscodeDetails = HsCode::find($group->hs_code_id);
                if($hscodeDetails != null && $hscodeDetails != ''){
                    if($hscodeDetails->correct_composition != null){
                        return $hscodeDetails->correct_composition;
                    }else{
                        return $composition;
                    }
                
                }else{
                    return $composition;
                }
            }
        }else{
            return $composition;
        }

    }

    public function hsCode($category,$composition){
        $hscodeList = HsCodeGroupsCategoriesComposition::where('category_id', $category)->where('composition',$composition)->first();

        if($hscodeList != null && $hscodeList != '')
        {
            $groupId = $hscodeList->hs_code_group_id;
            $group = HsCodeGroup::find($groupId);
            $hscodeDetails = HsCode::find($group->hs_code_id);
            if($hscodeDetails != null && $hscodeDetails != ''){
                if($hscodeDetails->description != null){
                    return $hscodeDetails->code;
                }else{
                    return false;
                }
                
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function isGroupExist($category,$composition,$parentCategory,$childCategory){
        $composition = strip_tags($composition);
        $composition = str_replace(['&nbsp;','/span>'],' ',$composition);

        $hscodeList = HsCodeGroupsCategoriesComposition::where('category_id', $category)->where('composition', 'LIKE', '%'.$composition.'%')->first();

        if($hscodeList != null && $hscodeList != '')
        {
            
            return false;
        }else{
          
            return true;
        }      
    }


    public function websiteProducts()
    {
        return $this->hasMany("App\WebsiteProduct","product_id","id");
    }

    
    public function publishedOn()
    {
        return array_keys($this->websiteProducts->pluck("product_id","store_website_id")->toArray());


    }

    /**
     * get product images from watson
     * 
     */

    public static function attachProductChat($brands = [], $category = [], $existeProducts = [])
    {
        return \App\Product::whereIn("brand", $brands)->whereIn("category", $category)
                ->whereNotIn("id", $existeProducts)
                ->join("mediables as m",function($q){
                    $q->on("m.mediable_id","products.id")->where("m.mediable_type",\App\Product::class);
                })
                ->where("stock",">",0)
                ->orderBy("created_at", "desc")
                ->limit(\App\Library\Watson\Action\SendProductImages::SENDING_LIMIT)
                ->get();
    }

    /**
    * Get price calculation
    * @return float
    **/
    public function getPrice($websiteId,$countryId = null, $countryGroup = null)
    {
        $website        = \App\StoreWebsite::find($websiteId);
        $priceRecords   = null;

        if($website) {

           $brand    = @$this->brands->brand_segment;
           $category = $this->category;
           $country  = $countryId;

           if($countryGroup == null) {
               $listOfGroups = \App\CountryGroup::join("country_group_items as cgi","cgi.country_group_id","country_groups.id")->where("cgi.country_code",$country)->first();
               if($listOfGroups) {
                  $countryGroup = $listOfGroups->country_group_id;
               }
           }

           $priceModal = \App\PriceOverride::where("store_website_id",$website->id);
           $priceCModal = clone $priceModal;

           if(!empty($brand) && !empty($category) && !empty($countryGroup))  {
              $priceRecords = $priceModal->where("country_group_id",$countryGroup)->where("brand_segment",$brand)->where("category_id",$category)->first();
           }

           if(!$priceRecords) {
              $priceModal = $priceCModal;
              $priceRecords = $priceModal->where(function($q) use($brand, $category, $countryGroup) {
                $q->orWhere(function($q) use($brand, $category) {
                    $q->where("brand_segment", $brand)->where("category_id",$category);
                })->orWhere(function($q) use($brand, $countryGroup) {
                    $q->where("brand_segment", $brand)->where("country_group_id",$countryGroup);
                })->orWhere(function($q) use($countryGroup, $category) {
                    $q->where("country_group_id", $countryGroup)->where("category_id",$category);
                });
              })->first();
           }

           if(!$priceRecords) {
              $priceModal = $priceCModal;
              $priceRecords = $priceModal->where("brand_segment",$brand)->first();
           }

           if(!$priceRecords) {
              $priceModal = $priceCModal;
              $priceRecords = $priceModal->where("category_id",$category)->first();
           }

           if(!$priceRecords) {
              $priceModal = $priceCModal;
              $priceRecords = $priceModal->where("country_group_id",$countryGroup)->first();
           }

           if($priceRecords) {
              if($priceRecords->calculated == "+") {
                 if($priceRecords->type == "PERCENTAGE")  {
                    $price = ($this->price * $priceRecords->value) / 100;
                    return ["original_price" => $this->price , "promotion" => $price , "total" =>  $this->price + $price];
                 }else{
                    return ["original_price" => $this->price , "promotion" => $priceRecords->value , "total" =>  $this->price + $priceRecords->value];
                 }
              }
              if($priceRecords->calculated == "-") {
                 if($priceRecords->type == "PERCENTAGE")  {
                    $price = ($this->price * $priceRecords->value) / 100;
                    return ["original_price" => $this->price , "promotion" => -$price , "total" =>  $this->price - $price];
                 }else{
                    return ["original_price" => $this->price , "promotion" => - $priceRecords->value , "total" =>  $this->price - $priceRecords->value];
                 }
              }
           }
        }

        return ["original_price" => $this->price , "promotion" => "0.00", "total" =>  $this->price];
    }

    public function getDuty($countryCode)
    {
       $hsCode = ($this->product_category) ? $this->product_category->simplyduty_code : null;
       if(!empty($hsCode)){
            $duty = \App\CountryDuty::leftJoin("duty_groups as dg","dg.id","country_duties.duty_group_id")
            ->where("country_duties.hs_code",$hsCode)
            ->where("country_duties.destination",$countryCode)
            ->select(["country_duties.*","dg.id as has_group","dg.duty as group_duty","dg.vat as group_vat"])
            ->first();

            if($duty) {
                if($duty->has_group != null) {
                    return $duty->group_duty + $duty->group_vat;
                }else{
                    return $duty->duty_percentage + $duty->vat_percentage;
                }
            }
       }
        
        return (float)"0.00";

    }

    public function storeWebsiteProductAttributes($storeId = 0)
    {
        return \App\StoreWebsiteProductAttribute::where("product_id", $this->id)->where("store_website_id",$storeId)->first();
    }

    public function checkExternalScraperNeed()
    {
        if(empty($this->name) || $this->name == ".." || empty($this->short_description) || empty($this->price)) {
            $this->status_id = StatusHelper::$requestForExternalScraper;
            $this->save();
        }else{
            // if validation pass and status is still external scraper then remove and put for the auto crop
            if($this->status_id == StatusHelper::$requestForExternalScraper) {
               $this->status_id =  StatusHelper::$autoCrop;
               $this->save();
            }
        }
    }

    public function landingPageProduct()
    {
        return $this->hasOne('App\LandingPageProduct','product_id','id');
    }

    /**
    * This is using for ingoring the product for next step
    * like due to problem in crop we are not sending white product on approval
    *
    */
    public function isNeedToIgnore()
    {
        if(strtolower($this->color) == "white") {
            $this->status_id = \App\Helpers\StatusHelper::$scrape;
            $this->save();
        }
    }

    public function getStoreBrand($storeId)
    {
        $platformId = 0;

        $brand = $this->brands;
        if($brand) {
            $storeWebsiteBrand = \App\StoreWebsiteBrand::where("brand_id",$brand->id)->where("store_website_id",$storeId)->first();
            if($storeWebsiteBrand) {
                $platformId = $storeWebsiteBrand->magento_value;
            }
        }

        return $platformId;
    }

    public function getStatusName()
    {
        return @\App\Helpers\StatusHelper::getStatus()[$this->status_id];
    }
}
