<?php

namespace App\Services\Products;

use App\SkuColorReferences;
use Validator;
use Illuminate\Support\Facades\Log;
use App\Brand;
use App\Category;
use App\ColorNamesReference;
use App\Product;
use App\ProductStatus;
use App\ScrapActivity;
use App\Supplier;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\SupplierBrandCount;
use App\SupplierCategoryCount;

class ProductsCreator
{
    public function createProduct($image, $isExcel = 0)
    {
        // Debug log
        Log::channel('productUpdates')->debug("[Start] createProduct is called");

        // Set supplier
        $supplier = Supplier::where(function ($query) use ($image) { $query->where('supplier', '=', $image->website)->orWhere('scraper_name', '=', $image->website); })->first();

        // Do we have a supplier?
        if ($supplier == null) {
            // Debug
            Log::channel('productUpdates')->debug("[Error] Supplier is null " . $image->website);

            // Return false
            return false;
        } else {
            $supplierId = $supplier->id;
            $supplier = $supplier->supplier;
        }

        // Get formatted data
        $formattedPrices = $this->formatPrices($image);
        $formattedDetails = $this->getGeneralDetails($image->properties);

        // Set data.sku for validation
        $data[ 'sku' ] = ProductHelper::getSku($image->sku);
        $validator = Validator::make($data, [
            'sku' => 'unique:products,sku'
        ]);

        // Get color
        $color = ColorNamesReference::getProductColorFromObject($image);

        // Store count
        try {
            SupplierBrandCount::firstOrCreate(['supplier_id' => $supplierId, 'brand_id' => $image->brand_id]);
            if (!empty($formattedDetails[ 'category' ])) {
                SupplierCategoryCount::firstOrCreate(['supplier_id' => $supplierId, 'category_id' => $formattedDetails[ 'category' ]]);
            }
            if (!empty($color)) {
                SkuColorReferences::firstOrCreate(['brand_id' => $image->brand_id, 'color_name' => $color]);
            }
        } catch (\Exception $e) {
            // var_dump($e->getMessage());
        }

        // Product validated
        if ($validator->fails()) {
            // Debug
            Log::channel('productUpdates')->debug("[validator] fails - sku exists " . ProductHelper::getSku($image->sku));

            // Try to get the product from the database
            $product = Product::where('sku', $data[ 'sku' ])->first();

            // Does the product exist? This should not fail, since the validator told us it's there
            if (!$product) {
                // Debug
                Log::channel('productUpdates')->debug("[Error] No product!");

                // Return false
                return false;
            }

            // Is the product not approved yet?
            if (!StatusHelper::isApproved($image->status_id)) {
                // Check if we can update the title - not manually entered
                $manual = ProductStatus::where('name', 'MANUAL_TITLE')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->name = ProductHelper::getRedactedText($image->title, 'name');
                }

                // Check if we can update the short description - not manually entered
                $manual = ProductStatus::where('name', 'MANUAL_SHORT_DESCRIPTION')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->short_description = ProductHelper::getRedactedText($image->description, 'short_description');
                }

                // Check if we can update the color - not manually entered
                $manual = ProductStatus::where('name', 'MANUAL_COLOR')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    $product->color = $color;
                }

                // Check if we can update the composition - not manually entered
                $manual = ProductStatus::where('name', 'MANUAL_COMPOSITION')->first();
                if ($manual == null || (int)$manual->value == 0) {
                    // Check for composition key
                    if (isset($image->properties[ 'composition' ])) {
                        $product->composition = trim(ProductHelper::getRedactedText($image->properties[ 'composition' ] ?? '', 'composition'));
                    }

                    // Check for material_used key
                    if (isset($image->properties[ 'material_used' ])) {
                        $product->composition = trim(ProductHelper::getRedactedText($image->properties[ 'material_used' ] ?? '', 'composition'));
                    }
                }

