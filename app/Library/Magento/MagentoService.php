<?php

namespace App\Library\Magento;

use App\Category;
use App\CharityCountry;
use App\CustomerCharity;
use App\GoogleTranslate;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Product;
use App\ProductPushErrorLog;
use App\ProductReference;
use App\Product_translation;
use App\StoreWebsite;
use App\StoreWebsiteAttributes;
use App\Supplier;
use Carbon\Carbon;
use App\StoreWebsiteSalesPrice;
use Illuminate\Support\Facades\Log;

/**
 * Get Magento service request
 *
 *
 */
class MagentoService
{
    public $product;
    public $storeWebsite;
    public $log;
    public $categories;
    public $brand;
    public $token;
    public $category;
    public $websiteIds;
    public $websiteAttributes;
    public $translations;
    public $prices;
    public $totalRequest;
    public $sizes;
    public $meta;
    public $md;
    public $sku;
    public $description;
    public $magentoBrand;
    public $images;
    public $storeWebsiteSize;
    public $storeWebsiteColor;
    public $measurement;
    public $estMinimumDays;
    public $sizeChart;
    public $storeColor;
    public $productType;
    public $imageIds;
    public $languagecode    = [];
    public $aclanguagecode  = [];
    public $activeLanguages = [];
    public $charity ;

    const SKU_SEPERATOR = "-";

    public function __construct(Product $product, StoreWebsite $storeWebsite, $log = null)
    {
        $this->product      = $product;
        $this->storeWebsite = $storeWebsite;
        $this->log          = $log;
        $this->charity=0;
        $p                     = \App\CustomerCharity::where('product_id', $this->product->id)->first();
        if ($p)
          $this->charity=1;
    }

    public function pushProduct()
    {
        // start to send request if there is token

        

        if (!$this->validateToken()) {
            return false;
        }
        
        // started to check for the category
        if ($this->charity==0 && !$this->validateCategory()) {
            return false;
        }
       
        // started to check the product rediness test
         if (!$this->validateReadiness()) {
        return false;
        }

        if (!$this->validateBrand()) {
            return false;
        }

        if (!$this->validateProductCategory()) {
            return false;
        }

        // assign reference
        
        $this->assignReference();

        return $this->assignOperation();
    }

    private function assignOperation()
    {
       
        //assign all default datas so we can use on calculation
        \Log::info($this->product->id . " #1 => " . date("Y-m-d H:i:s"));
        $this->websiteIds = $this->getWebsiteIds();
        \Log::info($this->product->id . " #2 => " . date("Y-m-d H:i:s"));
        $this->websiteAttributes = $this->getWebsiteAttributes();
        \Log::info($this->product->id . " #3 => " . date("Y-m-d H:i:s"));
        // start for translation
        
        $this->startTranslation();
        

        \Log::info($this->product->id . " #4 => " . date("Y-m-d H:i:s"));
        $this->meta = $this->getMeta();
        \Log::info($this->product->id . " #5 => " . date("Y-m-d H:i:s"));

        $this->translations = $this->getTranslations();
		if(!$this->translations) {
            $this->storeLog("translation_not_found", "No translations found for the product total translation " . count($this->translations), null, null);
			 return false;
		}
        // after the translation that validate translation from her
        $this->activeLanguages = $this->getActiveLanguages();
        if (!$this->validateTranslation()) {
            return false;
        }

        \Log::info($this->product->id . " #6 => " . date("Y-m-d H:i:s"));

        $this->totalRequest += count($this->translations);

        \Log::info($this->product->id . " #7 => " . date("Y-m-d H:i:s"));
        $this->sizes = $this->getSizes();
        \Log::info($this->product->id . " #8 => " . date("Y-m-d H:i:s"));
        $this->sku = $this->getSku();
        \Log::info($this->product->id . " #9 => " . date("Y-m-d H:i:s"));

        $this->description = $this->getDescription();
        \Log::info($this->product->id . " #10 => " . date("Y-m-d H:i:s"));

        $this->magentoBrand = $this->getMagentoBrand();

        \Log::info($this->product->id . " #11 => " . date("Y-m-d H:i:s"));
        $this->images = $this->getImages();
        \Log::info($this->product->id . " #12 => " . date("Y-m-d H:i:s"));
        $this->storeWebsiteSize = $this->storeWebsiteSize();
        \Log::info($this->product->id . " #13 => " . date("Y-m-d H:i:s"));
        $this->storeWebsiteColor = $this->storeWebsiteColor();
        \Log::info($this->product->id . " #14 => " . date("Y-m-d H:i:s"));
        $this->measurement = $this->getMeasurements();
        \Log::info($this->product->id . " #15 => " . date("Y-m-d H:i:s"));
        $this->estMinimumDays = $this->getEstimateMinimumDays();
        \Log::info($this->product->id . " #16 => " . date("Y-m-d H:i:s"));
        $this->sizeChart = $this->getSizeChart();
        \Log::info($this->product->id . " #17 => " . date("Y-m-d H:i:s"));
        $this->storeColor = $this->getStoreColor();
        \Log::info($this->product->id . " #18 => " . date("Y-m-d H:i:s"));

        // get normal and special prices
        
        $this->getPricing();

        

        \Log::info($this->product->id . " #19 => " . date("Y-m-d H:i:s"));
        return $this->assignProductOperation();

    }

    private function getActiveLanguages()
    {
        return \App\Language::where("status", 1)->pluck("code", "code")->toArray();
    }

    private function getStoreColor()
    {
        $color = preg_replace("/\s+/", " ", $this->product->color);
        if (!empty($color)) {
            $colorFromSite = \App\StoreWebsiteColor::where("erp_color", trim($color))
                ->where("store_website_id", $this->storeWebsite->id)
                ->select(["platform_id"])
                ->first();

            if ($colorFromSite) {
                return $colorFromSite->platform_id;
            }
        }

        return false;
    }

