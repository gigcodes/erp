<?php

namespace App\Services\Scrap;

use App\ScrapEntries;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class GebnegozionlineScraper extends Scraper
{
    private const URL = [
        'homepage' => 'https://www.gebnegozionline.com/it_it/',
        'designers' => 'https://www.gebnegozionline.com/it_it/women/designers'
    ];


    public function scrap(): void
    {
        $this->scrapPage(self::URL['homepage'], false);
    }

    private function scrapPage($url, $hasProduct=true): void
    {
        $scrapEntry = ScrapEntries::where('url', $url)->first();
        if (!$scrapEntry) {
            $scrapEntry = new ScrapEntries();
            $scrapEntry->title = $url;
            $scrapEntry->url = $url;
            $scrapEntry->save();
        }

        if ($hasProduct) {
            $this->getProducts($scrapEntry);
            return;
        }

        $body = $this->getContent($url);
        $c = new HtmlPageCrawler($body);
        $links = $c->filter('.hover-landing-column-desc')->filter('ul li a')->getIterator();

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

        $products = $c->filter('.product-item')->getIterator();

        foreach ($products as $product) {
            $images = $this->getImagesFromProduct($product);
            $title = $this->getTitleFromProduct($product);
            $link = $this->getLinkFromProduct($product);

            if (!$title || !$link || !$images) {
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

    private function getImagesFromProduct($product): array
    {
        $items = $product->getElementsByTagName('img');
        $images = [];

        for ($i=0; $i<$items->length; $i++) {
            $images[] = $items->item($i)->getAttribute('src');
        }

        return $images;
    }

    private function getTitleFromProduct($product) {
        try {
            $description = preg_replace('/\s\s+/', '', $product->getElementsByTagName('p')->item(0)->firstChild->data);
        } catch (\Exception $exception) {
            $description = '';
        }

        return $description;

    }

    private function getLinkFromProduct($product)
    {
        try {
            $item = $product->childNodes->item(1)->getElementsByTagName('a')->item(0);
            $link = $item->getAttribute('href');
        } catch (\Exception $exception) {
            $link = '';
        }

        $link = str_replace('en_wr', 'it_it', $link);
        $link = str_replace('en_us', 'it_it', $link);

        return $link;
    }

    private function getPaginationData( HtmlPageCrawler $c): array
    {
        $maxPageNumber = 1;
        $paginationOptions = $c->filter('.pages-items li a')->getIterator();
        foreach ($paginationOptions as $paginationOption) {
            $text = $paginationOption->textContent;
            $text = str_replace('Page', '', $text);
            $text = preg_replace('/\s\s+/', '', $text);
            if ($text > $maxPageNumber && ctype_digit($text)) {
                $maxPageNumber = $text;
            }
        }

        $options = [
            'current_page_number' => 1,
            'total_pages' => $maxPageNumber
        ];

        return $options;


    }
}