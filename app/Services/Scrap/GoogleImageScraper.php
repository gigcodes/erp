<?php

namespace App\Services\Scrap;

use Wa72\HtmlPageDom\HtmlPageCrawler;

class GoogleImageScraper extends Scraper
{
    private const GOOGLE_IMAGE_SEARCH_URL = [
        'https://www.google.com/search?tbm=isch&source=lnms&q=',
        'https://www.google.com/search?q=nike&source=lnms&tbm=isch&sa=X#imgrc='
    ];


    public function scrapGoogleImages($q, $outputCount): array
    {
        $body = $this->getContent(self::GOOGLE_IMAGE_SEARCH_URL[0].$q);
        $c = new HtmlPageCrawler($body);
        $imageJson = $c->filter('body')->filter('div.rg_meta');

        $images = [];


        foreach ($imageJson as $key => $image) {
            $item = json_decode($image->firstChild->data, true);

            $images[] = $item['ou'];

            if ($key+1>=$outputCount) {
                break;
            }
        }

        return $images;
    }
}