    private function getSizeChart()
    {
        $categorym = $this->category;
        if ($categorym) {
            $categoryparent = $categorym->parent;
            if ($categoryparent && $categoryparent->size_chart_needed == 1) {
                return $categoryparent->getSizeChart($this->storeWebsite->id);
            }

            if ($categorym && $categorym->size_chart_needed == 1) {
                return $categorym->getSizeChart($this->storeWebsite->id);
            }
        }

        return false;
    }

    private function getEstimateMinimumDays()
    {
        $estimated_minimum_days = 0;
        $supplier               = Supplier::join('product_suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
            ->where('product_suppliers.product_id', $this->product->id)
            ->select('suppliers.*')
            ->first();
        if ($supplier) {
            $estimated_minimum_days = is_numeric($supplier->est_delivery_time) ? $supplier->est_delivery_time : 0;
        }

        return $estimated_minimum_days;
    }

    private function getMeasurements()
    {
        return ProductHelper::getMeasurements($this->product);
    }

    private function storeWebsiteSize()
    {
        $arrSizes = $this->sizes;

        $arsizes = [];

        foreach ($arrSizes as $arSize) {
            $e         = preg_replace("/\s+/", " ", $arSize);
            $arsizes[] = trim($e);
        }

        $sizeFromSite = \App\Size::join("store_website_sizes as sws", "sws.size_id", "sizes.id")
            ->whereIn("sizes.name", $arsizes)
            ->where("sws.store_website_id", $this->storeWebsite->id)
            ->pluck("sws.platform_id", "sizes.name")
            ->toArray();

        return $sizeFromSite;
    }

    private function storeWebsiteColor()
    {
        $colorFromSite = \App\StoreWebsiteColor::where("store_website_id", $this->storeWebsite->id)
            ->pluck("erp_color", "platform_id")
            ->toArray();

        return $colorFromSite;
    }

    private function getImages()
    {
        return $this->product->getImages("gallery_" . $this->storeWebsite->cropper_color);
    }

    private function getMagentoBrand()
    {
        return $this->product->getStoreBrand($this->storeWebsite->id);
    }

    private function getDescription()
    {
        $description = $this->product->setRandomDescription($this->storeWebsite);

        // assign description game wise
        $storeWebsiteAttributes = $this->product->storeWebsiteProductAttributes($this->storeWebsite->id);
        if ($storeWebsiteAttributes) {
            $description = $storeWebsiteAttributes->description;
        }

        return $description;
    }

    private function getSku()
    {
        $sku = $this->product->sku . self::SKU_SEPERATOR . $this->product->color;
        $sku = rtrim($sku, self::SKU_SEPERATOR);

        return $sku;
    }

    private function assignProductOperation()
    {
        $product  = $this->product;
        $brand    = $this->brand;
        $category = $this->category;
        $website  = $this->storeWebsite;
        $meta     = $this->meta;
        $token    = $this->token;

        // start operation for simple or configurable
        $mainCategory = $this->category;

        $pushSingle = false;



        if ($mainCategory->push_type == 0 && !is_null($mainCategory->push_type)) {
            \Log::info("Product push type single via category");
            \Log::info($this->product->id . " #20 => " . date("Y-m-d H:i:s"));
            $pushSingle = true;
        } else if ($mainCategory->push_type == 1) {
            \Log::info("Product push type configurable via category");
            \Log::info($this->product->id . " #20 => " . date("Y-m-d H:i:s"));
            $pushSingle = false;
        } else {
            \Log::info("Product push type else condition via category");
            if (!empty($this->sizes) && count($this->sizes) > 1) {
                \Log::info($this->product->id . " #20 => " . date("Y-m-d H:i:s"));
                $pushSingle = false;

            } else {
                if ($this->product->size_eu == 'OS') {
                    $product->size_eu = null;
                }
                \Log::info($this->product->id . " #20 => " . date("Y-m-d H:i:s"));
                $pushSingle = true;
            }
        }
        
        if ($pushSingle) {
              $totalRequest = 1 + count($this->prices['samePrice']) + count($this->prices['specialPrice']) + count($this->translations);
            if ($this->log) {
                $this->log->total_request_assigned = $totalRequest;
                $this->log->save();
            }
            $result = $this->_pushSingleProduct();
        } else {

            $totalRequest = ((count($this->prices['samePrice']) + count($this->prices['specialPrice']) + count($this->translations) + 1) * (1 + count($this->storeWebsiteSize)));
            if ($this->log) {
                $this->log->total_request_assigned = $totalRequest;
                $this->log->save();
            }
            $result = $this->_pushConfigurableProductWithChildren();
        }
       
        // started to check that request issue
        $platform_id=0;
        if (isset($result->id))
        {
            $platform_id=$result->id;
            $sp=\App\StoreWebsiteProduct::where('product_id',$this->product->id)
              ->where('store_website_id',$this->storeWebsite->id)->first();
            if ($sp)
            {
                  $sp->platform_id=$platform_id;
                  $sp->updated_at=date("Y-m-d H:i:s");
                  $sp->save();

            }
            else
            {
                $data['product_id']=$this->product->id;
                $data['store_website_id']=$this->storeWebsite->id;
                $data['platform_id']=$platform_id;
                $data['created_at']=date("Y-m-d H:i:s");
                \App\StoreWebsiteProduct::insert($data);
            }
            
        }
            

       
       
        
        if ($this->log) {
            $totalReq     = $this->log->total_request_assigned;
            $totalSuccess = \App\ProductPushErrorLog::where('log_list_magento_id', $this->log->id)->where('response_status', 'success')->count();
            if ($totalSuccess < $totalReq) {
                $this->log->magento_status = "error";
                $this->log->message        = "Product has been failed to push as total request is not matching with current request";
                $this->log->save();
            } else {
               
                $this->pushdiscountprice();
                $this->product->status_id        = StatusHelper::$inMagento;
                $this->product->isUploaded       = 1;
                $this->product->is_uploaded_date = Carbon::now();
                $this->product->isListed         = 1;
                $this->product->save();
                
                

                $this->log->languages = json_encode($this->languagecode);
                $this->log->save();
            }
        }

        \Log::info($this->product->id . " #21 => " . date("Y-m-d H:i:s"));
    }

    private function defaultData($data)
    {
        /*update product name code starts*/
        $productNamelength = strlen($this->product->name);
        if ($productNamelength < 50) {
            if (isset($this->product->brands->name) and $this->product->brands->name != null) {
                $brandName = $this->product->brands->name;
                similar_text($this->product->name, $brandName, $brandProductMatch);
                if ($brandProductMatch < 70) {
                    $this->product->name = $brandName . ' ' . $this->product->name;
                    $productNamelength   = strlen($this->product->name);
                }
            }
            if (isset($this->product->categories->title) and $this->product->categories->title != "Select Category") {
                $catName = $this->product->categories->title;
                if ($productNamelength < 50) {
                    similar_text($this->product->name, $catName, $categoryProductMatch);
                    if ($categoryProductMatch < 70) {
                        $this->product->name = $catName . ' ' . $this->product->name;
                    }
                }
            }
        }
        /*update product name code ends*/

        $e = [
            'product' => array(
                'sku'                  => $data['sku'], // Simple products to associate
                'name'                 => html_entity_decode(strtoupper($this->product->name), ENT_QUOTES, 'UTF-8'),
                'attribute_set_id'     => $data['attribute_set_id'],
                'price'                => $this->product->price,
                'status'               => $data['status'],
                'weight'               => $data['weight'],
                'type_id'              => $data['type_id'],
                'extension_attributes' => [
                    'website_ids'    => $data['website_ids'],
                    'category_links' => $this->categories,
                    'stock_item'     => $data['stock_item'],
                ],
                'custom_attributes'    => [
                    ['attribute_code' => 'description', 'value' => $data['description']],
                    ['attribute_code' => 'short_description', 'value' => $data['description']],
                    ['attribute_code' => 'composition', 'value' => $this->product->composition],
                    ['attribute_code' => 'material', 'value' => $this->product->color],
                    ['attribute_code' => 'tax_class_id', 'value' => $data['tax_class_id']],
                    ['attribute_code' => 'color', 'value' => $this->product->color],
                    ['attribute_code' => 'country_of_manufacture', 'value' => $this->product->made_in],
                    ['attribute_code' => 'brands', 'value' => $this->magentoBrand],
                ],
            )];

        return $e;
    }

    private function _pushProduct($productType, $sku, $data = [], $size = null)
    {
        $assku   = $sku . (!empty($size) ? '-' . $size : '');
        $product = $this->product;

        $this->productType = $productType;

        if ($productType == "simple_configurable") {
            $data['product']['visibility'] = 1;
        }

        $data['product']['sku']                  = $assku;
        $data['product']['custom_attributes'][8] = [
            'attribute_code' => 'url_key',
            'value'          => self::createURL($product->name . "-" . $assku),
        ];

        $data['product']['media_gallery_entries'] = [];
        if ($productType == "configurable" || $productType == "single") {
            $data['product']['media_gallery_entries'] = $this->images;
        }

        // add return here if not
        if ($productType != "configurable") {
            $size = preg_replace("/\s+/", " ", $size);
            if (isset($this->storeWebsiteSize[$size])) {
                $data['product']['custom_attributes'][9] = [
                    'attribute_code' => 'size_v2',
                    'value'          => $this->storeWebsiteSize[$size],
                ];
            }
        }

        $data['product']['custom_attributes'][10] = [
            'attribute_code' => 'dimensions',
            'value'          => $this->measurement,
        ];

        $data['product']['custom_attributes'][11] = [
            'attribute_code' => 'estimated_minimum_days',
            'value'          => $this->estMinimumDays,
        ];

        $catLinks = [];
        if (!empty($this->categories)) {
            foreach ($this->categories as $category) {
                $catLinks[] = ['position' => $category['position'], 'category_id' => $category['category_id']];
            }
        }
        $data['product']['extension_attributes']['category_links'] = $catLinks;
        $data['product']['custom_attributes'][12]                  = [
            'attribute_code' => 'meta_title',
            'value'          => $this->meta['meta_title'],
        ];

        $data['product']['custom_attributes'][13] = [
            'attribute_code' => 'meta_description',
            'value'          => $this->meta['meta_description'],
        ];

        $data['product']['custom_attributes'][14] = [
            'attribute_code' => 'meta_keyword',
            'value'          => $this->meta['meta_keyword'],
        ];

        if (!empty($this->sizeChart)) {
            $data['product']['custom_attributes'][22] = [
                'attribute_code' => "size_chart_url",
                'value'          => $this->sizeChart,
            ];
        }
        //add maximum days for the custom attributes
        $data['product']['custom_attributes'][] = [
            'attribute_code' => 'estimated_maximum_days',
            'value'          => $this->estMinimumDays + 7,
        ];

        if (!empty($this->storeColor)) {
            $data['product']['custom_attributes'][] = [
                'attribute_code' => 'color_v2',
                'value'          => $this->storeColor,
            ];
        }
        
        $functionResponse = $this->sendRequest($this->storeWebsite->magento_url . "/rest/V1/products/", $this->token, $data);
        
        $res              = json_decode($functionResponse['res']);
        $returnres=$res;
        

         

        // store image function has been done
        if ($functionResponse['httpcode'] == 200) {
          if ($this->charity==0 && ($this->productType == "configurable" || $this->productType == "single")) {
                if (array_key_exists('media_gallery_entries', $res) && !empty($res->media_gallery_entries)) {
                    foreach ($res->media_gallery_entries as $key => $image) {
                        $this->imageIds[] = $image->id;
                    }
                }
            }

            if (isset($res->id)) {
                if ($this->productType == "configurable") {
                    $this->sendConfigurableOptions($this->product, $res, $this->storeWebsite, $this->token);
                } else if ($productType == "simple_configurable") {
                    $this->setSimpleSingleProductToConfig($assku, $sku, $this->token, $this->storeWebsite, null, $this->product);
                }

                if (!empty($this->prices['samePrice'])) {
                    foreach ($this->prices['samePrice'] as $kp => $sp) {
                        $url     = $this->storeWebsite->magento_url . "/rest/V1/multistore/productprice/" . $data['product']['sku'];
                        $resData = [
                            "countrycode" => implode(",", $sp),
                            "prices"      => ["base_price" => number_format($kp, 2, '.', ',')],
                        ];
                        $functionResponse = $this->sendRequest($url, $this->token, $resData, "PUT");
                        $priceRes         = json_decode($functionResponse['res']);
                    }
                }

                if (!empty($this->prices['specialPrice'])) {
                    foreach ($this->prices['specialPrice'] as $kp => $sp) {
                        $url     = $this->storeWebsite->magento_url . "/rest/V1/multistore/productprice/" . $data['product']['sku'];
                        $resData = [
                            "countrycode" => implode(",", $sp),
                            "prices"      => ["base_price" => number_format($kp, 2, '.', ',')],
                        ];
                        $functionResponse = $this->sendRequest($url, $this->token, $resData, "PUT");
                        $priceRes         = json_decode($functionResponse['res']);
                    }
                }

                //startTranslation
                if (!empty($this->translations)) {
                    $extrarequest = [];
                    foreach ($this->translations as $t => $translation) {
                        $extrarequest['product']['name']                 = $translation['title'];
                        $extrarequest['product']['custom_attributes'][0] = [
                            'attribute_code' => 'description',
                            'value'          => $translation['description'],
                        ];

                        $extrarequest['product']['custom_attributes'][1] = ['attribute_code' => 'short_description', 'value' => $translation['short_description']];

                        if (!empty($translation['composition'])) {
                            $extrarequest['product']['custom_attributes'][2] = ['attribute_code' => 'composition', 'value' => $translation['composition']];
                        }

                        if (!empty($translation['color'])) {
                            $extrarequest['product']['custom_attributes'][5] = ['attribute_code' => 'color', 'value' => $translation['color']];
                        }

                        if (!empty($translation['country_of_manufacture'])) {
                            $extrarequest['product']['custom_attributes'][6] = ['attribute_code' => 'country_of_manufacture', 'value' => $translation['country_of_manufacture']];
                        }

                        if (!empty($translation['dimension'])) {
                            $extrarequest['product']['custom_attributes'][10] = ['attribute_code' => 'dimensions', 'value' => $translation['dimension']];
                        }

                        $extrarequest['product']['custom_attributes'][12] = ['attribute_code' => 'meta_title', 'value' => $translation['meta_title']];
                        $extrarequest['product']['custom_attributes'][13] = ['attribute_code' => 'meta_description', 'value' => $translation['meta_description']];
                        $extrarequest['product']['custom_attributes'][14] = ['attribute_code' => 'meta_keyword', 'value' => $translation['meta_keyword']];

                        $extrarequest['storecode'] = $translation['store_codes'];

                        $url = $this->storeWebsite->magento_url . "/rest/V1/multistore/storeproducts/" . $data['product']['sku'];

                        $functionResponse = $this->sendRequest($url, $this->token, $extrarequest, "PUT");

                        $res      = $functionResponse['res'];
                        $result[] = $res;
                        $httpcode = $functionResponse['httpcode'];

                        if ($httpcode == 200) {
                            $this->languagecode[] = $t;
                        }

                        $res = json_decode($functionResponse['res']);
                    }
                }

            }
        }
        return $returnres;

    }

    private static function setSimpleSingleProductToConfig($childSku, $sku, $token, $website, $storeView = null, $product = null)
    {

        $data = ['childSku' => $childSku];
        $data = json_encode($data);
        if (empty($storeView)) {
            $url = $website->magento_url . '/rest/V1/configurable-products/' . $sku . '/child';
        } else {
            $url = $website->magento_url . '/rest/' . $storeView . '/V1/configurable-products/' . $sku . '/child';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'accept: application/json', 'Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $err    = curl_error($ch);
        \Log::channel('listMagento')->info(json_encode([$url, $token, $data, $result, "setSimpleProductToConfig"]));
        $response = json_decode($result);

        \Log::info(print_r([$url, $token, $data, $result], true));
    }

    private function sendConfigurableOptions($product, $res, $website, $token, $store = null)
    {
        $product = $this->product;
        $website = $this->storeWebsite;

        $request           = [];
        $request["option"] = [
            "attribute_id"   => $this->websiteAttributes['size_v2'],
            "label"          => "Size",
            "position"       => 0,
            "is_use_default" => true,
        ];

        if (!empty($this->storeWebsiteSize)) {
            foreach ($this->storeWebsiteSize as $sizefrom) {
                $request["option"]["values"][] = [
                    "value_index" => $sizefrom,
                ];
            }
        }

        $data = json_encode($request);

        if ($store) {
            $url = $this->storeWebsite->magento_url . "/rest/" . trim($store) . "/V1/configurable-products/" . $res->sku . "/options";
        } else {
            $url = $this->storeWebsite->magento_url . "/rest/V1/configurable-products/" . $res->sku . "/options";
        }

        $result = false;

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'accept: application/json', 'Authorization: Bearer ' . $this->token));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result   = curl_exec($ch);
            $err      = curl_error($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            \Log::info(print_r([$url, $token, $data, $result], true));
            if ($httpcode != 200) {
                if ($this->log) {
                    $this->log->message     = "Product push to magento failed for product ID " . $product->id . ' messaage : ' . $result;
                    $this->log->sync_status = "error";
                    $this->log->save();
                } else {
                    $this->log = LogListMagento::log($product->id, "Product push to magento failed for product ID " . $product->id . ' messaage : ' . $result, 'emergency', $website->id, 'error');
                }
                ProductPushErrorLog::log($url, $product->id, "Product push to magento failed for product ID " . $product->id . ' messaage : ' . $result, 'error', $website->id, $data, $err, $this->log->id);
            }
        } catch (\SoapFault $e) {
            // Log alert
            \Log::error($e);
            Log::channel('listMagento')->alert("option for product " . $product->id . " with failed while pushing to Magento with message: " . $e->getMessage());
        }

        return $result;
    }

