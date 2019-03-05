<?php

namespace App\Services\Products;

use App\Brand;
use App\Product;
use App\Setting;
use Validator;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class DoubleProductsCreator
{
    public function createDoubleProducts($image)
    {
      $data['sku'] = str_replace(' ', '', $image->sku);
      $validator = Validator::make($data, [
        'sku' => 'unique:products,sku'
      ]);

      if ($validator->fails()) {

      } else {
        $product = new Product;
        $product->sku = str_replace(' ', '', $image->sku);
        $product->brand = $image->brand_id;
        $product->supplier = 'Double F';
        $product->name = $image->title;
        $product->short_description = $image->description;
        $product->supplier_link = $image->url;
        $product->stage = 3;

        $properties_array = $image->properties ?? [];


        if (array_key_exists('1', $properties_array)) {
          $product->composition = (string) $properties_array['1'];
        }

        if (array_key_exists('Colors', $properties_array)) {
          $product->color = $properties_array['Colors'];
        }

        foreach ($properties_array as $property) {
          if (!is_array($property)) {
            if (strpos($property, 'Width:') !== false) {
              preg_match_all('/Width: ([\d]+)/', $property, $match);

              $product->lmeasurement = (int) $match[1];
              $product->measurement_size_type = 'measurement';
            }

            if (strpos($property, 'Height:') !== false) {
              preg_match_all('/Height: ([\d]+)/', $property, $match);

              $product->hmeasurement = (int) $match[1];
            }

            if (strpos($property, 'Depth:') !== false) {
              preg_match_all('/Depth: ([\d]+)/', $property, $match);

              $product->dmeasurement = (int) $match[1];
            }
          }
        }

        $brand = Brand::find($image->brand_id);

        $price = (int) preg_replace('/[\&euro;.]/', '', $image->price);
        $product->price = $price;

        if(!empty($brand->euro_to_inr))
          $product->price_inr = $brand->euro_to_inr * $product->price;
        else
          $product->price_inr = Setting::get('euro_to_inr') * $product->price;

        $product->price_inr = round($product->price_inr, -3);
        $product->price_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

        $product->price_special = round($product->price_special, -3);

        $product->save();

        $images = $image->images;

        foreach ($images as $image_name) {
          $path = public_path('uploads') . '/social-media/' . $image_name;
          $media = MediaUploader::fromSource($path)->upload();
          $product->attachMedia($media,config('constants.media_tags'));
        }
      }
    }
}
