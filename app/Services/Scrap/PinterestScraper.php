<?php

namespace App\Services\Scrap;

use Wa72\HtmlPageDom\HtmlPageCrawler;

class PinterestScraper extends Scraper
{
    private const URL = [
        'https://www.pinterest.com/search/pins/?q={query_string}&rs=rs&eq='
    ];


    public function scrap($q, $outputCount): array
    {
        return [];

//        $query = str_replace('{query_string}', $q, self::URL[0]);
//
//        $body = $this->getContent($query);
//        $c = new HtmlPageCrawler($body);
//
//        $c = $c->filter('body')->filter('div.');

    }
}