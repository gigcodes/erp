<?php

namespace App\Services\Products;

use App\Brand;
use Validator;
use App\Product;
use App\Setting;
use App\Supplier;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class ToryProductsCreator
{
    public function createProduct($image)
    {
        $data['sku'] = str_replace(' ', '', $image->sku);
        $validator   = Validator::make($data, [
            'sku' => 'unique:products,sku',
        ]);

        if ($validator->fails()) {
            $product = Product::where('sku', $image->sku)->first();
        } else {
            $product = new Product;
        }

        $product->sku               = str_replace(' ', '', $image->sku);
        $product->brand             = $image->brand_id;
        $product->supplier          = 'Tory Burch';
        $product->name              = $image->title;
        $product->short_description = $image->description;
        $product->supplier_link     = $image->url;
        $product->stage             = 3;
        $product->is_scraped        = 1;

        $properties_array = $image->properties;

        if (array_key_exists('sizes', $properties_array)) {
            $sizes = $properties_array['sizes'];
            if (array_key_exists('Length', $sizes)) {
                preg_match_all('/\s((.*))\s/', $sizes['Length'], $match);

                if (array_key_exists('0', $match[1])) {
                    $product->lmeasurement          = (int) $match[1][0];
                    $product->measurement_size_type = 'measurement';
                }
            }
        }

        if (array_key_exists('color', $properties_array)) {
            $product->color = $properties_array['color'];
        }

        $brand = Brand::find($image->brand_id);

        if (strpos($image->price, ',') !== false) {
            if (strpos($image->price, '.') !== false) {
                if (strpos($image->price, ',') < strpos($image->price, '.')) {
                    $final_price = str_replace(',', '', $image->price);
                }
            } else {
                $final_price = str_replace(',', '.', $image->price);
            }
        } else {
            $final_price = $image->price;
        }

        $price          = round(preg_replace('/[\&euro;€,]/', '', $final_price));
        $product->price = $price;

        if (! empty($brand->euro_to_inr)) {
            $product->price_inr = $brand->euro_to_inr * $product->price;
        } else {
            $product->price_inr = Setting::get('euro_to_inr') * $product->price;
        }

        $product->price_inr     = round($product->price_inr, -3);
        $product->price_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

        $product->price_special = round($product->price_special, -3);

        $product->save();

        if ($db_supplier = Supplier::where('supplier', 'Tory Burch')->first()) {
            $product->suppliers()->syncWithoutDetaching($db_supplier->id);
        }

        $images = $image->images;

        $product->detachMediaTags(config('constants.media_tags'));

        foreach ($images as $image_name) {
            $path  = public_path('uploads') . '/social-media/' . $image_name;
            $media = MediaUploader::fromSource($path)
                                   ->toDirectory('product/' . floor($product->id / config('constants.image_per_folder')))
                                   ->upload();
            $product->attachMedia($media, config('constants.media_tags'));
        }
    }
}
