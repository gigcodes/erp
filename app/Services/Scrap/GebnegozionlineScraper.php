<?php

namespace App\Services\Scrap;

use Wa72\HtmlPageDom\HtmlPageCrawler;

class GebnegozionlineScraper extends Scraper implements ScrapInterface
{
    private const URL = [
        'homepage' => 'https://www.gebnegozionline.com/',
        ''
    ];


    public function scrap()
    {
        $this->scrapPage(self::URL['homepage']);
    }

    private function scrapPage($url) {
        $body = $this->getContent($url);
        $c = new HtmlPageCrawler($body);
    }

    private function getProducts() {

    }
}