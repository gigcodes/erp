<?php

namespace App\Services\Products;

use App\Brand;
use App\Product;
use App\Category;
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

        if (array_key_exists('Composition', $properties_array)) {
          $product->composition = (string) $properties_array['Composition'];
        }

        if (array_key_exists('Color code', $properties_array)) {
          $product->color = $properties_array['Color code'];
        }

        if (array_key_exists('Made In', $properties_array)) {
          $product->made_in = $properties_array['Made In'];
        }

        if (array_key_exists('category', $properties_array)) {
          $categories = Category::all();
          $category_id = 1;

          foreach ($properties_array['category'] as $cat) {
            if ($cat == 'WOMAN') {
              $cat = 'WOMEN';
            }

            foreach ($categories as $category) {
              if (strtoupper($category->title) == $cat) {
                $category_id = $category->id;
              }
            }
          }

          $product->category = $category_id;
        }

        $brand = Brand::find($image->brand_id);

        if (strpos($image->price, ',') !== false) {
          if (strpos($image->price, '.') !== false) {
            if (strpos($image->price, ',') < strpos($image->price, '.')) {
              $temp = str_replace(',', '-', $image->price);
              $dot = str_replace('.', ',', $temp);
              $final = str_replace('-', '.', $dot);
              $price = round(preg_replace('/[\€.]/', '', $final));
            }
          }
        } else {
          $price = round(preg_replace('/[\€.]/', '', $image->price));
        }

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