    private function sendRequest($url, $token, $productData, $type = "POST")
    {

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'accept: application/json', 'Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));

        $res = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpcode != 200) {
            if ($this->log) {
                $this->log->message     = $res;
                $this->log->sync_status = "error";
                $this->log->save();
            } else {
                $this->log = LogListMagento::log($this->product->id, $res, 'info', $this->storeWebsite->id, 'error');
            }
            unset($productData['product']['media_gallery_entries']);
            ProductPushErrorLog::log($url, $this->product->id, $res, 'error', $this->storeWebsite->id, $productData, json_decode($res), $this->log->id);
        } else {
            if ($this->log) {
                $this->log->message = "Product (" . $this->productType . ") with SKU " . $this->sku . " successfully pushed to Magento";
                if (!empty($this->sizeChart)) {
                    $this->log->size_chart_url = $this->sizeChart;
                }
                $this->log->sync_status = "success";
                $this->log->save();
            } else {
                $this->log = LogListMagento::log($this->product->id, "Product (" . $this->productType . ") with SKU " . $this->sku . " successfully pushed to Magento", 'info', $this->storeWebsite->id, 'success');
            }
            unset($productData['product']['media_gallery_entries']);
            ProductPushErrorLog::log($url, $this->product->id, "Product (" . $this->productType . ") with SKU " . $this->sku . " successfully pushed to Magento", 'success', $this->storeWebsite->id, $productData, $res, $this->log->id);
        }

