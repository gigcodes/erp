<?php

namespace App\Services\Scrap;

use App\ScrapEntries;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class WiseBoutiqueScraper extends Scraper
{
    private $scrapKey  = '';
    private const URL = [
        'man' => 'https://www.wiseboutique.com/en/man',
        'woman' => 'https://www.wiseboutique.com/en/woman',
        'HOMEPAGE' => 'https://www.wiseboutique.com/en',
    ];

    private $paginationData = [
        'man' => 31,
        'woman' => 54
    ];


    public function scrap($key): void
    {
        $this->scrapKey = $key;

        $this->scrapPage(self::URL[$key]);
    }

    private function scrapPage($url, $hasProduct=true): void
    {
        $scrapEntry = ScrapEntries::where('url', $url)->first();
        if (!$scrapEntry) {
            $scrapEntry = new ScrapEntries();
            $scrapEntry->title = $url;
            $scrapEntry->url = $url;
            $scrapEntry->site_name = 'Wiseboutique';
            $scrapEntry->save();
        }

        if ($scrapEntry->is_scraped) {
            return;
        }

        if ($hasProduct) {
            $this->getProducts($scrapEntry);
            return;
        }
    }

    private function getProducts(ScrapEntries $scrapEntry ): void
    {

        $paginationData = $scrapEntry->pagination;
        if (!$paginationData)
        {
            $scrapEntry->pagination =  $this->getPaginationData(1);
            $scrapEntry->save();
        }

        $pageNumber = $scrapEntry->pagination['current_page_number'];
        $totalPageNumber = $this->paginationData[$this->scrapKey];

        if ($pageNumber > $totalPageNumber) {
            $scrapEntry->pagination = $this->getPaginationData();
            $scrapEntry->save();
        }

        $body = $this->getContent($scrapEntry->url . '?page=' . $pageNumber);
        $c = new HtmlPageCrawler($body);

        $products = $c->filter('.contfoto .cotienifoto a:first-child')->getIterator();

        foreach ($products as $product) {
            $title = $product->getAttribute('title');
            $link = self::URL['HOMEPAGE'] . '/' . $product->getAttribute('href');

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
            $entry->is_product_page = 1;
            $entry->site_name = 'Wiseboutique';
            $entry->save();
        }


        if ($pageNumber <= $totalPageNumber) {
            ++$pageNumber;
            $scrapEntry->pagination = $this->getPaginationData($pageNumber);
            $scrapEntry->save();
        }
    }

    private function getPaginationData($default = 1): array
    {
        $options = [
            'current_page_number' => $default
        ];

        return $options;
    }
}
