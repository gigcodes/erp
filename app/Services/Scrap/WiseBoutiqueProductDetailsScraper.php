<?php

namespace App\Services\Scrap;

use App\Brand;
use App\ScrapedProducts;
use App\ScrapEntries;
use App\Product;
use Storage;
use Validator;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class WiseBoutiqueProductDetailsScraper extends Scraper
{

    public function scrap()
    {
        $products = ScrapEntries::where('is_scraped', 0)->where('is_product_page', 1)->where('site_name', 'Wiseboutique')->take(25)->get();

        foreach ($products as $product) {
            $this->getProductDetails($product);
        }
    }

    public function createProducts()
    {
        dd('here...');
        $products = ScrapedProducts::where('has_sku', 1)->get();

        foreach ($products as $product) {
          $data['sku'] = $product->sku;
          $validator = Validator::make($data, [
            'sku' => 'unique:products,sku'
          ]);

          if (!$validator->fails()) {
              $new_product = new Product;
              $new_product->sku = $product->sku;
              $new_product->brand = $product->brand_id;
              $new_product->supplier = 'Wiseboutique';
              $new_product->name = $product->title;
              $new_product->short_description = $product->description;
              $new_product->price = $product->price;
              $new_product->supplier_link = $product->url;
              $new_product->save();

              foreach ($product->images as $image_name) {
                  $path = public_path('uploads') . '/social-media/' . $image_name;
                  $media = MediaUploader::fromSource($path)->upload();
                  $new_product->attachMedia($media,config('constants.media_tags'));
              }
          }
        }
    }

    private function getProductDetails(ScrapEntries $scrapEntry)
    {
        $content = $this->getContent($scrapEntry->url);
        if ($content === '') {
            $scrapEntry->delete();
            return;
        }

        $c = new HtmlPageCrawler($content);
        $title = $this->getTitle($c);
        $brand = $this->getDesignerName($c);
        $price = $this->getPrice($c);
        $sku = $this->getSku($c);
        $images = $this->getImages($c);
        $description = $this->getDescription($c);
        $properties = $this->getProperties($c);

        if (!$images || !$title) {
            $scrapEntry->delete();
            return;
        }

        $brandId = $this->getBrandId($brand);

        $image = new ScrapedProducts();
        $image->brand_id = $brandId;
        $image->sku = $sku;
        $image->website = 'Wiseboutique';
        $image->title = $title;
        $image->description = $description;
        $image->images = $images;
        $image->price = $price;
        if ($sku != 'N/A') {
            $image->has_sku = 1;
        }
        $image->url = $scrapEntry->url;
        $image->properties = $properties;
        $image->save();

        unset($image);

        $scrapEntry->is_scraped = 1;
        $scrapEntry->save();

        $data['sku'] = $sku;
        $validator = Validator::make($data, [
          'sku' => 'unique:products,sku'
        ]);

        if ($validator->fails()) {

        } else {
          $product = new Product;
          $product->sku = $sku;
          $product->brand = $brandId;
          $product->supplier = 'Wiseboutique';
          $product->name = $title;
          $product->short_description = $description;
          $product->price = $price;
          $product->supplier_link = $scrapEntry->url;
          $product->stage = 3;
          $product->save();

          foreach ($images as $image_name) {
            $path = public_path('uploads') . '/social-media/' . $image_name;
            $media = MediaUploader::fromSource($path)->upload();
            $product->attachMedia($media,config('constants.media_tags'));
          }
        }
    }

    private function getTitle(HtmlPageCrawler $c) {
        try {
            $title = preg_replace('/\s\s+/', '', $c->filter('div.dettagliinterno h2')->getInnerHtml());
        } catch (\Exception $exception) {
            $title = '';
        }
        return $title;
    }

    private function getPrice(HtmlPageCrawler $c) {
        try {
            $price = preg_replace('/\s\s+/', '', $c->filter('div.prezzidettaglio div span')->getInnerHtml());
        } catch (\Exception $exception) {
            $price = 'N/A';
        }

        return $price;
    }

    private function getSku(HtmlPageCrawler $c) {
        try {
            $sku = preg_replace('/\s\s+/', '', $c->filter('div.dettagliinterno h3 i span')->getInnerHtml());
        } catch (\Exception $exception) {
            $sku = 'N/A';
        }

        return $sku;
    }

    private function getDescription(HtmlPageCrawler $c) {
        try {
            $title = preg_replace('/\s\s+/', '', strip_tags($c->filter('div.descrizioniprodotto div')->getInnerHtml()));
        } catch (\Exception $exception) {
            $title = '';
        }

        $title = str_replace('-', '\n', $title);
        return $title;
    }

    private function getImages(HtmlPageCrawler $c) {
        $images = $c->filter('.dettagli a')->getIterator();
        $content = [];

        foreach ($images as $image) {
            $content[] = 'https://www.wiseboutique.com' . trim($image->getAttribute('href'));
        }

        return $this->downloadImages($content, 'gnb');
    }

    private function getDesignerName(HtmlPageCrawler $c)
    {
        try {
            $title = preg_replace('/\s\s+/', '', $c->filter('h1.notranslate a span')->getInnerHtml());
        } catch (\Exception $exception) {
            $title = '';
        }
        return $title;
    }

    private function getBrandId($brandName)
    {
        $brand = Brand::where('name', $brandName)->first();

        if (!$brand) {
            $brand = new Brand();
            $brand->name = $brandName;
            $brand->euro_to_inr = 0;
            $brand->deduction_percentage = 0;
            $brand->save();
        }


        return $brand->id;
    }

    private function downloadImages($data, $prefix = 'img'): array
    {
        $images = [];
        foreach ($data as $key=>$datum) {
            try {
                $imgData = file_get_contents($datum);
            } catch (\Exception $exception) {
                continue;
            }

            $fileName = $prefix . '_' . md5(time()).'.png';
            Storage::disk('uploads')->put('social-media/'.$fileName, $imgData);

            $images[] = $fileName;
        }

        return $images;
    }

    private function getProperties(HtmlPageCrawler $c) {
        $propertiesValues =  $c->filter('div.dettagliinterno div.clear .col9')->getIterator();
        $propertiesData = [];

        foreach ($propertiesValues as $key=>$property) {
            $value = preg_replace('/\s\s+/', '\n', $property->textContent);
            $propertiesData[] = $value;
        }

        $bread = $c->filter('ol.breadcrumb li')->filter('a span')->getIterator();

        $categoryTypes = [];

        foreach ($bread as $item) {
            if (trim($item->textContent) != 'HOME') {
                $categoryTypes[] = trim($item->textContent);
            }
        }

        if (in_array('DONNA', $categoryTypes, false) || in_array('WOMAN', $categoryTypes, false)) {
            $propertiesData['gender'] = 'Female';
        } else {
            $propertiesData['gender'] = 'Male';
        }

        $propertiesData['category'] = $categoryTypes;

        return $propertiesData;
    }
}
