<?php

namespace App\Services\Scrap;

use App\Brand;
use App\Image;
use App\ScrapEntries;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class GebnegozionlineProductDetailsScraper extends Scraper
{


    public function scrap()
    {
        $products = ScrapEntries::where('is_scraped', 0)->where('is_product_page', 1)->take(25)->get();

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
        $images = $this->getImages($c);
        $description = $this->getDescription($c);

        if (!$images) {
            $scrapEntry->delete();
        }

        $brandId = $this->getBrandId($brand);

        $image = new Image();
        $image->brand = $brandId;
        $image->title = $title;
        $image->description = $description;
        $image->filename = 'default.png';
        $image->save();

        $scrapEntry->is_scraped = 1;
        $scrapEntry->save();

    }

    private function getTitle(HtmlPageCrawler $c) {
        try {
            $title = preg_replace('/\s\s+/', '', $c->filter('.product-title-name div.value p.title')->getInnerHtml());
        } catch (\Exception $exception) {
            $title = '';
        }
        return $title;
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

        try {
            $image = $content[0]['full'];
        } catch (\Exception $exception) {
            $image = '';
        }

        return $image;
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
}