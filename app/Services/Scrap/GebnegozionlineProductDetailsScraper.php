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

class GebnegozionlineProductDetailsScraper extends Scraper
{

    public function scrap()
    {
        $products = ScrapEntries::where('is_scraped', 0)->where('is_product_page', 1)->take(25)->get();

        foreach ($products as $product) {
            $this->getProductDetails($product);
        }
    }

    public function createProducts()
    {
        $products = ScrapedProducts::where('has_sku', 1)->get();

        foreach ($products as $product) {
          $data['sku'] = $product->sku;
          $validator = Validator::make($data, [
            'sku' => 'unique:products,sku'
          ]);

          if ($validator->fails()) {

          } else {
            $new_product = new Product;
            $new_product->sku = $product->sku;
            $new_product->brand = $product->brand_id;
            $new_product->supplier = 'G & B Negozionline';
            $new_product->name = $product->title;
            $new_product->short_description = $product->description;
            $new_product->price = $product->price;
            $new_product->supplier_link = $product->url;
            $new_product->stage = 3;
            $new_product->save();

            foreach ($product->images as $image_name) {
              $path = public_path('uploads') . '/social-media/' . $image_name;
              $media = MediaUploader::fromSource($path)->upload();
              $new_product->attachMedia($media,config('constants.media_tags'));
            }
          }
        }
    }

    public function updateSku() {

        $products = ScrapedProducts::where('has_sku', 0)->take(10)->get();

        foreach ($products as $product) {
            $content = $this->getContent($product->url);
            if ($content === '') {
                $product->delete();
                return;
            }

            $c = new HtmlPageCrawler($content);
            $sku = $this->getSku($c);
            $product->sku = $sku;
            if ($sku != 'N/A') {
                $product->has_sku = 1;
            }
            $product->save();
        }
    }

    public function updateProperties() {

        $products = ScrapedProducts::where('is_property_updated', 0)->take(10)->get();

        foreach ($products as $product) {
            $content = $this->getContent($product->url);
            if ($content === '') {
                $product->delete();
                return;
            }

            $c = new HtmlPageCrawler($content);
            $properties = $this->getProperties($c);
            $product->properties = $properties;
            $product->is_property_updated = 1;
            $product->save();
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
        $sku = $this->getSku($c);
        $brand = $this->getDesignerName($c);
        $images = $this->getImages($c);
        $description = $this->getDescription($c);
        $price = $this->getPrice($c);
        $properties = $this->getProperties($c);


        if (!$images || !$title) {
            $scrapEntry->delete();
            return;
        }

        $brandId = $this->getBrandId($brand);

        $image = new ScrapedProducts();
        $image->brand_id = $brandId;
        $image->sku = $sku;
        $image->website = 'G&B';
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

        // $data['sku'] = $sku;
        // $validator = Validator::make($data, [
        //   'sku' => 'unique:products,sku'
        // ]);
        //
        // if ($validator->fails()) {
        //
        // } else {
        //   $product = new Product;
        //   $product->sku = $sku;
        //   $product->brand = $brandId;
        //   $product->supplier = 'G & B Negozionline';
        //   $product->name = $title;
        //   $product->short_description = $description;
        //   $product->price = $price;
        //   $product->supplier_link = $scrapEntry->url;
        //   $product->stage = 3;
        //
        //   $properties_array = json_decode($properties);
        //
        //   if (array_key_exists('Details', $properties_array)) {
        //     $product->composition = $properties_array['Details'];
        //   }
        //
        //   if (array_key_exists('Color Code', $properties_array)) {
        //     $product->color = $properties_array['Color Code'];
        //   }
        //
        //   // size
        //   // price
        //
        //   $product->save();
        //
        //   foreach ($images as $image_name) {
        //     $path = public_path('uploads') . '/social-media/' . $image_name;
        //     $media = MediaUploader::fromSource($path)->upload();
        //     $product->attachMedia($media,config('constants.media_tags'));
        //   }
        }
    }

    private function getTitle(HtmlPageCrawler $c) {
        try {
            $title = preg_replace('/\s\s+/', '', $c->filter('.product-title-name div.value p.title')->getInnerHtml());
        } catch (\Exception $exception) {
            $title = '';
        }
        return $title;
    }

    private function getPrice(HtmlPageCrawler $c) {
        try {
            $price = preg_replace('/\s\s+/', '', $c->filter('span.price')->getInnerHtml());
        } catch (\Exception $exception) {
            $price = 'N/A';
        }

        return $price;
    }

    private function getSku(HtmlPageCrawler $c) {
        try {
            $sku = preg_replace('/\s\s+/', '', $c->filter('div.product-code div p')->getInnerHtml());
        } catch (\Exception $exception) {
            $sku = 'N/A';
        }

        return $sku;
    }

    private function getDescription(HtmlPageCrawler $c) {
        try {
            $title = preg_replace('/\s\s+/', '', strip_tags($c->filter('div.description div.value')->getInnerHtml()));
        } catch (\Exception $exception) {
            $title = '';
        }
        return $title;
    }

    private function getImages(HtmlPageCrawler $c) {
        $scripts = $c->filter('script')->getIterator();
        $content = '';

        foreach ($scripts as $script) {
            $content = trim($script->textContent);
            if (strpos($content, 'var sizeGuideData =') !== false) {
                break;
            }
        }

        $content = str_replace('var sizeGuideData = ', '', $content);
        $content = str_replace('}];', '}]', $content);


        $content = json_decode($content, true);


        $content = array_map(function($item) {
            return $item['full'];
        }, $content);


        return $this->downloadImages($content, 'gnb');
    }

    private function getDesignerName(HtmlPageCrawler $c)
    {
        try {
            $title = preg_replace('/\s\s+/', '', $c->filter('h1.page-title span')->getInnerHtml());
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

    private function isMaleOrFemale($url) {
        $url = strtolower($url);
        if (strpos($url, 'donna') !== false || strpos($url, 'women') !== false) {
            return 'female';
        }

        if (strpos($url, 'uomo') !== false || strpos($url, 'men') !== false) {
            return 'female';
        }

        return 'male';
    }

    private function getProperties(HtmlPageCrawler $c) {
        $properties =  $c->filter('table#product-attribute-specs-table tbody')->children()->getIterator();

        $propertiesData = [];

        foreach ($properties as $property) {
            $tag = '';
            $value = '';
            foreach ($property->childNodes as $key=>$childNode) {
                if ($key === 0) {
                    $tag = trim($childNode->textContent);
                    continue;
                }
                if ($key === 2) {
                    $value = preg_replace('/\s\s+/', '\n', $childNode->textContent);
                    break;
                }
            }
            $propertiesData[$tag] = $value;
        }

        return $propertiesData;
    }
}
