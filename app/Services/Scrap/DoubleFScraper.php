<?php

namespace App\Services\Scrap;

use App\ScrapEntries;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class DoubleFScraper extends Scraper
{
    private const URL = [
        'woman' => 'https://www.thedoublef.com/it_it/donna/designer/',
        'man' => 'https://www.thedoublef.com/it_it/uomo/designer/'
    ];


    public function scrap($key): void
    {
        $this->scrapPage(self::URL[$key], false);
    }

    private function scrapPage($url, $hasProduct=true): void
    {
        $scrapEntry = ScrapEntries::where('url', $url)->first();
        if (!$scrapEntry) {
            $scrapEntry = new ScrapEntries();
            $scrapEntry->title = $url;
            $scrapEntry->site_name = 'DoubleF';
            $scrapEntry->url = $url;
            $scrapEntry->save();
        }

        if ($scrapEntry->is_scraped) {
            return;
        }

        if ($hasProduct) {
            $this->getProducts($scrapEntry);
            return;
        }

        $body = $this->getContent($url);
        $c = new HtmlPageCrawler($body);
        $links = $c->filter('div.designers-list')->filter('ul li a')->getIterator();

        $urls = [];

        foreach ($links as $key=>$link) {
            $text = $link->firstChild->data;
            $text = trim(preg_replace('/\s\s+/', '', $text));
            $text = str_replace(' ', '-', strtolower($text));
            if ($text === '' || $text === 'designers') {
                continue;
            }
            $urls[$text.'_'.$key] = $link->getAttribute('href');
        }

        foreach ($urls as $itemUrl) {
            $this->scrapPage($itemUrl);
        }

    }

    private function getProducts(ScrapEntries $scrapEntriy ): void
    {
        $paginationData = $scrapEntriy->pagination;
        if (!$paginationData)
        {
            $body = $this->getContent($scrapEntriy->url);
            $c = new HtmlPageCrawler($body);
            $scrapEntriy->pagination =  $this->getPaginationData($c);
            $scrapEntriy->save();
        }

        $pageNumber = $scrapEntriy->pagination['current_page_number'];
        $totalPageNumber = $scrapEntriy->pagination['total_pages'];

        if ($pageNumber < $totalPageNumber) {
            $pageNumber++;
        }

        $body = $this->getContent($scrapEntriy->url . '?p=' . $pageNumber);
        $c = new HtmlPageCrawler($body);

        $products = $c->filter('.products-grid div.box');
        foreach ($products as $product) {
            $title = $this->getTitleFromProduct($product);
            $link = $this->getLinkFromProduct($product);

            if (!$title || !$link) {
                continue;
            }

            $entry = ScrapEntries::where('title', $title)
                ->orWhere('url', $link)
                ->first()
            ;

            if ($entry) {
                continue;
            }

            $entry = new ScrapEntries();
            $entry->title = $title;
            $entry->url = $link;
            $entry->site_name = 'DoubleF';
            $entry->is_product_page = 1;
            $entry->save();

        }

        if ($pageNumber >= $totalPageNumber) {
            $scrapEntriy->pagination = [
                'current_page_number' => $totalPageNumber,
                'total_pages' => $totalPageNumber
            ];
            $scrapEntriy->is_scraped = 1;
            $scrapEntriy->save();
        }

    }

    private function getTitleFromProduct($product) {
        try {
            $description = preg_replace('/\s\s+/', '', $product->getElementsByTagName('h4')->item(0)->textContent);
        } catch (\Exception $exception) {
            $description = '';
        }

        return $description;
    }

    private function getLinkFromProduct($product)
    {
        try {
            $link = $product->getElementsByTagName('a')->item(0)->getAttribute('href');
        } catch (\Exception $exception) {
            $link = '';
        }

        return $link;
    }

    private function getPaginationData( HtmlPageCrawler $c): array
    {
        $maxPageNumber = 1;
        $options = [
            'current_page_number' => 1,
            'total_pages' => $maxPageNumber
        ];

        $text = $c->filter('div.pages ol li.current span')->getInnerHtml();
        $text = preg_replace('/\s\s+/', '', $text);
        if (strlen($text) < 5) {
            return $options;
        }

        $text = explode(' ', $text);
        $maxPageNumber = $text[count($text)-1];

        $options = [
            'current_page_number' => 1,
            'total_pages' => $maxPageNumber
        ];

        return $options;
    }
}