        return ["res" => $res, "httpcode" => $httpcode];
    }

    private function _pushSingleProduct()
    {
        $d                     = [];
        $d['sku']              = $this->sku;
        $d['weight']           = 0;
        $d['attribute_set_id'] = 4;
        $d['status']           = 1;
        $d['type_id']          = 'simple';
        $p                     = \App\CustomerCharity::where('product_id', $this->product->id)->first();
        if ($p) {
            $d['type_id'] = 'donation';
        }

        $d['website_ids'] = $this->websiteIds;
        $d['stock_item']  = [
            'use_config_manage_stock' => 1,
            'manage_stock'            => 1,
            'qty'                     => 1,
            'is_in_stock'             => 1,
        ];
        $d['description']  = $this->description;
        $d['tax_class_id'] = 2;
       
        
        $data = $this->defaultData($d);
        
        $result = $this->_pushProduct('single', $this->sku, $data, '', $this->storeWebsite, $this->token, $this->product);
        // Return result
        
        return $result;
    }

    private function _pushConfigurableProductWithChildren()
    {

        // Get all the sizes
        $product  = $this->product;
        $website  = $this->storeWebsite;
        $arrSizes = explode(',', $product->size_eu);

        $data                     = [];
        $data['sku']              = $this->sku;
        $data['attribute_set_id'] = $this->websiteAttributes['attribute_set_id'];
        $data['status']           = 1;
        $data['weight']           = 0;
        $data['type_id']          = 'configurable';
        $data['website_ids']      = $this->websiteIds;
        $data['stock_item']       = [
            'use_config_manage_stock' => 1,
            'manage_stock'            => 1,
            'qty'                     => 1,
            'is_in_stock'             => 1,
        ];
        $data['description']  = $this->description;
        $data['tax_class_id'] = 2;
        $data['color']        = 0;
        // Set product data for Magento

        $datarequest = $this->defaultData($data);

        // Get result
        $result = $this->_pushProduct('configurable', $this->sku, $datarequest, '', $this->storeWebsite, $this->token, $this->product);
        // remove here

        // Loop over each size and create a single (child) product
        if (!empty($arrSizes)) {
            foreach ($arrSizes as $size) {
                // Create a new product reference for this size
                $reference             = new ProductReference;
                $reference->product_id = $product->id;
                $reference->sku        = $product->sku;
                $reference->color      = $product->color;
                $reference->size       = $size;
                $reference->save();

                $attributeSetid = $this->websiteAttributes['attribute_set_id'];

                $data['type_id']          = 'simple';
                $data['attribute_set_id'] = $attributeSetid;
                $productData              = $this->defaultData($data);
                // Push simple product to Magento
                $result = $this->_pushProduct('simple_configurable', $this->sku, $productData, $size, $website, $this->token, $product);
            }

        }

        return $result;

    }

    private function getMeta()
    {
        $product  = $this->product;
        $brand    = $this->brand;
        $category = $this->category;
        $website  = $this->storeWebsite;

        $meta                = [];
        $meta['description'] = 'Shop ' . $brand->name . ' ' . $product->color . ' .. ' . $product->composition . ' ... ' . $category->title . ' Largest collection of luxury products in the world from ' . ucwords($website->title) . ' at special prices';

        $categories     = $this->categories;
        $catLinks       = [];
        $metakeywordarr = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $catnamearray = Category::find($category['org_id']);
                if ($catnamearray) {
                    $metakeywordarr[] = $catnamearray->title;
                }
            }
        }

        $metakeywords = implode(',', $metakeywordarr);

        $seoFormat      = \App\StoreWebsiteSeoFormat::where("store_website_id", $this->storeWebsite->id)->first();
        $seoTitle       = $product->name . ' | ' . $brand->name;
        $seoDescription = $this->description;
        $seoKeywords    = ($metakeywords != '') ? $metakeywords . ',' . $this->storeWebsite->title : $this->storeWebsite->title;
        if ($seoFormat) {
            //$metaTitle = $seoFormat->meta_title;
            @eval("\$dbseoTitle = \"$seoFormat->meta_title\";");
            if (!empty($dbseoTitle)) {
                $seoTitle = $dbseoTitle;
            }

            @eval("\$dbseoDescription = \"$seoFormat->meta_description\";");
            if (!empty($dbseoDescription)) {
                $seoDescription = $dbseoDescription;
            }

            @eval("\$dbseoKeyword = \"$seoFormat->meta_keyword\";");
            if (!empty($dbseoKeyword)) {
                $seoKeywords = $dbseoKeyword;
            }
        }

        $meta['meta_title']       = $seoTitle;
        $meta['meta_description'] = $seoDescription;
        $meta['meta_keyword']     = $seoKeywords;

        return $meta;
    }

    private function getSizes()
    {
        return explode(",", $this->product->size_eu);
    }

    private function getPricing()
    {
        $website = $this->storeWebsite;
        $id      = $this->product->id;
        $p       = \App\CustomerCharity::where('product_id', $id)->first();
        if ($p) {
            $webStores = \App\CharityProductStoreWebsite::join('websites', 'charity_product_store_websites.website_id', 'websites.id')->where('charity_id', $p->id)->get();
        } else {
            $webStores = \App\Website::where("store_website_id", $website->id)->get();
        }

        $product   = $this->product;
        $pricesArr = [];
        if (!$webStores->isEmpty()) {
            foreach ($webStores as $key => $webStore) {

                if ($p) {
                    $countries    = CharityCountry::where('charity_id', $p->id)->get();
                    $magentoPrice = round($webStore->price, -1 * (strlen($webStore->price) - 1), PHP_ROUND_HALF_UP);
                    $price        = $magentoPrice;
                    $specialPrice = 0;
                    $totalAmount  = 0;

                    foreach ($countries as $c) {

                        if (isset($c->price) && $c->price > 0) {
                            $price = round($c->price, -1 * (strlen($c->price) - 1), PHP_ROUND_HALF_UP);

                        }

                        $pricesArr[$c->country_code] = [
                            "price"         => $price,
                            "special_price" => $specialPrice,
                        ];

                    }
                } else {
                    if ($webStore->is_price_ovveride || 1 == 1) {
                        $countries = !empty($webStore->countries) ? json_decode($webStore->countries) : [];
                        $dutyPrice = 0;
                        if (!empty($countries)) {
                            foreach ($countries as $cnt) {
                                $dutyPrice = $product->getDuty($cnt);
                                if ($dutyPrice > 0) {
                                    break;
                                }
                            }
                        }
                        // pricing check for the discount case
                        $ovverridePrice = 0;$segmentDiscount=0;
                        if (!empty($countries)) {
                            foreach ($countries as $cnt) {
                                $discountPrice = $product->getPrice($website, $cnt, null, true, $dutyPrice);
                                if (!empty($discountPrice['total']) && $discountPrice['total'] > 0) {
                                    $ovverridePrice = $discountPrice['total'];
                                    $segmentDiscount = $discountPrice['segment_discount'];
                                    break;
                                }
                            }
                        }

                        $magentoPrice = \App\Product::getIvaPrice($product->price);
                        if ($magentoPrice > 0) {
                            $totalAmount  = $magentoPrice * $dutyPrice / 100;
                            $magentoPrice = $magentoPrice + $totalAmount;
                        }
                        $specialPrice = 0;
                        if ($magentoPrice > $ovverridePrice) {
                            $price        = $magentoPrice;
                            $specialPrice = $ovverridePrice;
                        } else {
                            $price = $magentoPrice;
                        }

                    }

                    foreach ($countries as $c) {

                        $pricesArr[$c] = [
                            "price"         => $price,
                            "special_price" => $specialPrice,
                        ];

                    }

                    $d = \App\StoreWebsiteProductPrice::where('product_id', $product->id)->where('web_store_id', $webStore->id)->where('store_website_id', $website->id)->first();
                    if ($d) {
                        $d->default_price  = $magentoPrice;
                        $d->duty_price     = $dutyPrice;
                        $d->override_price = $ovverridePrice;
                        $d->segment_discount = $segmentDiscount;

                        $d->save();
                    } else {
                        $data = [
                            'product_id'       => $product->id,
                            'default_price'    => $magentoPrice,
                            'segment_discount' => $segmentDiscount,
                            'duty_price'       => $dutyPrice,
                            'override_price'   => $ovverridePrice,
                            'status'           => '1',
                            'web_store_id'     => $webStore->id,
                            'store_website_id' => $website->id,

                        ];
                        \App\StoreWebsiteProductPrice::insert($data);

                    }

                }
            }
        }
        Log::info("pricesArr " . json_encode($pricesArr));

        // start to matching price fix
        $samePrice    = [];
        $specialPrice = [];
        if (!empty($pricesArr)) {
            foreach ($pricesArr as $k => $pa) {
                if ($pa['special_price'] > 0) {
                    $specialPrice[$pa['special_price']][] = strtolower($k);
                } else {
                    $samePrice[$pa['price']][] = strtolower($k);
                }
            }
        }

        $this->prices['samePrice'] = $samePrice;
        $this->totalRequest += count($samePrice);
        $this->prices['specialPrice'] = $specialPrice;
        $this->totalRequest += count($specialPrice);

    }

    private function getTranslations()
    {
        $translations = Product_translation::join("languages as l", function ($q) {
            $q->on("l.locale", "product_translations.locale")->where("l.status", "=", 1);
        })->join("website_store_views as wsv", "wsv.name", "l.name")
            ->join("website_stores as ws", "wsv.website_store_id", "ws.id")
            ->join("websites as w", "w.id", "ws.website_id")
            ->where("product_translations.product_id", $this->product->id)
            ->where("wsv.platform_id", ">", 0)
            ->where('w.store_website_id', $this->storeWebsite->id)
            ->where('l.locale', "!=", "en")
            ->where('product_translations.title', "!=", "")
            ->where('product_translations.description', "!=", "")
            ->where('product_translations.composition', "!=", "")
            ->where('product_translations.color', "!=", "")
            ->where('product_translations.size', "!=", "")
            ->where('product_translations.country_of_manufacture', "!=", "")
            ->where('product_translations.dimension', "!=", "")
            ->groupBy("l.locale")
            ->select(["product_translations.*", "l.locale", "l.name as local_name", \DB::raw("group_concat(wsv.code) as store_codes")])
            ->get();

        $tdata = [];
        if (!$translations->isEmpty()) {
            foreach ($translations as $translation) {
                if (empty($translation->local_name)) {
                    continue;
                }

                $this->aclanguagecode[] = $translation->locale;

                $translatetSeoTitle = \App\Http\Controllers\GoogleTranslateController::translateProducts(
                    new GoogleTranslate(),
                    $translation->locale,
                    [$this->meta["meta_title"]],
                    ','
                );

                $translatetSeoDescription = \App\Http\Controllers\GoogleTranslateController::translateProducts(
                    new GoogleTranslate(),
                    $translation->locale,
                    [$this->meta["meta_description"]],
                    ','
                );

                $translatetSeoKeywords = \App\Http\Controllers\GoogleTranslateController::translateProducts(
                    new GoogleTranslate(),
                    $translation->locale,
                    [$this->meta["meta_keyword"]],
                    ','
                );

                $tdata[$translation->locale] = [
                    "title"                  => $translation->title,
                    "description"            => $translation->description,
                    "short_description"      => $translation->description,
                    "composition"            => $translation->composition,
                    "color"                  => $translation->color,
                    "country_of_manufacture" => $translation->country_of_manufacture,
                    "dimensions"             => $translation->dimension,
                    "meta_title"             => !empty($translatetSeoTitle) ? $translatetSeoTitle : $this->meta["meta_title"],
                    "meta_description"       => !empty($translatetSeoDescription) ? $translatetSeoDescription : $this->meta["meta_description"],
                    "meta_keyword"           => !empty($translatetSeoKeywords) ? $translatetSeoKeywords : $this->meta["meta_keyword"],
                    "store_codes"            => $translation->store_codes,
                ];
            }
        }

        return $tdata;
    }

    private function startTranslation()
    {
        \App\Http\Controllers\GoogleTranslateController::translateProductDetails($this->product, $this->log->id);
	}

    private function getWebsiteAttributes()
    {
        return StoreWebsiteAttributes::where("store_website_id", $this->storeWebsite->id)->pluck('attribute_val', 'attribute_key')->toArray();
    }

    private function getWebsiteIds()
    {
        $id = $this->product->id;
        $p  = \App\CustomerCharity::where('product_id', $id)->first();
        if ($p) {
            return \App\CharityProductStoreWebsite::join('websites', 'charity_product_store_websites.website_id', 'websites.id')->where('charity_id', $p->id)->where('platform_id', '>', 0)->get()->pluck('platform_id')->toArray();
        } else {
            return $this->storeWebsite->websites()->where('platform_id', '>', 0)->get()->pluck('platform_id')->toArray();
        }

    }

    private function validateTranslation()
    {
        if (count($this->activeLanguages) != count($this->translations)) {
            $this->storeLog("translation_not_found", "No translations found for the product total translation " . count($this->activeLanguages) . " and total found " . count($this->translations), null, null, [
                "languages" => json_encode($this->aclanguagecode),
            ]);
            return false;
        }
        return true;
    }

    private function validateProductCategory()
    {
        $category = $this->product->categories;

        if (empty($category)) {
            $this->storeLog("error", "Product has no category found");
        }

        $this->category = $category;

        return true;

    }

    private function validateBrand()
    {
        $brand = $this->product->brands;

        if (empty($brand->name)) {
            $this->storeLog("error", "Product has no brand found");
            return false;
        }

        $this->brand = $brand;

        return true;

    }

    private function assignReference()
    {
        $product = $this->product;
        if ($product->references) {
            $product->references()->delete();
        }

        // Create a new product reference (without sizes)
        $reference             = new ProductReference;
        $reference->product_id = $product->id;
        $reference->sku        = $product->sku;
        $reference->color      = $product->color;
        $reference->save();
    }

    private function validateReadiness()
    {
        $readiness = ProductHelper::checkReadinessForLive($this->product, $this->storeWebsite, $this->log);

        if (!$readiness) {
            $this->storeLog("error", "Product has been failed into readiness test");
            $this->changeProductStatus(StatusHelper::$manualAttribute);
            return false;
        }

        return true;
    }

    private function changeProductStatus($status)
    {
        $product            = $this->product;
        $product->status_id = $status;
        $product->save();

        return true;
    }

    private function validateCategory()
    {
        $categories = $this->getCategories();
        if (count($categories) == 0) {
            $this->storeLog("error", "Product has no categoies assigned with remote id");
            return false;
        }
        return $categories;
    }

    private function getCategories()
    {
        $this->categories = Category::getCategoryTreeMagentoWithPosition($this->product->category, $this->storeWebsite, true);
        return $this->categories;
    }

    private function hasToken()
    {
        return !empty($this->storeWebsite->api_token) ? $this->storeWebsite->api_token : false;
    }

    private function validateToken()
    {
        $token = $this->hasToken();
        if (empty($token)) {
            $this->storeLog("error", "Not able to generate token for website " . $this->storeWebsite->title);
            return false;
        } else {
            $this->token = $token;
            return $token;
        }
    }

    public function storeLog($type, $message, $request = null, $response = null, $extraFiels = [])
    {
        $product      = $this->product;
        $storeWebsite = $this->storeWebsite;

        if ($this->log) {
            $this->log->message     = $message;
            $this->log->sync_status = $type;
            if (!empty($extraFiels)) {
                foreach ($extraFiels as $k => $ext) {
                    $this->log->{$k} = $ext;
                }
            }
            $this->log->save();
        } else {
            $this->log = LogListMagento::log($product->id, $message, $type, $storeWebsite->id, $type);
        }

        ProductPushErrorLog::log(null, $product->id, $message, $type, $storeWebsite->id, null, null, $this->log->id);

        return false;
    }

    public static function createURL($string)
    {

        $string = trim($string); // Trim String
        $string = strtolower($string); //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string); //Strip any unwanted characters
        $string = preg_replace("/[\s-]+/", " ", $string); // Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s_]/", "-", $string); //Convert whitespaces and underscore to dash

        return $string;
    }

    public  function pushdiscountprice()
    {
         $product = $this->product; 
         $discount=0;
         $discount_type='amount';
         $start_date=date('Y-m-d');
         $end_date=date('Y-m-d');
         $date=date('Y-m-d');
         $supplier_id=0;
         $supplier               = Supplier::join('product_suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
         ->where('product_suppliers.product_id', $this->product->id)
         ->select('suppliers.*')
         ->first();
         if ($supplier)
             $supplier_id=$supplier->id;
         $product_discount=StoreWebsiteSalesPrice::where('type','product')
            ->where('type_id',$product->id)
            ->whereRaw('$date between date(start_date) and date(end_date)')
            ->first();
         if ($product_discount)
           {
                $discount=$product_discount->amount;
                $discount_type=$product_discount->amount_type;
                $start_date=date('Y-m-d',strtotime($product_discount->start_date));
                $end_date=date('Y-m-d',strtotime($product_discount->end_date));
           }
         else 
         {
                $storeWebsite=$this->storeWebsite;
                $product_discount1=StoreWebsiteSalesPrice::where('type','store_website')
                ->where('type_id',$storeWebsite->id)
                ->whereRaw('$date between date(start_date) and date(end_date)')
                ->first();
                if ($product_discount1)
                {
                    $discount=$product_discount1->amount;
                    $discount_type=$product_discount1->amount_type;
                    $start_date=date('Y-m-d',strtotime($product_discount1->start_date));
                    $end_date=date('Y-m-d',strtotime($product_discount1->end_date));
                }
                else
                {
                    $category=$this->$category;
                    $product_discount2=StoreWebsiteSalesPrice::where('type','category')
                    ->where('type_id',$category->id)
                    ->where('supplier_id',$supplier_id)
                    ->whereRaw('$date between date(start_date) and date(end_date)')
                    ->first();
                    if ($product_discount2)
                        {
                            $discount=$product_discount2->amount;
                            $discount_type=$product_discount2->amount_type;
                            $start_date=date('Y-m-d',strtotime($product_discount2->start_date));
                            $end_date=date('Y-m-d',strtotime($product_discount2->end_date));
                        }
                    else
                        {
                            $brand=$this->$brand;
                            $product_discount3=StoreWebsiteSalesPrice::where('type','brand')
                            ->where('type_id',$brand->id)
                            ->where('supplier_id',$supplier_id)
                            ->whereRaw('$date between date(start_date) and date(end_date)')
                            ->first();
                                if ($product_discount3)
                                {
                                    $discount=$product_discount3->amount;
                                    $discount_type=$product_discount3->amount_type;
                                    $start_date=date('Y-m-d',strtotime($product_discount3->start_date));
                                    $end_date=date('Y-m-d',strtotime($product_discount3->end_date));
                                }
                        }    
                }
         }  

         if ($discount>0)
         {
             if ($discount_type=='percentage')
                   $discount=($this->prices/100) * $discount;

                $assku   = $this->sku . (!empty($this->size) ? '-' . $this->size : '');

                $data['prices']['sku']   = $assku;
                $data['prices']['price']  = $discount;
                $data['prices']['price_from'] = $start_date;
                $data['prices']['price_to'] = $end_date;
                $data['prices']['store_id']=0;


             $functionResponse = $this->sendRequest($this->storeWebsite->magento_url . "/rest/V1/products/special-price/", $this->token, $data);
             $httpcode = $functionResponse['httpcode'];

                if ($httpcode != 200) {

                    if ($this->log) {
                        $this->log->message     = "Product Discount push to magento failed for product ID " . $product->id ;
                        $this->log->sync_status = "error";
                        $this->log->save();
                    }    
                }
                else
                {
                    if ($this->log) {
                        $this->log->message     = "Product Discount push to magento Done for product ID " . $product->id ;
                        $this->log->sync_status = "message";
                        $this->log->save();
                    }   
                }



         }

    }


}