                // Update the category
                $product->category = $formattedDetails[ 'category' ];
            }

            // Get current sizes
            $allSize = [];

            // Update with scraped sizes
            if (is_array($image->properties[ 'sizes' ]) && count($image->properties[ 'sizes' ]) > 0) {
                $sizes = $image->properties[ 'sizes' ];

                // Loop over sizes and redactText
                if (is_array($sizes) && $sizes > 0) {
                    foreach ($sizes as $size) {
                        $allSize[] = ProductHelper::getRedactedText($size, 'composition');
                    }
                }

                $product->size = implode(',', $allSize);
            }

            // Store measurement
            $product->lmeasurement = $formattedDetails[ 'lmeasurement' ] > 0 ? $formattedDetails[ 'lmeasurement' ] : null;
            $product->hmeasurement = $formattedDetails[ 'hmeasurement' ] > 0 ? $formattedDetails[ 'hmeasurement' ] : null;
            $product->dmeasurement = $formattedDetails[ 'dmeasurement' ] > 0 ? $formattedDetails[ 'dmeasurement' ] : null;
            $product->price = $formattedPrices[ 'price' ];
            $product->price_inr = $formattedPrices[ 'price_inr' ];
            $product->price_special = $formattedPrices[ 'price_special' ];
            $product->price_eur_special = $formattedPrices[ 'price_eur_special' ];
            $product->is_scraped = $isExcel == 1 ? $product->is_scraped : 1;
            $product->save();
            $product->attachImagesToProduct();

            if ($image->is_sale) {
                $product->is_on_sale = 1;
                $product->save();
            }

            if ($db_supplier = Supplier::where(function ($query) use ($supplier) { $query->where('supplier', '=', $supplier)->orWhere('scraper_name', '=', $supplier); })->first()) {
                if ($product) {
                    $product->suppliers()->syncWithoutDetaching([
                        $db_supplier->id => [
                            'title' => $image->title,
                            'description' => $image->description,
                            'supplier_link' => $image->url,
                            'stock' => 1,
                            'price' => $formattedPrices[ 'price' ],
                            'price_discounted' => $formattedPrices[ 'price_discounted' ],
                            'size' => $formattedDetails[ 'size' ],
                            'color' => $formattedDetails[ 'color' ],
                            'composition' => $formattedDetails[ 'composition' ],
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

            Log::channel('productUpdates')->debug("[Success] Updated product");

            return;

        } else {
            Log::channel('productUpdates')->debug("[validator] success - new sku " . ProductHelper::getSku($image->sku));
            $product = new Product;
        }

        if ($product === null) {
            Log::channel('productUpdates')->debug("[Skipped] Product is null");
            return;
        }

        $product->status_id = 2;
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

        $product->composition = $formattedDetails[ 'composition' ];
        $product->color = ColorNamesReference::getProductColorFromObject($image);
        $product->size = $formattedDetails[ 'size' ];
        $product->lmeasurement = (int)$formattedDetails[ 'lmeasurement' ];
        $product->hmeasurement = (int)$formattedDetails[ 'hmeasurement' ];
        $product->dmeasurement = (int)$formattedDetails[ 'dmeasurement' ];
        $product->measurement_size_type = $formattedDetails[ 'measurement_size_type' ];
        $product->made_in = $formattedDetails[ 'made_in' ];
        $product->category = $formattedDetails[ 'category' ];

        $product->price = $formattedPrices[ 'price' ];
        $product->price_inr = $formattedPrices[ 'price_inr' ];
        $product->price_special = $formattedPrices[ 'price_special' ];
        $product->price_eur_special = $formattedPrices[ 'price_eur_special' ];

        try {
            $product->save();
            $product->attachImagesToProduct();
            Log::channel('productUpdates')->debug("[New] Product created with ID " . $product->id);
        } catch (\Exception $exception) {
            Log::channel('productUpdates')->alert("[Exception] Couldn't create product");
            Log::channel('productUpdates')->alert($exception->getMessage());
            return;
        }

        if ($db_supplier = Supplier::where(function ($query) use ($supplier) { $query->where('supplier', '=', $supplier)->orWhere('scraper_name', '=', $supplier); })->first()) {
            $product->suppliers()->syncWithoutDetaching([
                $db_supplier->id => [
                    'title' => $image->title,
                    'description' => $image->description,
                    'supplier_link' => $image->url,
                    'stock' => 1,
                    'price' => $formattedPrices[ 'price' ],
                    'price_discounted' => $formattedPrices[ 'price_discounted' ],
                    'size' => $formattedDetails[ 'size' ],
                    'color' => $formattedDetails[ 'color' ],
                    'composition' => $formattedDetails[ 'composition' ],
                    'sku' => $image->original_sku
                ]
            ]);
        }
    }

    public function formatPrices($image)
    {
        // Get brand from database
        $brand = Brand::find($image->brand_id);

        // Check for EUR to INR
        if (!empty($brand->euro_to_inr)) {
            $price_inr = (float)$brand->euro_to_inr * (float)trim($image->price);
        } else {
            $price_inr = (float)Setting::get('euro_to_inr') * (float)trim($image->price);
        }

        // Set INR price and special price
        $price_inr = round($price_inr, -3);
        $price_special = $price_inr - ($price_inr * $brand->deduction_percentage) / 100;
        $price_special = round($price_special, -3);
        
        if (!empty($image->price)) {
            $priceEurSpecial = $image->price - ($image->price * $brand->deduction_percentage) / 100;
        }else{
            $priceEurSpecial = '';   
        }

        // Return prices
        return [
            'price' => $image->price,
            'price_discounted' => $image->discounted_price,
            'price_inr' => $price_inr,
            'price_special' => $price_special,
            'price_eur_special' => $priceEurSpecial,
            
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
            $orgSizes = $properties_array[ 'sizes' ];
            $tmpSizes = [];

            // Loop over sizes
            foreach ($orgSizes as $size) {
                if (substr(strtoupper($size), -2) == 'IT') {
                    $size = str_replace('IT', '', $size);
                    $size = trim($size);
                }

                if (!empty($size)) {
                    $tmpSizes[] = $size;
                }
            }

            $size = implode(',', $tmpSizes);
        }

        if (array_key_exists('dimension', $properties_array)) {
            if (is_array($properties_array[ 'dimension' ])) {
                $exploded = $properties_array[ 'dimension' ];
                if (count($exploded) > 0) {
                    if (array_key_exists('0', $exploded)) {
                        $lmeasurement = (int)$exploded[ 0 ];
                        $measurement_size_type = 'measurement';
                    }

                    if (array_key_exists('1', $exploded)) {
                        $hmeasurement = (int)$exploded[ 1 ];
                    }

                    if (array_key_exists('2', $exploded)) {
                        $dmeasurement = (int)$exploded[ 2 ];
                    }
                }
            }
        }

        // Get category
        if (array_key_exists('category', $properties_array)) {
            // Check if category is an array
            if (is_array($properties_array[ 'category' ])) {
                // Set gender to null
                $gender = null;

                // Loop over categories to find gender
                foreach ($properties_array[ 'category' ] as $category) {
                    // Check for gender man
                    if (in_array(strtoupper($category), ['MAN', 'MEN', 'UOMO', 'MALE'])) {
                        $gender = 'MEN';
                    }

                    // Check for gender woman
                    if (in_array(strtoupper($category), ['WOMAN', 'WOMEN', 'DONNA', 'FEMALE'])) {
                        $gender = 'WOMEN';
                    }
                }

                // Try to get category ID
                $category = Category::getCategoryIdByKeyword(end($properties_array[ 'category' ]), $gender);
            }
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
