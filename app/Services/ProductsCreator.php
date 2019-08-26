<?php

namespace App\Services\Products;

use App\Brand;
use App\Product;
use App\ScrapActivity;
use App\ScrapedProducts;
use App\Setting;
use App\Category;
use App\ProductStatus;
use App\Supplier;
use GuzzleHttp\Client;
use http\Env\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Writer;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Validator;
use Storage;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\Http\Controllers\ProductInventoryController;

class ProductsCreator
{

    public function setStockZero()
    {

        ScrapedProducts::whereRaw('TIMESTAMPDIFF(HOUR, updated_at, NOW()) > 48')->chunk(1000, function ($scraped_products) {
            foreach ($scraped_products as $key => $scraped_product) {

                dump('Doing Inventory...' . $key);

                $supplier = $scraped_product->website;

                switch ($supplier) {
                    case 'lidiashopping':
                        $supplier_name = 'Lidia';

                        break;
                    case 'cuccuini':
                        $supplier_name = 'Cuccini';

                        break;
                    case 'DoubleF':
                        $supplier_name = 'Double F';

                        break;
                    case 'G&B':
                        $supplier_name = 'G & B Negozionline';

                        break;
                    case 'Tory':
                        $supplier_name = 'Tory Burch';

                        break;
                    case 'Wiseboutique':
                        $supplier_name = 'Wise Boutique';

                        break;
                    case 'Divo':
                        $supplier_name = 'Divo Boutique';

                        break;
                    case 'Spinnaker':
                        $supplier_name = 'Spinnaker 101';

                        break;
                    case 'alducadaosta':
                        $supplier_name = "Al Duca d'Aosta";

                        break;
                    case 'biffi':
                        $supplier_name = "Biffi Boutique (S.P.A.)";

                        break;
                    case 'brunarosso':
                        $supplier_name = "BRUNA ROSSO";

                        break;
                    case 'carofigliojunior':
                        $supplier_name = "Carofiglio Junior";

                        break;
                    case 'italiani':
                        $supplier_name = "Italiani";

                        break;
                    case 'coltorti':
                        $supplier_name = "Coltorti";

                        break;
                    case 'griffo210':
                        $supplier_name = "Grifo210";

                        break;
                    case 'linoricci':
                        $supplier_name = "Lino Ricci Lei";

                        break;
                    case 'conceptstore':
                        $supplier_name = "Women Concept Store Cagliari";

                        break;
                    case 'deliberti':
                        $supplier_name = "Deliberti";

                        break;
                    case 'giglio':
                        $supplier_name = "Giglio Lamezia Terme";

                        break;
                    case 'laferramenta':
                        $supplier_name = "La Ferramenta";

                        break;
                    case 'les-market':
                        $supplier_name = "Les Market";

                        break;
                    case 'leam':
                        $supplier_name = "Leam";

                        break;
                    case 'mimmaninnishop':
                        $supplier_name = "Mimma Ninni Boutique";

                        break;
                    case 'montiboutique':
                        $supplier_name = "Monti";

                        break;
                    case 'nugnes1920':
                        $supplier_name = "Nugnes 1920";

                        break;
                    case 'railso':
                        $supplier_name = "Rail";

                        break;
                    case 'savannahs':
                        $supplier_name = "Savannah's";

                        break;
                    case 'tessabit':
                        $supplier_name = "Tessabit";

                        break;
                    case 'stilmoda':
                        $supplier_name = "Stilmoda";

                        break;
                    case 'tizianafausti':
                        $supplier_name = "Tiziana Fausti";

                        break;
                    case 'vinicio':
                        $supplier_name = "Vinicio";

                        break;
                    case 'mariastore':
                        $supplier_name = "MARIA STORE";

                        break;
                    case 'angelominetti':
                        $supplier_name = "MINETTI";

                        break;
                    case 'amrstore':
                        $supplier_name = "AMR Chiavari";

                        break;
                    case 'aldogibiralo':
                        $supplier_name = "Aldo Gibilaro";

                        break;
                    case 'lattudaboutique':
                        $supplier_name = "Lattuada";

                        break;
                    case 'IMPORT_VALENTI':
                        $supplier_name = "Valenti";
                        break;

                    case 'baseblu';
                        $supplier_name = 'Base Blu';
                        break;

                        break;
                    default:
                        return;
                }

                $params = [
                    'website' => $supplier,
                    'status' => 0
                ];

                $params[ 'scraped_product_id' ] = $scraped_product->id;


                if ($product = Product::where('sku', $scraped_product->sku)->first()) {
                    if ($db_supplier = Supplier::where('supplier', $supplier_name)->first()) {
                        $product->suppliers()->syncWithoutDetaching([$db_supplier->id => ['stock' => 0]]);
                    }
                }
                ScrapActivity::create($params);
            }
        });

        // Save CSV file to magento...


        $scraped_products = ScrapedProducts::take(50)->get();
        $skuForMagentoArray = [];

        foreach ($scraped_products as $key => $scraped_product) {
            dump('Again..' . $key);
            $product = Product::where('sku', $scraped_product->sku)->first();
            if (!$product) {
                continue;
            }

            $data = DB::table('product_suppliers')->where('product_id', $product->id)->selectRaw('SUM(`stock`) As count')->first();

            $skus = $this->makeSkuReadyForMagento($product);

            foreach ($skus as $sku) {
                $skuForMagentoArray[] = $sku . ',' . $data->count . "\n";
            }
        }

        try {
            echo __DIR__ . '/stock.csv';
            File::put(__DIR__ . '/stock.csv', implode('', $skuForMagentoArray));
        } catch (\Exception $exception) {
            dd($exception);
        }
        $this->sendFileToMagento();


    }

