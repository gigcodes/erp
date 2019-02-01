<?php

namespace App\Services\Scrap;

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
        $images = $this->getImages($c);
        $description = $this->getDescription($c);

        if (!$images) {
            $scrapEntry->delete();
        }





    }

    private function getTitle(HtmlPageCrawler $c) {
        return '';
    }

    private function getDescription(HtmlPageCrawler $c) {
        return '';
    }

    private function getImages(HtmlPageCrawler $c) {
        return '';
    }
}