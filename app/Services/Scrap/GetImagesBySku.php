<?php

namespace App\Services\Scrap;

use App\Brand;
use App\ScrapedProducts;
use App\ScrapEntries;
use App\Product;
use App\Setting;
use Storage;
use Validator;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class GetImagesBySku extends Scraper
{

    private $supportedBrands = [
        'Yves Saint Laurent' => 'https://www.ysl.com/Search/Index?siteCode=SAINTLAURENT_IT&season=A,P,E&department=llmnwmn&gender=D,U,E&emptySearchResult=true&textsearch={QUERY}',
        'valentino' => '',
        'prada' => 'https://store.prada.com/SearchDisplay?searchTerm={QUERY}&q={QUERY}&storeId=32851',
        'fendi' => 'https://www.fendi.com/it/search-results?async=true&q={QUERY}',
        'stella mcartny' => '',
        'kenzo' => '',
        'farfetch' => 'https://www.farfetch.com/it/shopping/tops-1/items.aspx?q={QUERY}'
    ];

    public function scrap()
    {
        $products = Product::where('brand', 11)->get();

        foreach ($products as $product) {
            if ($product->hasMedia(config('constants.media_tags'))) {
                continue;
            }

            $brand = $product->brands;

            if (!$brand) {
                continue;
            }


            $brand = strtolower($brand->name);

            if (array_key_exists($brand, $this->supportedBrands)) {
                $this->getProductDetails($product, $brand);
            }

        }
    }


    /**
     * @param ScrapEntries $scrapEntry
     * @throws \Exception
     */
    private function getProductDetails($product, $brand): void
    {


        $sku = $product->sku;
        if (!$sku) {
            return;
        }

        $sku = str_replace(' ', '', $sku);
        $url = str_replace('{QUERY}', $sku, $this->supportedBrands[$brand]);

        $content = $this->getContent($url);
        if ($content === '') {
            return;
        }

        $c = new HtmlPageCrawler($content);
        $images = [];

        switch (strtolower($brand)) {
            case 'yves saint laurent':
//                $product = $c->filter('article')->getIterator();
                break;
            case 'prada':
            case 'fendi':
                $productUrl = '';
                $productBox = $c->filter('div.products div.product-card-mini a');
                if (count($productBox)) {
                    $productUrl = $productBox->getAttribute('href');
                }

                if (!$productUrl) {
                    break;
                }

                $productUrl = 'https://fendi.com' . $productUrl;
                $productContent = $this->getContent($productUrl);
                if ($productContent === '') {
                    return;
                }

                $c2 = new HtmlPageCrawler($productContent);
                $images = $this->getImagesForFendi($c2);

            foreach ($images as $image_name) {
                $path = public_path('uploads') . '/social-media/' . $image_name;
                $media = MediaUploader::fromSource($path)->upload();
                $product->attachMedia($media,config('constants.media_tags'));
            }

            case 'farfetch':
            default:
                break;

        }

    }

    private function getImagesForFendi(HtmlPageCrawler $c): array
    {
        $images = $c->filter('div.inner a img')->getIterator();
        $content = [];

        foreach ($images as $image) {
            if (trim($image->getAttribute('data-zoom-img'))) {
                $content[] = trim($image->getAttribute('data-zoom-img'));
            }
        }

        return $this->downloadImages($content, 'fendi');
    }

    private function getImagesForPrada(HtmlPageCrawler $c) {
        $images = $c->filter('div.thumbnail-list__item img')->getIterator();


        foreach ($images as $image) {
            $content[] = trim($image->getAttribute('src'));
        }

        dd($content);

        return $this->downloadImages($content, 'tory');
    }

    private function getImagesForYSL(HtmlPageCrawler $c) {
        $images = $c->filter('div.thumbnail-list__item img')->getIterator();
        $content = [];

        foreach ($images as $image) {
            $content[] = trim($image->getAttribute('src'));
        }

        return $this->downloadImages($content, 'tory');
    }

    private function downloadImages($data, $prefix = 'img'): array
    {
        $images = [];
        foreach ($data as $key=>$datum) {
            try {
                $datum = $this->getImageUrl($datum);
                $imgData = file_get_contents($datum);
            } catch (\Exception $exception) {
                continue;
            }

            $fileName = $prefix . '_' . md5(time()).'.png';
            Storage::disk('uploads')->put('social-media/'.$fileName, $imgData);

            $images[] = $fileName;
        }
        return $images;
    }
    private function getImageUrl($url)
    {
        return $url;
    }
}