    private function sendFileToMagento()
    {
        $client = new Client();
        $filepath = __DIR__ . '/stock.csv';
        $fileContent = File::get($filepath);
        try {
            $request = $client->post('https://sololuxury.co.in/devsite/stockupdate.php', [
                'multipart' => [
                    [
                        'name' => 'stock.csv',
                        'contents' => $fileContent,
                        'filename' => 'stock.csv',
                    ],
                ],
            ]);
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    private function makeSkuReadyForMagento($product): array
    {
        $skuList = [];

        $sku = $product->sku . $product->color;

        if (!empty($product->size)) {

            $sizes_array = explode(',', $product->size);

            foreach ($sizes_array as $size) {
                $error_message = '';
                $skuList[] = $sku . '-' . $size;
            }

            $skuList[] = $sku;
        } else {
            $skuList[] = $sku;
        }


        return $skuList;

    }

    public function createProduct($image, $isExcel=0)
    {
        $properties_array = $image->properties;

        switch ($image->website) {
            case 'lidiashopping':
                $supplier = 'Lidia';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'cuccuini':
                $supplier = 'Cuccini';
                $formatted_details = $this->getCucciniDetails($properties_array);

                break;
            case 'DoubleF':
                $supplier = 'Double F';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'G&B':
                $supplier = 'G & B Negozionline';
                $formatted_details = $this->getGnbDetails($properties_array);

                break;
            case 'Tory':
                $supplier = 'Tory Burch';
                $formatted_details = $this->getToryDetails($properties_array);

                break;
            case 'Wiseboutique':
                $supplier = 'Wise Boutique';
                $formatted_details = $this->getWiseDetails($properties_array);

                break;
            case 'Divo':
                $supplier = 'Divo Boutique';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'Spinnaker':
                $supplier = 'Spinnaker 101';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'alducadaosta':
                $supplier = "Al Duca d'Aosta";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'biffi':
                $supplier = "Biffi Boutique (S.P.A.)";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'brunarosso':
                $supplier = "BRUNA ROSSO";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'italiani':
                $supplier = "Italiani";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'coltorti':
                $supplier = "Coltorti";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'griffo210':
                $supplier = "Grifo210";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'linoricci':
                $supplier = "Lino Ricci Lei";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'conceptstore':
                $supplier = "Women Concept Store Cagliari";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'deliberti':
                $supplier = "Deliberti";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'giglio':
                $supplier = "Giglio Lamezia Terme";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'laferramenta':
                $supplier = "La Ferramenta";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'les-market':
                $supplier = "Les Market";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'leam':
                $supplier = "Leam";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'mimmaninnishop':
                $supplier = "Mimma Ninni Boutique";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'montiboutique':
                $supplier = "Monti";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'nugnes1920':
                $supplier = "Nugnes 1920";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'railso':
                $supplier = "Rail";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'savannahs':
                $supplier = "Savannah's";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'tessabit':
                $supplier = "Tessabit";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'stilmoda':
                $supplier = "Stilmoda";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'tizianafausti':
                $supplier = "Tiziana Fausti";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'vinicio':
                $supplier = "Vinicio";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'mariastore':
                $supplier = "MARIA STORE";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'angelominetti':
                $supplier = "MINETTI";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'amrstore':
                $supplier = "AMR Chiavari";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'aldogibiralo':
                $supplier = "Aldo Gibilaro";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'lattudaboutique':
                $supplier = "Lattuada";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'IMPORT_VALENTI':
                $supplier = "Valenti";
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'baseblu';
                $supplier = 'Base Blu';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'luisaworld';
                $supplier = 'Luisa World';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'modes';
                $supplier = 'MODES';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'eleonorabonucci';
                $supplier = 'ELEONORA BONUCCI';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;

            case 'bernardellishop';
                $supplier = 'Bernardelli';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;

            case 'LeLunetier';
                $supplier = 'LE LUNETIER MILANO';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;

            case 'lunetier';
                $supplier = 'LE LUNETIER MILANO';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'groppetti';
                $supplier = 'Groppetti';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'antonia';
                $supplier = 'Boutique Antonia';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'liska';
                $supplier = 'Liska';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;

            case 'originators';
                $supplier = 'SHADESORIGINATORS';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;

            case 'quattro-stagioni';
                $supplier = 'Quattro Stagioni';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;

            case 'julian-fashion';
                $supplier = 'Julian Fashion';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;

            case 'maximilian';
                $supplier = 'Maxim';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            case 'Ines';
                $supplier = 'Ines';
                $formatted_details = $this->getGeneralDetails($properties_array);

                break;
            default:
                return;
        }

        $data[ 'sku' ] = (string)str_replace(' ', '', $image->sku);
        $validator = Validator::make($data, [
            'sku' => 'unique:products,sku'
        ]);

        $formatted_prices = $this->formatPrices($image);

        if ($validator->fails()) {
            $product = Product::where('sku', $data[ 'sku' ])->first();

            // Does the product exist?
            if (!$product) {
                return;
            }

            if (!$product->is_approved && !$product->is_listing_rejected) {
                // Check if we can update
                $manual = ProductStatus::where('name', 'MANUAL_TITLE')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->name = $image->title;
                }

                // Check if we can update
                $manual = ProductStatus::where('name', 'MANUAL_SHORT_DESCRIPTION')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->short_description = $image->description;
                }
            }

            if (!$product->is_approved) {
                // Check if we can update
                $manual = ProductStatus::where('name', 'MANUAL_COLOR')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->color = $image->properties[ 'color' ] ?? '';
                }

                $manual = ProductStatus::where('name', 'MANUAL_COMPOSITION')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    // Check for composition key
                    if (isset($image->properties[ 'composition' ])) {
                        $product->composition = trim($image->properties[ 'composition' ] ?? '');
                    }

                    // Check for material_used key
                    if (isset($image->properties[ 'material_used' ])) {
                        $product->composition = trim($image->properties[ 'material_used' ] ?? '');
                    }
                }
            }

