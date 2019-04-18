<?php

namespace App\Services\Scrap;

use App\ScrapCounts;
use App\ScrapEntries;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class ToryScraper extends Scraper
{
    private const URL = [
        'clothing' => 'https://www.toryburch.it/abbigliamento/visualizza-tutto/?sz=8000&start=1',
        'shoes' => 'https://www.toryburch.it/scarpe/visualizza-tutto/?sz=8000&start=1',
        'bags' => 'https://www.toryburch.it/borse/visualizza-tutto/?sz=8000&start=1',
    ];


    public function scrap($key): void
    {
        $this->scrapPage(self::URL[$key]);
    }

    private function scrapPage($url, $hasProduct=true): void
    {
        $scrapEntry = ScrapEntries::where('url', $url)->first();
        if (!$scrapEntry) {
            $scrapEntry = new ScrapEntries();
            $scrapEntry->title = $url;
            $scrapEntry->site_name = 'Tory';
            $scrapEntry->url = $url;
            $scrapEntry->save();
        }

        if ($hasProduct) {
            $this->getProducts($scrapEntry);
        }

    }

    private function getProducts(ScrapEntries $scrapEntriy ): void
    {

        $date = date('Y-m-d');
        $allLinks = ScrapCounts::where('scraped_date', $date)->where('website', 'Tory')->first();
        if (!$allLinks) {
            $allLinks = new ScrapCounts();
            $allLinks->scraped_date = $date;
            $allLinks->website = 'Tory';
            $allLinks->save();
        }
        $body = $this->getContent($scrapEntriy->url);
        $c = new HtmlPageCrawler($body);

        $products = $c->filter('a.product-tile__name');
        foreach ($products as $product) {
            $allLinks->link_count = $allLinks->link_count + 1;
            $allLinks->save();
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
            $entry->site_name = 'Tory';
            $entry->is_product_page = 1;
            $entry->save();

        }

    }

    private function getTitleFromProduct($product) {
        try {
            $description = preg_replace('/\s\s+/', '', $product->getAttribute('title'));
        } catch (\Exception $exception) {
            $description = '';
        }

        return $description;
    }

    private function getLinkFromProduct($product)
    {
        try {
            $link = preg_replace('/\s\s+/', '', $product->getAttribute('href'));
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