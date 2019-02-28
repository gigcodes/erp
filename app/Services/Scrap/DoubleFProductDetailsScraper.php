<?php

namespace App\Services\Scrap;

use App\Brand;
use App\ScrapedProducts;
use App\ScrapEntries;
use App\Product;
use App\Setting;
use Storage;
use Validator;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class DoubleFProductDetailsScraper extends Scraper
{

    public function scrap()
    {
        $products = ScrapEntries::where('is_scraped', 0)->where('is_product_page', 1)->where('site_name', 'DoubleF')->take(25)->get();

        foreach ($products as $product) {
            $this->getProductDetails($product);
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

        if (!$brandId) {
            $scrapEntry->delete();
            return;
        }

        $image = ScrapedProducts::where('sku', $sku)->orWhere('url', $scrapEntry->url)->first();
        if ($image) {
            $scrapEntry->is_scraped = 1;
            $scrapEntry->save();
            return;
        }

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
        $image->is_price_updated = 1;
        $image->url = $scrapEntry->url;
        $image->properties = $properties;
        $image->save();


        $scrapEntry->is_scraped = 1;
        $scrapEntry->save();
    }

    private function getTitle(HtmlPageCrawler $c) {
        try {
            $title = preg_replace('/\s\s+/', '', $c->filter('h1 div.name')->getInnerHtml());
        } catch (\Exception $exception) {
            $title = '';
        }
        return $title;
    }

    private function getPrice(HtmlPageCrawler $c) {
        try {
            $price = preg_replace('/\s\s+/', '', $c->filter('div.price-box span.price')->getInnerHtml());
        } catch (\Exception $exception) {
            $price = 'N/A';
        }

        $price = str_replace('&nbsp;', '', $price);
        $price = str_replace('&euro;', '€', $price);

        return $price;
    }

    private function getSku(HtmlPageCrawler $c) {
        try {
            $sku = preg_replace('/\s\s+/', '', $c->filter('div#tab1 .panel-body ul')->getIterator());
            dd($sku);
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
        $images = $c->filter('div.product-img-box a')->getIterator();
        $content = [];

        foreach ($images as $image) {
            $content[] = trim($image->getAttribute('href'));
        }


        return $this->downloadImages($content, 'doublef');
    }

    private function getDesignerName(HtmlPageCrawler $c)
    {
        try {
            $title = preg_replace('/\s\s+/', '', $c->filter('div.product-name h2 strong a')->getInnerHtml());
        } catch (\Exception $exception) {
            $title = '';
        }
        return $title;
    }

    private function getBrandId($brandName)
    {
        $brand = Brand::where('name', $brandName)->first();

        if (!$brand) {
            return false;
        }


        return $brand->id;
    }

    private function downloadImages($data, $prefix = 'img'): array
    {
        $images = [];
        foreach ($data as $key=>$datum) {
            try {
                $datum = $this->getImageUrl($datum);
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

    private function getImageUrl($url)
    {
        $content = $this->getContent($url);
        if ($content === '') {
            return '';
        }

        $c = new HtmlPageCrawler($content);

        $imageUrl = $c->filter('img')->attr('src');
        return $imageUrl;
    }
}