            // Get current sizes
            $sizes = $product->size;

            // Update with scraped sizes
            if (is_array($image->properties[ 'sizes' ]) && count($image->properties[ 'sizes' ]) >= 1) {
                $sizes = implode(',', $image->properties[ 'sizes' ] ?? []);
            }

            // Store everything again in sizes
            $product->size = $sizes;

            // Store measurement
            $product->lmeasurement = $formatted_details[ 'lmeasurement' ] > 0 ? $formatted_details[ 'lmeasurement' ] : null;
            $product->hmeasurement = $formatted_details[ 'hmeasurement' ] > 0 ? $formatted_details[ 'hmeasurement' ] : null;
            $product->dmeasurement = $formatted_details[ 'dmeasurement' ] > 0 ? $formatted_details[ 'dmeasurement' ] : null;
            $product->price = $formatted_prices[ 'price' ];
            $product->price_inr = $formatted_prices[ 'price_inr' ];
            $product->price_special = $formatted_prices[ 'price_special' ];
            $product->is_scraped = $isExcel == 1 ? $product->is_scraped : 1;
            $product->save();

            if ($image->is_sale) {
                $product->is_on_sale = 1;
                $product->save();
            }

            if ($db_supplier = Supplier::where('supplier', $supplier)->first()) {
                if ($product) {
                    $product->suppliers()->syncWithoutDetaching([
                        $db_supplier->id => [
                            'title' => $image->title,
                            'description' => $image->description,
                            'supplier_link' => $image->url,
                            'stock' => 1,
                            'price' => $formatted_prices[ 'price' ],
                            'price_discounted' => $formatted_prices[ 'price_discounted' ],
                            'size' => $formatted_details[ 'size' ],
                            'color' => $formatted_details[ 'color' ],
                            'composition' => $formatted_details[ 'composition' ],
                            'sku' => $image->original_sku
                        ]
                    ]);
                }
            }

