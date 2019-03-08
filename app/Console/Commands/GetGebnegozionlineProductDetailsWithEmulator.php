<?php

namespace App\Console\Commands;

use App\ScrapedProducts;
use App\ScrapEntries;
use App\Services\Bots\WebsiteEmulator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;


class GetGebnegozionlineProductDetailsWithEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:traffic-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $country;
    protected $IP;

    public function handle(): void
    {
        $products = ScrapEntries::where('is_scraped', 0)->where('is_product_page', 1)->where('site_name', 'GNB')->take(250)->get();
        foreach ($products as $product) {
            $this->runFakeTraffic($product->url);
        }
    }


    private function runFakeTraffic($url): void
    {
        $url = explode('/category', $url);
        $url = $url[0];
        $this->info($url);
        $duskShell = new WebsiteEmulator();
//        $duskShell->setProxyList();
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (\Exception $exception) {
            $content = ['', ''];
        }

        if ($content === ['', '']) {
            return;
        }

        $image = ScrapedProducts::where('sku', $content[1])->first();
        $image->price = $content[0];
        $image->save();
        if (!$image) {
            return;
        }

        if ($image->is_updated_on_server == 1) {
            return;
        }

        $this->updateProductOnServer($image);


    }

    private function setCountry(): void
    {

        $this->country = 'IT';
    }

    private function updateProductOnServer(ScrapedProducts $image)
    {

        $this->info('here saving to server');
        $client = new Client();
        $response = $client->request('POST', 'http://erp.sololuxury.co.in/api/sync-product', [
//        $response = $client->request('POST', 'https://erp.sololuxury.co.in/api/sync-product', [
            'form_params' => [
                'sku' => $image->sku,
                'website' => $image->website,
                'has_sku' => $image->has_sku,
                'title' => $image->title,
                'brand_id' => $image->brand_id,
                'description' => $image->description,
//                'images' => $this->imagesToDownload,
                'price' => $image->price,
                'properties' => $image->properties,
                'url' => $image->url,
                'is_property_updated' => 0,
                'is_price_updated' => 1,
                'is_enriched' => 0,
                'can_be_deleted' => 0
            ]
        ]);

        if (!$response) {
            dd($response->getBody()->getContents());
        }

        $image->is_updated_on_server = 1;
        $image->save();
    }

    private function setIP(): void
    {
        $this->IP = '5.61.4.70	' . ':' . '8080';
    }
}