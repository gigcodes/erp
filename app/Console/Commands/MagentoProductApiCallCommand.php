<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Loggers\LogListMagento;
use seo2websites\MagentoHelper\MagentoHelperv2;
use App\StoreMagentoApiSearchProduct;

class MagentoProductApiCallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Magento-Product:Api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Product API Call';

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
        $produts = LogListMagento::join("products as p", "p.id", "log_list_magentos.product_id")
                                ->where("sync_status", "success")
                                ->groupBy("product_id", "store_website_id")
                                ->orderBy("log_list_magentos.id", "desc")
                                ->get();

        $languages = ['arabic', 'german', 'spanish', 'french', 'italian', 'japanese', 'korean', 'russian', 'chinese'];

        $products = array();

        $magentoHelper = new MagentoHelperv2;

        if (!$produts->isEmpty()) {
            foreach ($produts as $p) {
                $sku = $p->sku . "-" . $p->color;
                $websiteId = $p->store_website_id;

                try {
                    // $get_store_website = \App\StoreWebsite::find($websiteId);
                    $get_store_website = \App\StoreWebsite::find($websiteId);
                    $result            = $magentoHelper->getProductBySku($sku, $get_store_website);
                    
                    if (isset($result->id)) {
                        $result->success        = true;
                        $result->size_chart_url = "";
        
                        $englishDescription = "";
                        if (!empty($result->custom_attributes)) {
                            foreach ($result->custom_attributes as $attributes) {
                                if ($attributes->attribute_code == "size_chart_url") {
                                    $result->size_chart_url = $attributes->value;
                                }
                                if ($attributes->attribute_code == "description") {
                                    $englishDescription = $attributes->value;
                                    $result->english    = "Yes";
                                }
                            }
                        }
        
                        // check for all langauge request
                        foreach ($languages as $language) {
                            $firstStore = \App\Website::join("website_stores as ws", "ws.website_id", "websites.id")
                                ->join("website_store_views as wsv", "wsv.website_store_id", "ws.id")
                                ->where("websites.store_website_id", $get_store_website->id)
                                ->where("wsv.name", "like", $language)
                                ->groupBy("ws.name")
                                ->select("wsv.*")
                                ->first();
        
                            if ($firstStore) {
                                $exresult = $magentoHelper->getProductBySku($sku, $get_store_website, $firstStore->code);
                                if (isset($exresult->id)) {
        
                                    $diffrentDescription = "";
        
                                    if (!empty($exresult->custom_attributes)) {
                                        foreach ($exresult->custom_attributes as $attributes) {
                                            if ($attributes->attribute_code == "description") {
                                                $diffrentDescription = $attributes->value;
                                            }
                                        }
                                    }
        
                                    if (trim(strip_tags(strtolower($englishDescription))) != trim(strip_tags(strtolower($diffrentDescription))) && !empty($diffrentDescription)) {
                                        $result->{$language} = "Yes";
                                    } else {
                                        $result->{$language} = "No";
                                    }
                                }
                            }
                        }
                        $result->skuid            = $sku;
                        $result->store_website_id = $websiteId;
                        $products[]               = $result;
                    } else {
                        $result->success = false;
                    }
        
                } catch (\Exception $e) {
                    \Log::info("Error from LogListMagentoController 448" . $e->getMessage());
                }
            }
            if (!empty($products)) {

                $data = collect($this->processProductAPIResponce($products));
                foreach ($data as $value) {
                    $StoreMagentoApiSearchProduct = new StoreMagentoApiSearchProduct;

                        $StoreMagentoApiSearchProduct->website_id = $value['store_website_id'];
                        $StoreMagentoApiSearchProduct->website = implode(",", $value['websites']);
                        $StoreMagentoApiSearchProduct->sku = $value['sku'];
                        $StoreMagentoApiSearchProduct->size = $value['size'];
                        $StoreMagentoApiSearchProduct->brands = $value['brands'];
                        $StoreMagentoApiSearchProduct->dimensions = $value['dimensions'];
                        $StoreMagentoApiSearchProduct->composition = $value['composition'];
                        $StoreMagentoApiSearchProduct->category_names = implode(",", $value['category_names']);
                        $StoreMagentoApiSearchProduct->size_chart_url = $value['size_chart_url'];
                        $StoreMagentoApiSearchProduct->status = $value['success'] ? "Success" : "Product not found in Website.";
                        $StoreMagentoApiSearchProduct->images = !empty($value['media_gallery_entries']) ? $value['media_gallery_entries'][0] : null;
                        $StoreMagentoApiSearchProduct->english = !empty($value['english']) ? $value['english'] : "No";
                        $StoreMagentoApiSearchProduct->arabic = !empty($value['arabic']) ? $value['arabic'] : "No";
                        $StoreMagentoApiSearchProduct->german = !empty($value['german']) ? $value['german'] : "No";
                        $StoreMagentoApiSearchProduct->spanish = !empty($value['spanish']) ? $value['spanish'] : "No";
                        $StoreMagentoApiSearchProduct->french = !empty($value['french']) ? $value['french'] : "No";
                        $StoreMagentoApiSearchProduct->italian = !empty($value['italian']) ? $value['italian'] : "No";
                        $StoreMagentoApiSearchProduct->japanese = !empty($value['japanese']) ? $value['japanese'] : "No";
                        $StoreMagentoApiSearchProduct->korean = !empty($value['korean']) ? $value['korean'] : "No";
                        $StoreMagentoApiSearchProduct->russian = !empty($value['russian']) ? $value['russian'] : "No";
                        $StoreMagentoApiSearchProduct->chinese = !empty($value['chinese']) ? $value['chinese'] : "No";

                    $StoreMagentoApiSearchProduct->save();

                    if ($value["success"]) {
                        $StoreWebsiteProductCheck = \App\StoreWebsiteProductCheck::where('website_id', $value['store_website_id'])->first();
                        $addItem                  = [
                            'website_id'  => $value['store_website_id'],
                            'website'     => implode(",", $value['websites']),
                            'sku'         => $value['sku'],
                            'size'        => $value['size'],
                            'brands'      => $value['brands'],
                            'dimensions'  => $value['dimensions'],
                            'composition' => $value['composition'],
                            'english'     => !empty($value['english']) ? $value['english'] : "No",
                            'arabic'      => !empty($value['arabic']) ? $value['arabic'] : "No",
                            'german'      => !empty($value['german']) ? $value['german'] : "No",
                            'spanish'     => !empty($value['spanish']) ? $value['spanish'] : "No",
                            'french'      => !empty($value['french']) ? $value['french'] : "No",
                            'italian'     => !empty($value['italian']) ? $value['italian'] : "No",
                            'japanese'    => !empty($value['japanese']) ? $value['japanese'] : "No",
                            'korean'      => !empty($value['korean']) ? $value['korean'] : "No",
                            'russian'     => !empty($value['russian']) ? $value['russian'] : "No",
                            'chinese'     => !empty($value['chinese']) ? $value['chinese'] : "No",
                        ];

                        if ($StoreWebsiteProductCheck == null) {
                            $StoreWebsiteProductCheck = \App\StoreWebsiteProductCheck::create($addItem);
                        } else {
                            $StoreWebsiteProductCheck->where('website_id', $value['store_website_id'])->update($addItem);
                        }
                    }
                }

            }
        }

    }

    protected function processProductAPIResponce($products)
    {
        $prepared_products_data = array();
        $websites               = array();
        $category_names         = array();
        $size                   = '';
        $brands                 = '';
        $composition            = '';
        $brand                  = "";
        $dimensions             = "N/A";
        $size                   = "N/A";
        foreach ($products as $value) {
            $websites[] = \App\StoreWebsite::where('id', $value->store_website_id)->value('title');
            if (isset($value->extension_attributes)) {
                foreach ($value->extension_attributes->website_ids as $vwi) {
                    $websites[] = \App\Website::where('platform_id', $vwi)->value('name');
                }
            }

            if (isset($value->custom_attributes)) {
                foreach ($value->custom_attributes as $v) {
                    if ($v->attribute_code === "category_ids") {
                        foreach ($v->value as $key => $cat_id) {
                            $category_names[] = \App\StoreWebsiteCategory::join("categories as c", "c.id", "store_website_categories.category_id")
                                ->where('remote_id', $cat_id)
                                ->value('title');
                        }
                    }
                    if ($v->attribute_code === "size_v2" || $v->attribute_code === "size") {
                        $sizeM = \App\StoreWebsiteSize::join("sizes as s", "s.id", "store_website_sizes.size_id")->where("platform_id", $v->value)->where("store_website_id", $value->store_website_id)->select("s.*")->first();
                        if ($sizeM) {
                            $size = $sizeM->name;
                        }

                    }

                    if ($v->attribute_code === "brands") {
                        $brandsModel = \App\StoreWebsiteBrand::join("brands as b", "b.id", "store_website_brands.brand_id")
                            ->where("magento_value", $v->value)
                            ->where("store_website_id", $value->store_website_id)
                            ->select("b.*")
                            ->first();
                        if ($brandsModel) {
                            $brand = $brandsModel->name;
                        }
                    }
                    if ($v->attribute_code === "composition") {
                        $composition = $v->value;
                    }

                    if ($v->attribute_code === "dimensions") {
                        $dimensions = $v->value;
                    }

                }
            }

            $prepared_products_data[$value->sku] = [
                'store_website_id'      => $value->store_website_id,
                'magento_id'            => $value->id,
                'sku'                   => $value->sku,
                'product_name'          => $value->name,
                'media_gallery_entries' => $value->media_gallery_entries,
                'websites'              => array_filter($websites),
                'category_names'        => $category_names,
                'size'                  => $size,
                'brands'                => $brand,
                'composition'           => $composition,
                'dimensions'            => $dimensions,
                'english'               => !empty($value->english) ? $value->english : "No",
                'arabic'                => !empty($value->arabic) ? $value->arabic : "No",
                'german'                => !empty($value->german) ? $value->german : "No",
                'spanish'               => !empty($value->spanish) ? $value->spanish : "No",
                'french'                => !empty($value->french) ? $value->french : "No",
                'italian'               => !empty($value->italian) ? $value->italian : "No",
                'japanese'              => !empty($value->japanese) ? $value->japanese : "No",
                'korean'                => !empty($value->korean) ? $value->korean : "No",
                'russian'               => !empty($value->russian) ? $value->russian : "No",
                'chinese'               => !empty($value->chinese) ? $value->chinese : "No",
                'size_chart_url'        => !empty($value->size_chart_url) ? "Yes" : "No",
                'success'               => true,
            ];
            if (!$value->success) {
                $product_name = \App\Product::with('product_category', 'brands')->where('sku', $value->skuid)->first();
                if (isset($product_name) && $product_name->product_category != null) {
                    if ($product_name->product_category) {
                        $category_names[] = $product_name->product_category->title;
                    }
                }
                $brand                                 = isset($product_name->brands) ? $product_name->brands->name : "";
                $prepared_products_data[$value->skuid] = [
                    'store_website_id'      => $value->store_website_id,
                    'magento_id'            => "",
                    'sku'                   => $value->skuid,
                    'product_name'          => $product_name != null ? $product_name->name : "",
                    'media_gallery_entries' => "",
                    'websites'              => $websites,
                    'category_names'        => $category_names,
                    'size'                  => $product_name != null ? $product_name->size : "",
                    'brands'                => $brand,
                    'composition'           => $product_name != null ? $product_name->composition : "",
                    'dimensions'            => $product_name != null ? $product_name->lmeasurement . "," . $product_name->hmeasurement . "," . $product_name->dmeasurement : "",
                    'english'               => 'No',
                    'arabic'                => 'No',
                    'german'                => 'No',
                    'spanish'               => 'No',
                    'french'                => 'No',
                    'italian'               => 'No',
                    'japanese'              => 'No',
                    'korean'                => 'No',
                    'russian'               => 'No',
                    'chinese'               => 'No',
                    'size_chart_url'        => 'No',
                    'success'               => false,
                ];
            }

            $category_names = [];
            $websites       = [];
            $size           = '';
            $brands         = '';
            $composition    = '';
        }
        return $prepared_products_data;
    }
}