            $dup_count = 0;
            $supplier_prices = [];

            foreach ($product->suppliers_info as $info) {
                if ($info->price != '') {
                    $supplier_prices[] = $info->price;
                }
            }

            foreach (array_count_values($supplier_prices) as $price => $c) {
                $dup_count++;
            }

            if ($dup_count > 1) {
                $product->is_price_different = 1;
            } else {
                $product->is_price_different = 0;
            }

            $product->stock += 1;
            $product->save();

            $supplier = $image->website;

            $params = [
                'website' => $supplier,
                'scraped_product_id' => $product->id,
                'status' => 1
            ];

            ScrapActivity::create($params);

            return;

        } else {
            $product = new Product;
        }

        if ($product === null) {
            echo "SKIPPED ============================================== \n";
            return;
        }

        $product->sku = str_replace(' ', '', $image->sku);
        $product->brand = $image->brand_id;
        $product->supplier = $supplier;
        $product->name = $image->title;
        $product->short_description = $image->description;
        $product->supplier_link = $image->url;
        $product->stage = 3;
        $product->is_scraped = $isExcel == 1 ? 0 : 1;
        $product->stock = 1;
        $product->is_without_image = 1;
        $product->is_on_sale = $image->is_sale ? 1 : 0;

        $product->composition = $formatted_details[ 'composition' ];
        $product->color = $formatted_details[ 'color' ];
        $product->size = $formatted_details[ 'size' ];
        $product->lmeasurement = (int)$formatted_details[ 'lmeasurement' ];
        $product->hmeasurement = (int)$formatted_details[ 'hmeasurement' ];
        $product->dmeasurement = (int)$formatted_details[ 'dmeasurement' ];
        $product->measurement_size_type = $formatted_details[ 'measurement_size_type' ];
        $product->made_in = $formatted_details[ 'made_in' ];
        $product->category = $formatted_details[ 'category' ];

        $product->price = $formatted_prices[ 'price' ];
        $product->price_inr = $formatted_prices[ 'price_inr' ];
        $product->price_special = $formatted_prices[ 'price_special' ];

        try {
            $product->save();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            echo "Couldnt create product...";
            return;
        }

        if ($db_supplier = Supplier::where('supplier', $supplier)->first()) {
            $product->suppliers()->syncWithoutDetaching([
                $db_supplier->id => [
                    'title' => $image->title,
                    'description' => $image->description,
                    'supplier_link' => $image->url,
                    'stock' => 1,
                    'price' => $formatted_prices[ 'price' ],
                    'price_discounted' => $formatted_prices[ 'price_discounted' ],
                    'size' => $formatted_details[ 'size' ],
                    'color' => $formatted_details[ 'color' ],
                    'composition' => $formatted_details[ 'composition' ],
                    'sku' => $image->original_sku
                ]
            ]);
        }

        // $images = $image->images;
        //
        // $product->detachMediaTags(config('constants.media_tags'));
        //
        // foreach ($images as $image_name) {
        //   // Storage::disk('uploads')->delete('/social-media/' . $image_name);
        //
        //   $path = public_path('uploads') . '/social-media/' . $image_name;
        //   $media = MediaUploader::fromSource($path)->upload();
        //   $product->attachMedia($media,config('constants.media_tags'));
        // }

    }

    public function formatPrices($image)
    {
        $brand = Brand::find($image->brand_id);

        if (strpos($image->price, ',') !== false) {
            if (strpos($image->price, '.') !== false) {
                if (strpos($image->price, ',') < strpos($image->price, '.')) {
                    $final_price = str_replace(',', '', $image->price);
                } else {
                    $final_price = str_replace(',', '|', $image->price);
                    $final_price = str_replace('.', ',', $final_price);
                    $final_price = str_replace('|', '.', $final_price);
                    $final_price = str_replace(',', '', $final_price);
                }
            } else {
                $final_price = str_replace(',', '.', $image->price);
            }
        } else {
            $final_price = $image->price;
        }

        $final_price = trim(preg_replace('/[^0-9\.]/i', '', $final_price));

        if (strpos($final_price, '.') !== false) {
            $exploded = explode('.', $final_price);

            if (strlen($exploded[ 1 ]) > 2) {
                if (count($exploded) > 2) {
                    $sliced = array_slice($exploded, 0, 2);
                } else {
                    $sliced = $exploded;
                }

                $final_price = implode('', $sliced);
            }
        }

        $price = round($final_price);

        if (!empty($brand->euro_to_inr)) {
            $price_inr = $brand->euro_to_inr * $price;
        } else {
            $price_inr = Setting::get('euro_to_inr') * $price;
        }

        $price_inr = round($price_inr, -3);
        $price_special = $price_inr - ($price_inr * $brand->deduction_percentage) / 100;
        $price_special = round($price_special, -3);

        // Price Discounted
        if (strpos($image->discounted_price, ',') !== false) {
            if (strpos($image->discounted_price, '.') !== false) {
                if (strpos($image->discounted_price, ',') < strpos($image->discounted_price, '.')) {
                    $final_discounted_price = str_replace(',', '', $image->discounted_price);
                } else {
                    $final_discounted_price = str_replace(',', '|', $image->discounted_price);
                    $final_discounted_price = str_replace('.', ',', $final_discounted_price);
                    $final_discounted_price = str_replace('|', '.', $final_discounted_price);
                    $final_discounted_price = str_replace(',', '', $final_discounted_price);
                }
            } else {
                $final_discounted_price = str_replace(',', '.', $image->discounted_price);
            }
        } else {
            $final_discounted_price = $image->discounted_price;
        }

        $final_discounted_price = trim(preg_replace('/[^0-9\.]/i', '', $final_discounted_price));

        if (strpos($final_discounted_price, '.') !== false) {
            $exploded = explode('.', $final_discounted_price);

            if (strlen($exploded[ 1 ]) > 2) {
                if (count($exploded) > 2) {
                    $sliced = array_slice($exploded, 0, 2);
                } else {
                    $sliced = $exploded;
                }

                $final_discounted_price = implode('', $sliced);
            }
        }

        $price_discounted = round($final_discounted_price);

        return [
            'price' => $price,
            'price_discounted' => $price_discounted,
            'price_inr' => $price_inr,
            'price_special' => $price_special
        ];
    }

    public function getDoubleDetails($properties_array)
    {
        if (array_key_exists('Composition', $properties_array)) {
            $composition = (string)$properties_array[ 'Composition' ];
        }

        if (array_key_exists('Color code', $properties_array)) {
            $color = $properties_array[ 'Color code' ];
        }

        if (array_key_exists('Made In', $properties_array)) {
            $made_in = $properties_array[ 'Made In' ];
        }

        if (array_key_exists('category', $properties_array)) {
            $categories = Category::all();
            $category_id = 1;

            foreach ($properties_array[ 'category' ] as $key => $cat) {
                $up_cat = strtoupper($cat);

                if ($up_cat == 'WOMAN') {
                    $up_cat = 'WOMEN';
                }

                if ($key == 0 && $up_cat == 'WOMEN') {
                    $women_children = Category::where('title', 'WOMEN')->first()->childs;
                }

                if (isset($women_children)) {
                    foreach ($women_children as $children) {
                        if (strtoupper($children->title) == $up_cat) {
                            $category_id = $children->id;
                        }

                        foreach ($children->childs as $child) {
                            if (strtoupper($child->title) == $up_cat) {
                                $category_id = $child->id;
                            }
                        }
                    }
                } else {
                    foreach ($categories as $category) {
                        if (strtoupper($category->title) == $up_cat) {
                            $category_id = $category->id;
                        }
                    }
                }

            }

            $category = $category_id;
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => '',
            'lmeasurement' => '',
            'hmeasurement' => '',
            'dmeasurement' => '',
            'measurement_size_type' => '',
            'made_in' => isset($made_in) ? $made_in : '',
            'category' => isset($category) ? $category : 1,
        ];
    }

    public function getLidiaDetails($properties_array): array
    {
        if (array_key_exists('material_used', $properties_array)) {
            $composition = $properties_array[ 'material_used' ];
        }

        if (array_key_exists('color', $properties_array)) {
            $color = $properties_array[ 'color' ];
        }

        if (array_key_exists('sizes', $properties_array)) {
            $sizes = $properties_array[ 'sizes' ];
            $imploded_sizes = implode(',', $sizes);
            $size = $imploded_sizes;
        }

        if (array_key_exists('Category', $properties_array)) {
            $categories = Category::all();
            $category_id = 1;

            foreach ($properties_array[ 'Category' ] as $cat) {
                if ($cat == 'WOMAN') {
                    $cat = 'WOMEN';
                }

                foreach ($categories as $category) {
                    if (strtoupper($category->title) == $cat) {
                        $category_id = $category->id;
                    }
                }
            }

            $category = $category_id;
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => isset($size) ? $size : '',
            'lmeasurement' => '',
            'hmeasurement' => '',
            'dmeasurement' => '',
            'measurement_size_type' => '',
            'made_in' => '',
            'category' => isset($category) ? $category : 1,
        ];
    }

    public function getCucciniDetails($properties_array)
    {
        if (array_key_exists('COMPOSIZIONE', $properties_array)) {
            $composition = $properties_array[ 'COMPOSIZIONE' ];
        }

        if (array_key_exists('COLORI', $properties_array)) {
            $color = $properties_array[ 'COLORI' ];
        }

        $sizes = '';
        if (isset($properties_array[ 'sizes' ])) {
            $sizes = implode(',', $properties_array[ 'sizes' ]);
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => $sizes,
            'lmeasurement' => '',
            'hmeasurement' => '',
            'dmeasurement' => '',
            'measurement_size_type' => '',
            'made_in' => '',
            'category' => 1,
        ];
    }

    public function getGnbDetails($properties_array)
    {
        if (array_key_exists('Details', $properties_array)) {
            if (strpos($properties_array[ 'Details' ], 'Made in') !== false) {
                $made_in = str_replace('\n', '', substr($properties_array[ 'Details' ], strpos($properties_array[ 'Details' ], 'Made in') + 8));

                $composition = str_replace('\n', ' ', substr($properties_array[ 'Details' ], 0, strpos($properties_array[ 'Details' ], 'Made in')));
            } else {
                $composition = (string)$properties_array[ 'Details' ];
            }
        }

        if (array_key_exists('Color Code', $properties_array)) {
            $color = $properties_array[ 'Color Code' ];
        }

        if (array_key_exists('sizes', $properties_array)) {
            $size = implode(',', $properties_array[ 'sizes' ]);
        }

        if (array_key_exists('Size & Fit', $properties_array)) {
            $sizes = $properties_array[ 'Size & Fit' ];
            if (strpos($sizes, 'Width:') !== false) {
                preg_match_all('/Width: ([\d]+)/', $sizes, $match);

                $lmeasurement = (int)($match[ 1 ][ 0 ] ?? 0);
                $measurement_size_type = 'measurement';
            }

            if (strpos($sizes, 'Height:') !== false) {
                if (preg_match_all('/Height: ([\d]+)/', $sizes, $match)) {
                    $hmeasurement = (int)($match[ 1 ][ 0 ] ?? 0);
                }
            }

            if (strpos($sizes, 'Depth:') !== false) {
                if (preg_match_all('/Depth: ([\d]+)/', $sizes, $match)) {
                    $dmeasurement = (int)($match[ 1 ][ 0 ] ?? 0);
                }
            }
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => isset($size) ? $size : '',
            'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
            'hmeasurement' => isset($hmeasurement) ? $hmeasurement : '',
            'dmeasurement' => isset($dmeasurement) ? $dmeasurement : '',
            'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
            'made_in' => isset($made_in) ? $made_in : '',
            'category' => 1,
        ];
    }

    public function getToryDetails($properties_array)
    {

        return [
            'composition' => $properties_array[ 'composition' ] ?? '',
            'color' => $properties_array[ 'color' ] ?? '',
            'size' => implode(',', $properties_array[ 'sizes' ] ?? []),
            'lmeasurement' => $properties_array[ 'length' ] ?? '',
            'hmeasurement' => $properties_array[ 'height' ] ?? '',
            'dmeasurement' => $properties_array[ 'depth' ] ?? '',
            'measurement_size_type' => '',
            'made_in' => $properties_array[ 'made_in' ] ?? '',
            'category' => 1,
        ];
    }

    public function getWiseDetails($properties_array)
    {
        if (array_key_exists('composition', $properties_array)) {
            $composition = (string)$properties_array[ 'composition' ];
        }

        if (array_key_exists('colors', $properties_array)) {
            $color = $properties_array[ 'colors' ];
        }

        if (array_key_exists('sizes', $properties_array)) {
            $size = implode(',', $properties_array[ 'sizes' ]);
        }

        foreach ($properties_array as $property) {
            if (!is_array($property)) {
                if (strpos($property, 'Width:') !== false) {
                    preg_match_all('/Width: ([\d]+)/', $property, $match);

                    $lmeasurement = (int)$match[ 1 ];
                    $measurement_size_type = 'measurement';
                }

                if (strpos($property, 'Height:') !== false) {
                    preg_match_all('/Height: ([\d]+)/', $property, $match);

                    $hmeasurement = (int)$match[ 1 ];
                }

                if (strpos($property, 'Depth:') !== false) {
                    preg_match_all('/Depth: ([\d]+)/', $property, $match);

                    $dmeasurement = (int)$match[ 1 ];
                }
            }
        }

        if (array_key_exists('category', $properties_array)) {
            $categories = Category::all();
            $category_id = 1;

            foreach ($properties_array[ 'category' ] as $key => $cat) {
                $up_cat = strtoupper($cat);

                if ($up_cat == 'WOMAN') {
                    $up_cat = 'WOMEN';
                }

                if ($key == 0 && $up_cat == 'WOMEN') {
                    $women_children = Category::where('title', 'WOMEN')->first()->childs;
                }

                if (isset($women_children)) {
                    foreach ($women_children as $children) {
                        if (strtoupper($children->title) == $up_cat) {
                            $category_id = $children->id;
                        }

                        foreach ($children->childs as $child) {
                            if (strtoupper($child->title) == $up_cat) {
                                $category_id = $child->id;
                            }
                        }
                    }
                } else {
                    foreach ($categories as $category) {
                        if (strtoupper($category->title) == $up_cat) {
                            $category_id = $category->id;
                        }
                    }
                }

            }

            $category = $category_id;
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => isset($size) ? $size : '',
            'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
            'hmeasurement' => isset($hmeasurement) ? $hmeasurement : '',
            'dmeasurement' => isset($dmeasurement) ? $dmeasurement : '',
            'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
            'made_in' => '',
            'category' => isset($category) ? $category : 1,
        ];
    }

    public function getDivoDetails($properties_array)
    {
        if (array_key_exists('materialUsed', $properties_array)) {
            $composition = (string)$properties_array[ 'materialUsed' ];
        }

        if (array_key_exists('color', $properties_array)) {
            $color = $properties_array[ 'color' ];
        }

        // foreach ($properties_array as $property) {
        //   if (!is_array($property)) {
        //     if (strpos($property, 'Width:') !== false) {
        //       preg_match_all('/Width: ([\d]+)/', $property, $match);
        //
        //       $lmeasurement = (int) $match[1];
        //       $measurement_size_type = 'measurement';
        //     }
        //
        //     if (strpos($property, 'Height:') !== false) {
        //       preg_match_all('/Height: ([\d]+)/', $property, $match);
        //
        //       $hmeasurement = (int) $match[1];
        //     }
        //
        //     if (strpos($property, 'Depth:') !== false) {
        //       preg_match_all('/Depth: ([\d]+)/', $property, $match);
        //
        //       $dmeasurement = (int) $match[1];
        //     }
        //   }
        // }

        if (array_key_exists('category', $properties_array)) {
            $categories = Category::all();
            $category_id = 1;

            foreach ($properties_array[ 'category' ] as $key => $cat) {
                $up_cat = strtoupper($cat);

                if ($up_cat == 'WOMAN') {
                    $up_cat = 'WOMEN';
                }

                if ($key == 0 && $up_cat == 'WOMEN') {
                    $women_children = Category::where('title', 'WOMEN')->first()->childs;
                }

                if (isset($women_children)) {
                    foreach ($women_children as $children) {
                        if (strtoupper($children->title) == $up_cat) {
                            $category_id = $children->id;
                        }

                        foreach ($children->childs as $child) {
                            if (strtoupper($child->title) == $up_cat) {
                                $category_id = $child->id;
                            }
                        }
                    }
                } else {
                    foreach ($categories as $category) {
                        if (strtoupper($category->title) == $up_cat) {
                            $category_id = $category->id;
                        }
                    }
                }

            }

            $category = $category_id;
        }

        if (array_key_exists('country', $properties_array)) {
            $made_in = $properties_array[ 'country' ];
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => '',
            'lmeasurement' => '',
            'hmeasurement' => '',
            'dmeasurement' => '',
            'measurement_size_type' => '',
            'made_in' => isset($made_in) ? $made_in : '',
            'category' => isset($category) ? $category : 1,
        ];
    }

    public function getSpinnakerDetails($properties_array)
    {
        if (array_key_exists('material_used', $properties_array)) {
            $composition = (string)$properties_array[ 'material_used' ];
        }

        if (array_key_exists('color', $properties_array)) {
            $color = $properties_array[ 'color' ];
        }

        if (array_key_exists('sizes', $properties_array)) {
            $sizes = $properties_array[ 'sizes' ];
            $size = implode(',', $sizes);
            // if (array_key_exists('Length', $sizes)) {
            //   preg_match_all('/\s((.*))\s/', $sizes['Length'], $match);
            //
            //   if (array_key_exists('0', $match[1])) {
            //     $lmeasurement = (int) $match[1][0];
            //     $measurement_size_type = 'measurement';
            //   }
            // }
        }

        // foreach ($properties_array as $property) {
        //   if (!is_array($property)) {
        //     if (strpos($property, 'Width:') !== false) {
        //       preg_match_all('/Width: ([\d]+)/', $property, $match);
        //
        //       $lmeasurement = (int) $match[1];
        //       $measurement_size_type = 'measurement';
        //     }
        //
        //     if (strpos($property, 'Height:') !== false) {
        //       preg_match_all('/Height: ([\d]+)/', $property, $match);
        //
        //       $hmeasurement = (int) $match[1];
        //     }
        //
        //     if (strpos($property, 'Depth:') !== false) {
        //       preg_match_all('/Depth: ([\d]+)/', $property, $match);
        //
        //       $dmeasurement = (int) $match[1];
        //     }
        //   }
        // }

        // if (array_key_exists('category', $properties_array)) {
        //   $categories = Category::all();
        //   $category_id = 1;
        //   $cat = strtoupper($properties_array['category']);
        //   // foreach ($properties_array['category'] as $cat) {
        //     if ($cat == 'WOMAN') {
        //       $cat = 'WOMEN';
        //     }
        //
        //     foreach ($categories as $category) {
        //       if (strtoupper($category->title) == $cat) {
        //         $category_id = $category->id;
        //       }
        //     }
        //   // }
        //
        //   $category = $category_id;
        // }

        if (array_key_exists('country', $properties_array)) {
            $made_in = $properties_array[ 'country' ];
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => isset($size) ? $size : '',
            'lmeasurement' => '',
            'hmeasurement' => '',
            'dmeasurement' => '',
            'measurement_size_type' => '',
            'made_in' => isset($made_in) ? $made_in : '',
            'category' => isset($category) ? $category : 1,
        ];
    }

    public function getGeneralDetails($properties_array)
    {
        if (array_key_exists('material_used', $properties_array)) {
            $composition = (string)$properties_array[ 'material_used' ];
        }

        if (array_key_exists('color', $properties_array)) {
            $color = $properties_array[ 'color' ];
        }

        if (array_key_exists('sizes', $properties_array)) {
            $sizes = $properties_array[ 'sizes' ];
            $size = implode(',', $sizes);
        }

        if (array_key_exists('dimension', $properties_array)) {
            $measurement_size_type = 'measurement';
            $lmeasurement = $properties_array[ 'dimension' ][ 0 ] ?? '0';
            $hmeasurement = $properties_array[ 'dimension' ][ 1 ] ?? '0';
            $dmeasurement = $properties_array[ 'dimension' ][ 2 ] ?? '0';
        }

        if (array_key_exists('category', $properties_array)) {
            $categories = Category::all();
            $category_id = 1;

            foreach ($properties_array[ 'category' ] as $key => $cat) {
                $up_cat = strtoupper($cat);

                if ($up_cat == 'WOMAN') {
                    $up_cat = 'WOMEN';
                }

                if ($key == 0 && $up_cat == 'WOMEN') {
                    $women_children = Category::where('title', 'WOMEN')->first()->childs;
                }

                if (isset($women_children)) {
                    foreach ($women_children as $children) {
                        if (strtoupper($children->title) == $up_cat) {
                            $category_id = $children->id;
                        }

                        foreach ($children->childs as $child) {
                            if (strtoupper($child->title) == $up_cat) {
                                $category_id = $child->id;
                            }
                        }
                    }
                } else {
                    foreach ($categories as $category) {
                        if (strtoupper($category->title) == $up_cat) {
                            $category_id = $category->id;
                        }
                    }
                }

            }

            $category = $category_id;
        }

        if (array_key_exists('country', $properties_array)) {
            $made_in = $properties_array[ 'country' ];
        }

        return [
            'composition' => isset($composition) ? $composition : '',
            'color' => isset($color) ? $color : '',
            'size' => isset($size) ? $size : '',
            'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
            'hmeasurement' => isset($hmeasurement) ? $hmeasurement : '',
            'dmeasurement' => isset($dmeasurement) ? $dmeasurement : '',
            'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
            'made_in' => isset($made_in) ? $made_in : '',
            'category' => isset($category) ? $category : 1,
        ];
    }
}
