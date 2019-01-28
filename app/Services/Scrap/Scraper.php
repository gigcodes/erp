<?php

namespace App\Services\Scrap;

use GuzzleHttp\Client;
use App\Image;
use Illuminate\View\View;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class Scraper
{
    private $client;
    private const GOOGLE_IMAGE_SEARCH_URL = [
        'https://www.google.com/search?tbm=isch&source=lnms&q=',
        'https://www.google.com/search?q=nike&source=lnms&tbm=isch&sa=X#imgrc='
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function scrapGoogleImages($q)
    {
        $body = $this->getContent(self::GOOGLE_IMAGE_SEARCH_URL[0].$q);
        $c = new HtmlPageCrawler($body);
        $imageJson = $c->filter('body')->filter('div.rg_meta');

        $images = [];


        foreach ($imageJson as $key => $image) {
            $item = json_decode($image->firstChild->data, true);
            $images[] = $item['ou'];

            if ($key>4) {
                break;
            }
        }

        return $images;
    }

    private function getContent($url, $method = 'GET')
    {
        try {
            $response = $this->client->request($method, $url, [
                'headers'=>[
                    'User-Agent' => 'User-Agent\':"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36',
                ]
            ]);
            $content = $response->getBody()->getContents();
        } catch (\Exception $exception) {
            $content = '';
        }

        return $content;
    }
}